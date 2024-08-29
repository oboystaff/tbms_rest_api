<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Laravel\Sanctum\PersonalAccessToken;


class CheckTokenExpiry
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if ($token) {
            $tokenModel = PersonalAccessToken::where('token', hash('sha256', $token))->first();

            if ($tokenModel && $tokenModel->expires_at && $tokenModel->expires_at < Carbon::now()) {
                return response()->json(['message' => 'Token has expired.'], 401);
            }
        }

        return $next($request);
    }
}
