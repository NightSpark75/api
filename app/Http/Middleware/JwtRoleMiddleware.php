<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Web\UserPrg;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
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
        $user = JWTAuth::toUser($request->input('token')); 
        if ($user) {
            $user_id = Auth::user()->id;
            $sys_id = session('system');
            $prg_id = session('program');
            $prg_role = session('prg_role');
            if  ($prg_role['prg_id'] !== $prg_id) {
                $result = UserPrg::where('user_id', $user_id)->where('prg_id', $prg_id)->first();
                if (!$result) {
                    return redirect('/error')->with('message', '你無權限進行此一操作');
                }  
                session(['prg_role' => $result]);
            }
            return $next($request);
        }
        return redirect('/error')->with('message', '你無權限進行此一操作');
    }

    
}
