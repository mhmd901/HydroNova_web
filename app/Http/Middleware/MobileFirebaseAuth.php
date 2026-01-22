<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class MobileFirebaseAuth
{
    public function handle(Request $request, Closure $next)
    {
        $authHeader = $request->headers->get('Authorization')
            ?? $request->headers->get('authorization')
            ?? '';

        $authHeader = trim($authHeader);
        if (!preg_match('/^Bearer\\s+(.+)$/i', $authHeader, $matches)) {
            return $this->unauthenticated();
        }

        $idToken = trim($matches[1] ?? '');
        if ($idToken === '') {
            return $this->unauthenticated();
        }

        $auth = app('firebase.auth');

        try {
            $verifiedIdToken = $auth->verifyIdToken($idToken);
            $uid = (string) $verifiedIdToken->claims()->get('sub');
            $claims = $verifiedIdToken->claims()->all();
        } catch (Throwable) {
            return $this->unauthenticated();
        }

        if ($uid === '') {
            return $this->unauthenticated();
        }

        $request->attributes->set('firebase_uid', $uid);
        $request->attributes->set('firebase_claims', $claims);

        return $next($request);
    }

    protected function unauthenticated(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated, please login again',
        ], JsonResponse::HTTP_UNAUTHORIZED);
    }
}
