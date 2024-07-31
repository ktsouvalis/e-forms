<?php

namespace App\Http\Controllers\microapps;

use App\Models\Microapp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LeavesController extends Controller
{
    private $microapp;

    public function __construct(){
        $this->middleware('auth')->only(['index']);
        $this->middleware('isSchool')->only(['create', 'store']);
        $this->microapp = Microapp::where('url', '/leaves')->first();
    }

    public function index(){
        return view('microapps.leaves.index', ['appname' => 'leaves']);
    }

    public function create(){
        return view('microapps.leaves.create', ['appname' => 'leaves']);
    }

    public function upload_files(Request $request){
        return redirect(url('leaves/create'))->with('success','Τα αρχεία ανέβηκαν (fake).');//response()->json(['message' => 'Τα αρχεία ανέβηκαν επιτυχώς (fake)!']);
    }

    public function send_to_protocol(Request $request){
        return redirect(url('leaves/create'))->with('success','Η άδεια υποβλήθηκε (fake).');//response()->json(['message' => 'Η άδεια αποθηκεύτηκε επιτυχώς (fake)!']);
    }
}
