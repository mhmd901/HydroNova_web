<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Services\Auth\WebAuthService;
use App\Services\MobileTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class MobileAuthController extends Controller
{
    public function __construct(
        private WebAuthService $authService,
        private MobileTokenService $tokens
    ) {
    }

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:2', 'max:80'],
            'email' => ['required', 'email', 'max:120'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $validator->validated();

        if ($this->authService->findCustomerByEmail($data['email'])) {
            return response()->json([
                'success' => false,
                'message' => 'Email already registered',
                'errors' => ['email' => ['Email already registered']],
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $customer = $this->authService->createCustomer(
                $data['name'],
                $data['email'],
                $data['password']
            );
        } catch (Throwable $e) {
            Log::error('mobile.auth.register_failed', [
                'email' => $data['email'],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error. Please try again.',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        $token = $this->tokens->createToken([
            'sub' => $customer['uid'],
            'email' => $customer['email'],
            'name' => $customer['full_name'] ?? $data['name'],
        ]);

        return response()->json([
            'success' => true,
            'access_token' => $token['token'],
            'token_type' => 'Bearer',
            'expires_in' => $token['expires_in'],
            'user' => [
                'id' => $customer['uid'],
                'name' => $customer['full_name'] ?? $data['name'],
                'email' => $customer['email'],
            ],
            'message' => 'Registered successfully',
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $validator->validated();
        $customer = $this->authService->findCustomerByEmail($data['email']);

        if (!$customer || !$this->authService->verifyPassword($customer, $data['password'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $token = $this->tokens->createToken([
            'sub' => $customer['uid'],
            'email' => $customer['email'],
            'name' => $customer['full_name'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'access_token' => $token['token'],
            'token_type' => 'Bearer',
            'expires_in' => $token['expires_in'],
            'user' => [
                'id' => $customer['uid'],
                'name' => $customer['full_name'] ?? null,
                'email' => $customer['email'],
            ],
            'message' => 'Logged in',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $customer = $this->authService->findCustomerByUid((string) $user->id);
        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $customer['uid'],
                'name' => $customer['full_name'] ?? null,
                'email' => $customer['email'] ?? null,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Logged out',
        ]);
    }
}
