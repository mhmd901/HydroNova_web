<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        if ($request->is('checkout*')) {
            $request->session()->flash('auth_notice', 'Please log in to continue checkout.');
        } elseif ($request->is('cart*')) {
            $request->session()->flash('auth_notice', 'Please log in to view your cart.');
        }

        return route('login');
    }
}
