<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackLastLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            
            // Update last login time if it's been more than 5 minutes
            if (!$user->last_login_at || $user->last_login_at->diffInMinutes(now()) > 5) {
                $user->update(['last_login_at' => now()]);
            }
        }

        return $next($request);
    }
}
