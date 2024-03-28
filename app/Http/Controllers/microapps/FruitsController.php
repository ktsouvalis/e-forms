<?php

namespace App\Http\Controllers\microapps;

use Throwable;
use App\Models\School;
use App\Models\Microapp;
use Illuminate\Http\Request;
use App\Models\microapps\Fruit;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class FruitsController extends Controller
{
    //

    private $microapp;

    public function __construct(){
        $this->middleware('isSchool')->only(['create', 'store']);
        $this->middleware('canViewMicroapp')->only(['create','store']);
        $this->middleware('auth')->only(['index']);
        $this->middleware('canViewMicroapp')->only(['index']);
        $this->microapp = Microapp::where('url', '/fruits')->first();
    }

    public function index(){
        return view('microapps.fruits.index', ['appname' => 'fruits']);
    }

    public function create(){
        return view('microapps.fruits.create', ['appname' => 'fruits']);
    }

    public function store(Request $request){
        $school = Auth::guard('school')->user();
        if($this->microapp->accepts){
            try{
                Fruit::updateOrCreate(
                    [
                        'school_id'=>$school->id
                    ],
                    [
                        'no_of_students' => $request->input('students_number'),
                        'no_of_ukr_students' => $request->input('ukr_students_number'),
                        'comments' => $request->input('comments')
                    ]
                );
            }
            catch(Throwable $e){
                try{
                    Log::channel('throwable_db')->error(Auth::guard('school')->user()->name.' create fruits db error '.$e->getMessage());
                }
                catch(Throwable $e){
        
                }
                return redirect(url('/microapps/fruits/create'))->with('failure', 'Η εγγραφή δεν αποθηκεύτηκε. Προσπαθήστε ξανά');
            }
            $stakeholder = $this->microapp->stakeholders->where('stakeholder_id', $school->id)->where('stakeholder_type', 'App\Models\School')->first();
            $stakeholder->hasAnswer = 1;
            $stakeholder->save();
            
            return redirect(url('/microapps/fruits/create'))->with('success', 'Η εγγραφή αποθηκεύτηκε.');
        }
        else{
            return redirect(url('/microapps/fruits/create'))->with('failure', 'Η δυνατότητα υποβολής έκλεισε από τον διαχειριστή.');
        }
    }
}
