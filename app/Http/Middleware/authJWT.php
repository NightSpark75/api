<?php

namespace App\Http\Middleware;
use Closure;
use JWTAuth;
use Exception;

class authJWT
{
    public function handle($request, Closure $next)
    {
        try {
            // 如果用户登陆后的所有请求没有jwt的token抛出异常
            $user = JWTAuth::toUser($request->input('token')); 
        } catch (Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                $code = 401;
                $error = 'Token invalid';
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                $code = 401;
                $error = 'Token Expired';
            } else {
                $code = 500;
                $error = $e->getMessage();
            }
            return response()->json(compact('error'), $code);
        }
        return $next($request);
    }
}