<?php

namespace App\Http\Controllers\microapps;

use App\Http\Controllers\Controller;
use App\Models\microappps\BuildingProblems;
use Illuminate\Http\Request;

class BuidingProblemsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return view('microapps.building_problems.index', ['appname' => 'building_problems']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('microapps.building_problems.create', ['appname' => 'building_problems']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        //dd($request->all());
        // Validate the request data
        if($request->comments == null && !$request->hasFile('files')) {
            return redirect()->back()->withErrors(['comments' => 'Comments are required']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(BuildingProblems $buildingProblems)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BuildingProblems $buildingProblems)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BuildingProblems $buildingProblems)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BuildingProblems $buildingProblems)
    {
        //
    }

    public function updload_files(Request $request)
    {
        // Handle file upload logic here
        // Validate and store files, then return a response
        return response()->json(['message' => 'Files uploaded successfully']);
    }
}
