<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Services\Auth\WebAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class MobileProfileController extends Controller
{
    public function __construct(private WebAuthService $authService)
    {
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'min:2', 'max:80'],
            'email' => ['required', 'email'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $data = $validator->validated();
        $existing = $this->authService->findCustomerByEmail($data['email']);

        if ($existing && ($existing['uid'] ?? null) !== (string) $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => [
                    'email' => ['Email already in use'],
                ],
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $result = $this->authService->updateProfile((string) $user->id, $data['name'], $data['email']);
        } catch (Throwable $e) {
            Log::error('mobile.profile.update_failed', [
                'uid' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error. Please try again.',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        if (!$result['ok']) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => [
                    'email' => ['Email already in use'],
                ],
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $record = $result['record'];

        return response()->json([
            'success' => true,
            'message' => 'Profile updated',
            'user' => [
                'id' => $record['uid'],
                'name' => $record['full_name'] ?? $data['name'],
                'email' => $record['email'] ?? $data['email'],
            ],
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = $request->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $data = $validator->validated();
        $customer = $this->authService->findCustomerByUid((string) $user->id);

        if (!$customer || !$this->authService->verifyPassword($customer, $data['current_password'])) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        try {
            $this->authService->updatePassword((string) $user->id, $data['password']);
        } catch (Throwable $e) {
            Log::error('mobile.profile.change_password_failed', [
                'uid' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error. Please try again.',
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password updated',
        ]);
    }
}
