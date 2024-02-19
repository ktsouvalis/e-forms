<?php

namespace App\Http\Controllers;

use App\Models\Test;
use App\Models\Operation;
use Illuminate\Http\Request;

class TestController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('boss')->only('create','store','destroy');
        $this->middleware('auth')->only([ 'update']);
    }

    public function index(){
        $tests = Test::all();
        return view('tests.index', compact('tests'));
    }

    public function create(){
        return view('tests.create');
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required'
        ]);
        Test::create($request->all());
        return redirect()->route('tests.index')->with('success', 'Test created successfully');
    }

    public function show(Test $test)
    {
        // Display the specified resource.
        return view('tests.show', compact('test'), ['test' => $test]);
    }

    public function edit(Test $test)
    {
        // Show the form for editing the specified resource.
        return view('tests.edit', compact('test'), ['test' => $test]);
    }

    public function update(Request $request, Test $test)
    {
        // Update the specified resource in storage.
        $request->validate([
            'name' => 'required'
        ]);
        $test->update($request->all());
        return redirect()->route('tests.index')->with('success', 'Test updated successfully');
    }

    public function destroy(Test $test)
    {
        // Remove the specified resource from storage.
        $test->delete();
        return redirect()->route('tests.index')->with('success', 'Test deleted successfully');
    }
}
