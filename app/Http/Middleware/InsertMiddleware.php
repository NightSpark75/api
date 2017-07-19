<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class InsertMiddleware
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
        if (Auth::check() && 
            (session('program') === session('prg_role')['prg_id']) && 
            (session('prg_role')['prg_ins'] === 'Y')) {
                
            return $next($request);
        }
        return redirect('/error')->with('message', '你無權限進行此一操作');
    }
}
