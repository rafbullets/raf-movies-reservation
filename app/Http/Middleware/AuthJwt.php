<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\App;

class AuthJwt
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $key = "my_secret_key";

        $jwtToken = $request->bearerToken();

        if(is_null($jwtToken)) {
            return response()->json(['error' => 'Error fetching token'], 500);
        }

        $decoded = JWT::decode($jwtToken, $key, ['HS256']);

        if(!isset($decoded->exp) || $decoded->exp < Carbon::now()->timestamp) {
            return response()->json(['error' => 'Token expired'], 401);
        }

        if (!isset($decoded->id)) {
            return response()->json(['error' => 'Missing parameter id in JWT.'], 500);
        }
        $request->setUserResolver(function () use ($decoded, $jwtToken) {

            return [
                'id' => $decoded,
                'jwt' => $jwtToken
            ];
        });

        return $next($request);
    }
}
