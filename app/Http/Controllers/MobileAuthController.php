<?php

namespace App\Http\Controllers;

use App\Services\FirebaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Auth\SignIn\FailedToSignIn;
use Kreait\Firebase\Exception\Auth\EmailExists;
use Kreait\Firebase\Exception\Auth\EmailNotFound;
use Kreait\Firebase\Exception\Auth\InvalidPassword;
use Kreait\Firebase\Exception\Auth\UserNotFound;
use Throwable;

class MobileAuthController extends Controller
{
    public function __construct(private FirebaseService $firebase)
    {
    }

    public function register(Request $request): JsonResponse
    {
        $startedAt = microtime(true);

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $validator->validated();

        $auth = $this->firebase->auth();

        try {
            $user = $auth->createUser([
                'email' => $data['email'],
                'password' => $data['password'],
                'displayName' => $data['name'],
            ]);
        } catch (EmailExists) {
            return response()->json([
                'ok' => false,
                'message' => 'Validation failed.',
                'errors' => [
                    'email' => ['Email already registered.'],
                ],
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        } catch (Throwable $e) {
            Log::error('mobile.auth.register.create_user_failed', [
                'email' => $data['email'],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Server error. Please try again.',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        $uid = $user->uid;
        $record = [
            'uid' => $uid,
            'name' => $data['name'],
            'email' => $data['email'],
            'created_at' => now()->toIso8601String(),
        ];

        try {
            $this->firebase->getRef("customers/{$uid}")->set($record);
        } catch (Throwable $e) {
            Log::error('mobile.auth.register.profile_write_failed', [
                'uid' => $uid,
                'error' => $e->getMessage(),
            ]);
        }

        try {
            $signIn = $auth->signInWithEmailAndPassword($data['email'], $data['password']);
            $token = $signIn->idToken();
        } catch (Throwable $e) {
            Log::error('mobile.auth.register.signin_failed', [
                'uid' => $uid,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Server error. Please try again.',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        if (!$token) {
            Log::error('mobile.auth.register.token_missing', [
                'uid' => $uid,
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Server error. Please try again.',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        Log::info('mobile.auth.register', [
            'uid' => $uid,
            'duration_ms' => (int) ((microtime(true) - $startedAt) * 1000),
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'registered',
            'token' => $token,
            'user' => [
                'id' => $uid,
                'name' => $record['name'],
                'email' => $record['email'],
            ],
        ], JsonResponse::HTTP_CREATED);
    }

    public function login(Request $request): JsonResponse
    {
        $startedAt = microtime(true);

        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $validator->validated();
        $auth = $this->firebase->auth();

        try {
            $signIn = $auth->signInWithEmailAndPassword($data['email'], $data['password']);
        } catch (InvalidPassword|UserNotFound|EmailNotFound|FailedToSignIn) {
            return response()->json([
                'ok' => false,
                'message' => 'invalid credentials',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        } catch (Throwable $e) {
            Log::error('mobile.auth.login.signin_failed', [
                'email' => $data['email'],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Server error. Please try again.',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        $uid = $signIn->firebaseUserId();
        $token = $signIn->idToken();
        if (!$token) {
            Log::error('mobile.auth.login.token_missing', [
                'uid' => $uid,
            ]);

            return response()->json([
                'ok' => false,
                'message' => 'Server error. Please try again.',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        $profile = null;

        if ($uid) {
            try {
                $profile = $this->firebase->getRef("customers/{$uid}")->getValue();
            } catch (Throwable $e) {
                Log::error('mobile.auth.login.profile_read_failed', [
                    'uid' => $uid,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $name = $profile['name'] ?? $profile['full_name'] ?? null;
        $email = $profile['email'] ?? $data['email'];

        if (!$name && $uid) {
            try {
                $userRecord = $auth->getUser($uid);
                $name = $userRecord->displayName ?: null;
                $email = $userRecord->email ?: $email;
            } catch (Throwable $e) {
                Log::error('mobile.auth.login.user_record_failed', [
                    'uid' => $uid,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('mobile.auth.login', [
            'uid' => $uid,
            'duration_ms' => (int) ((microtime(true) - $startedAt) * 1000),
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'logged in',
            'token' => $token,
            'user' => [
                'id' => $uid,
                'name' => $name,
                'email' => $email,
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $authHeader = $request->header('Authorization', '');
        $token = str_starts_with($authHeader, 'Bearer ')
            ? substr($authHeader, 7)
            : null;

        if (!$token) {
            return response()->json([
                'ok' => false,
                'message' => 'Unauthorized.',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        try {
            $verifiedToken = $this->firebase->auth()->verifyIdToken($token);
            $uid = $verifiedToken->claims()->get('sub');
        } catch (Throwable) {
            return response()->json([
                'ok' => false,
                'message' => 'Unauthorized.',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $profile = null;
        try {
            $profile = $this->firebase->getRef("customers/{$uid}")->getValue();
        } catch (Throwable $e) {
            Log::error('mobile.auth.me.profile_read_failed', [
                'uid' => $uid,
                'error' => $e->getMessage(),
            ]);
        }

        $name = $profile['name'] ?? $profile['full_name'] ?? null;
        $email = $profile['email'] ?? null;

        if (!$name || !$email) {
            try {
                $userRecord = $this->firebase->auth()->getUser($uid);
                $name = $name ?: $userRecord->displayName;
                $email = $email ?: $userRecord->email;
            } catch (Throwable $e) {
                Log::error('mobile.auth.me.user_record_failed', [
                    'uid' => $uid,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'ok' => true,
            'user' => [
                'uid' => $uid,
                'name' => $name,
                'email' => $email,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $authHeader = $request->header('Authorization', '');
        $token = str_starts_with($authHeader, 'Bearer ')
            ? substr($authHeader, 7)
            : null;

        if ($token) {
            try {
                $verifiedToken = $this->firebase->auth()->verifyIdToken($token);
                $uid = $verifiedToken->claims()->get('sub');

                if ($uid) {
                    $this->firebase->auth()->revokeRefreshTokens($uid);
                }
            } catch (Throwable $e) {
                Log::warning('mobile.auth.logout.failed', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'logged out',
        ]);
    }
}
