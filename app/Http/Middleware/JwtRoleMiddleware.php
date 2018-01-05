<?php

namespace App\Http\Middleware;

use Exception;
use Closure;
use App\Models\Web\UserPrg;
use JWTAuth;

class JwtRoleMiddleware
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
        try {
            $user = JWTAuth::toUser($request->input('token')); 
            if ($user) {
                $user_id = $user['id'];
                $sys_id = $user['sys'];
                $prg_id = session('program');
                $prg_role = session('prg_role');
                if  ($prg_role['prg_id'] !== $prg_id) {
                    $result = UserPrg::where('user_id', $user_id)->where('prg_id', $prg_id)->first();
                    if (!$result) {
                        $code = 401;
                        $error = 'permission_denied';
                        return response()->json(compact('error'), $code);
                    }  
                    session(['prg_role' => $result]);
                }
                return $next($request);
            }
            throw new Exception('could_not_found_user');
        } catch (Exception $e) {
            $code = 401;
            $error = $e->getMessage();
        }
        return response()->json(compact('error'), $code);
    }

    
}
