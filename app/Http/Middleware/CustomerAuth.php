<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!session()->has('customer.uid')) {
            $request->session()->flash('auth_notice', 'Please log in to continue checkout.');
            return redirect()->guest(route('login'));
        }

        return $next($request);
    }
}
