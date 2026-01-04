<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->is('M_*')) {
                return response()->json([
                    'message' => 'Unauthorized.',
                ], 401);
            }
        });

        $this->renderable(function (Throwable $e, Request $request) {
            if (!$request->is('M_*')) {
                return null;
            }

            $status = $this->isHttpException($e) ? $e->getStatusCode() : 500;
            $message = match ($status) {
                404 => 'Not found.',
                403 => 'Forbidden.',
                default => 'Server error. Please try again.',
            };

            return response()->json([
                'message' => $message,
            ], $status);
        });
    }
}
