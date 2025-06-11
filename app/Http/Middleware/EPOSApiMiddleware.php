<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Sanctum\PersonalAccessToken;

class EPOSApiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah ada token Bearer
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak ditemukan. Gunakan Bearer token untuk akses API.',
                'data' => null
            ], 401);
        }

        // Validasi token
        $personalAccessToken = PersonalAccessToken::findToken($token);
        
        if (!$personalAccessToken) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak valid atau sudah expired.',
                'data' => null
            ], 401);
        }

        // Cek apakah token memiliki ability untuk akses ePOS
        if (!$personalAccessToken->can('epos:access')) {
            return response()->json([
                'success' => false,
                'message' => 'Token tidak memiliki permission untuk akses ePOS API.',
                'data' => null
            ], 403);
        }

        // Update last_used_at
        $personalAccessToken->forceFill(['last_used_at' => now()])->save();

        // Set user ke request
        $request->setUserResolver(function () use ($personalAccessToken) {
            return $personalAccessToken->tokenable;
        });

        return $next($request);
    }
}
