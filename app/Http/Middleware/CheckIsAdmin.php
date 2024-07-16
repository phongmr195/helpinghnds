<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckIsAdmin
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
        $user = $request->user();

        if($user->status && in_array($user->user_type, config('constant.user_admin'))){

            if(isSuperAdmin($user) && (!$user->hasRole('root') || !$user->hasRole('admin'))){
                $user->assignRole('root');
                $user->syncPermissions(['add', 'edit', 'delete']);
            }

            if(isAdmin($user) && !$user->hasRole('admin')){
                $user->assignRole('admin');
            }
            
            return $next($request);
        }

        Auth::logout();

        return redirect(route('login'))->with('alert_message', 'Only administrators can login to the dashboard');
        
    }
}
