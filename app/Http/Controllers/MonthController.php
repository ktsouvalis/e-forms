<?php

namespace App\Http\Controllers;

use App\Models\Month;
use Illuminate\Http\Request;

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

        return redirect(url('/month'))->with('success', "Ενεργός μήνας: $new_active_month->name");
    }
}
