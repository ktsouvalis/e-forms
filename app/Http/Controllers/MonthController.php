<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Month;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class MonthController extends Controller
{
    //
    public function setActiveMonth(Request $request){
        $request_active_month = $request->input('active_month');
        $new_active_month = Month::find($request_active_month);
        $current_active_month = Month::getActiveMonth();

        $current_active_month->active = 0;
        $current_active_month->save();
        
        $new_active_month->active = 1;
        $new_active_month->save();
        Log::channel('user_memorable_actions')->info(Auth::user()->username." set Active month to ".Month::getActiveMonth()->name);
        return redirect(url('/month'))->with('success', "Ενεργός μήνας: ". Month::getActiveMonth()->name);
    }

    public function setVirtualMonth(Request $request, School $school){
        $new_vmonth = Month::where('name', $request->input('month'))->first();
        // dd($new_vmonth);
        if($new_vmonth){
            try{
                $school->vmonth = $new_vmonth->number;
                $school->save();
                
            }
            catch(Exception $e){
                Log::channel('throwable_db')->error(Auth::user()->username." tried to change virtual month: ".$e->getMessage());
                return back()->with('failure', 'Κάποιο σφάλμα προέκυψε (throwable_db)');
            }
        }
        else{
            return back()->with('failure', 'Ο εικονικός μήνας πρέπει να επιλεγεί από τη λίστα');    
        }
        return back()->with('success',"Το $school->name έχει ενεργό εικονικά τον μήνα $new_vmonth->name");
    }

    public function resetActiveMonth(Request $request, School $school){
        try{
            $school->vmonth = 0;
            $school->save();
        }
        catch(\Exception $e){
            Log::channel('throwable_db')->error(Auth::user()->username." tried to change virtual month: ".$e->getMessage());
            return back()->with('failure', 'Κάποιο σφάλμα προέκυψε (throwable_db');   
        }
        return back()->with('success',"Έγινε επαναφορά για το $school->name");
    }
}
