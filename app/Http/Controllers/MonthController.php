<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Month;
use App\Models\School;
use App\Models\VirtualMonth;
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
        return back()->with('success', "Ενεργός μήνας: ". Month::getActiveMonth()->name);
    }

    public function setVirtualMonth(Request $request, School $school){
        $new_vmonth = Month::where('name', $request->input('month'))->first();
        
        if($new_vmonth){
            $months = [];
            $number = Month::getActiveMonth()->number;
            $i=$number;
            if($number >=9){
                for($i; $i>=9; $i--)
                    array_push($months, $i);
            }
            else{
                for($i; $i>=1; $i--)
                    array_push($months, $i);
                array_push($months,12,11,10,9);
            }
            if(!in_array($new_vmonth->number, $months) or in_array($new_vmonth->number, [Month::getActiveMonth()->number, 7, 8]) ){
                return back()->with('failure','Μόνο προηγούμενοι μήνες της σχολικής χρονιάς επιτρέπονται');
            }
            try{
                VirtualMonth::updateOrCreate(
                    ['school_id'=>$school->id],
                    ['vmonth' => $new_vmonth->number]
                );
            }
            catch(Exception $e){
                Log::channel('throwable_db')->error(Auth::user()->username." tried to change virtual month: ".$e->getMessage());
                return back()->with('failure', 'Κάποιο σφάλμα προέκυψε (throwable_db)');
            }
        }
        else{
            return back()->with('failure', 'Ο εικονικός μήνας πρέπει να επιλεγεί από τη λίστα');    
        }
        Log::channel('user_memorable_actions')->info(Auth::user()->username." change virtual month of $school->name to $new_vmonth->name");
        return back()->with('success',"Το $school->name έχει ενεργό εικονικά τον μήνα $new_vmonth->name");
    }

    public function resetActiveMonth(Request $request, School $school){
        try{
            VirtualMonth::updateOrCreate(
                ['school_id'=>$school->id],
                ['vmonth' => 0]
            );
        }
        catch(\Exception $e){
            Log::channel('throwable_db')->error(Auth::user()->username." tried to change virtual month: ".$e->getMessage());
            return back()->with('failure', 'Κάποιο σφάλμα προέκυψε (throwable_db');   
        }
        Log::channel('user_memorable_actions')->info(Auth::user()->username." reset virtual month of $school->name");
        return back()->with('success',"Έγινε επαναφορά για το $school->name");
    }
}
