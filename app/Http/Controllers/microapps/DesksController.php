<?php

namespace App\Http\Controllers\microapps;

use App\Models\Microapp;
use Illuminate\Http\Request;
use App\Models\microapps\Desks;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DesksController extends Controller
{
    private $microapp;

    public function __construct(){
        $this->middleware('auth')->only(['index']);
        $this->middleware('isSchool')->only(['create', 'store']);
        $this->microapp = Microapp::where('url', '/desks')->first();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('microapps.desks.index', ['appname' => 'desks']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('microapps.desks.create', ['appname' => 'desks']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
      
        Desks::updateOrCreate(
            [
                'school_id' => Auth::guard('school')->user()->id
            ],
            [
                'number' => $request->input('number'),
                'comments' => $request->input('comments')
            ]
        );
        return back()->with('success', 'Επιτυχής αποθήκευση αίτησης.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
