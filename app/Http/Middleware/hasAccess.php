<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Models\UsersMenus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class hasAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = User::where('id', Auth::id())->first();
        $menus = UsersMenus::where('user_id', $user->id)->get();
        foreach($menus as $one_menu){
            if($one_menu->menu->url == '/'.Route::currentRouteName()){
                return $next($request);
            }
        }
        return redirect('/')->with('failure','Δεν έχετε δικαίωμα πρόσβασης σε αυτό τον πόρο');
    }
}
