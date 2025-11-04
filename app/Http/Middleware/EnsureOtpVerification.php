<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Laravel\Sanctum\PersonalAccessToken;

class EnsureOtpVerification
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifier si l'utilisateur est authentifié
        if (!$request->user()) {
            return response()->json(['message' => 'Non authentifié'], 401);
        }

        // Vérifier si le token est un token de vérification OTP
        $token = $request->user()->currentAccessToken();
        
        if (!$token || !in_array('verify-otp', $token->abilities)) {
            return response()->json(['message' => 'Accès non autorisé'], 403);
        }

        return $next($request);
    }
}
