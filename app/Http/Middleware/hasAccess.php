<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Models\UsersOperations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Request as twin;

class hasAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $current_url = twin::path();
        $user = User::where('id', Auth::id())->first();
        $operations = UsersOperations::where('user_id', $user->id)->get();
        foreach($operations as $one_operation){
            if($one_operation->operation->url == '/'.$current_url){
                return $next($request);
            }
        }
        return redirect(url('/'))->with('failure','Δεν έχετε δικαίωμα πρόσβασης σε αυτό τον πόρο');
    }
}
