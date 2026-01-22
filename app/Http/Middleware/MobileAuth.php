<?php

namespace App\Http\Middleware;

use App\Services\Auth\WebAuthService;
use App\Services\MobileTokenService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MobileAuth
{
    public function __construct(
        private MobileTokenService $tokens,
        private WebAuthService $authService
    ) {
    }

    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return $this->unauthenticated();
        }

        $payload = $this->tokens->verify($token);
        if (!$payload || empty($payload['sub'])) {
            return $this->unauthenticated();
        }

        $customer = $this->authService->findCustomerByUid((string) $payload['sub']);
        if (!$customer) {
            return $this->unauthenticated();
        }

        $user = (object) [
            'id' => $customer['uid'] ?? $payload['sub'],
            'name' => $customer['full_name'] ?? $customer['name'] ?? null,
            'email' => $customer['email'] ?? null,
        ];

        $request->setUserResolver(static fn () => $user);

        return $next($request);
    }

    protected function unauthenticated(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated',
        ], JsonResponse::HTTP_UNAUTHORIZED);
    }
}
