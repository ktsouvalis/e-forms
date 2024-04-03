<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Microapp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CanUpdateOuting
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $microapp = Microapp::where('url','/outings')->first();
        if($microapp->active and $microapp->visible and $microapp->accepts){
            $outing = $request->route('outing'); // takes the {outing} argument from the route, which is also Outing model
            $school = Auth::guard('school')->user();
            if($school){
                if($outing->school_id == $school->id)
                    return $next($request);
            }
            abort(403, 'Μη εξουσιοδοτημένη ενέργεια');  
        }
        abort(403, 'Εφαρμογή "Εκδρομές" μη ενεργή');
    }
}
