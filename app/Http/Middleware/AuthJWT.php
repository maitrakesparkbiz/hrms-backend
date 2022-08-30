<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\PayloadException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthJWT
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof TokenInvalidException) {
                return $this->tokenError('Invalid Token');
            } else if ($e instanceof TokenExpiredException) {
                // todo renew token
                return $this->tokenError('Token Expired');
            } else if ($e instanceof PayloadException) {
                return $this->tokenError('Token Invalid');
            } else if ($e instanceof JWTException) {
                return $this->tokenError('No Token Found');
            } else {
                return $this->tokenError($e);
            }
        }
        return $next($request);
    }

    private function tokenError($reason)
    {
        $response['response'] = 'token error';
        $response['data'] = ['message' => $reason];
        return response()->json($response, 401);
    }
}
