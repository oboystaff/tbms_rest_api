<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Sanctum\PersonalAccessToken;
use App\Action\Log\LogError;
use DateTime;
use DateInterval;

class TokenExpiryMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if (!$token) {
            return LogError::createLogError($request, 'Token not provided.');
        }

        $accessToken = PersonalAccessToken::findToken($token);

        if (!$accessToken) {
            return LogError::createLogError($request, 'Invalid token.');
        }

        $expirationTime = env('tokenExpirationTime');
        $createdAt = new DateTime($accessToken->created_at);

        // Add the expiration time to the creation time
        $interval = new DateInterval('PT' . (int)$expirationTime . 'M');
        $createdAt->add($interval);

        // Check if the current time is past the expiration time
        if ($createdAt < new DateTime()) {
            $accessToken->delete();
            return LogError::createLogError($request, 'Token has expired.');
        }

        return $next($request);
    }
}
