<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DevAuthHeaderLogger
{
    public function handle(Request $request, Closure $next)
    {
        if (app()->environment('local')) {
            $authHeader = $request->headers->get('Authorization')
                ?? $request->headers->get('authorization')
                ?? '';

            $preview = $authHeader !== '' ? substr($authHeader, 0, 18) : null;

            Log::info('dev.auth_header', [
                'path' => $request->path(),
                'has_authorization' => $authHeader !== '',
                'authorization_preview' => $preview,
                'header_keys' => array_keys($request->headers->all()),
            ]);
        }

        return $next($request);
    }
}
