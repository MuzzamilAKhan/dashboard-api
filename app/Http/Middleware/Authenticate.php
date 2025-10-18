<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authenticate extends Middleware
{
    /**
     * Handle unauthenticated user.
     */
    protected function redirectTo(Request $request): ?string
    {
        // 👇 For API requests — never redirect, just return JSON
        if ($request->expectsJson()) {
            abort(response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please login again.'
            ], Response::HTTP_UNAUTHORIZED));
        }

        // 👇 For web requests — you can keep this fallback if needed
        return route('login');
    }
}
