<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class ApprovedUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        if (!Auth::user()->is_active) {
            Auth::logout();
            abort(403, 'Account not approved yet.');
        }

        return $next($request);
    }
}
