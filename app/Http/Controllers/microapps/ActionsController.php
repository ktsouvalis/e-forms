<?php

namespace App\Http\Controllers\microapps;

use Illuminate\Http\Request;
use App\Models\microapps\Action;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ActionsController extends Controller
{
    public function index()
    {
        // Check if the user is authenticated as a school user
        if (Auth::guard('school')->check()) {
            return view('microapps.actions.index');
            // You can retrieve the authenticated user using 
            //dd('school user', Auth::guard('school')->user());
        } 
        // Check if the user is authenticated as a consultant
        if (Auth::guard('consultant')->check()) {
            if(Auth::guard('consultant')->user()->is_supervisor) {
                return view('microapps.actions.index_consultant', ['is_supervisor' => true]);
            } else {
                return view('microapps.actions.index_consultant');
            }
            return view('microapps.actions.index_consultant');
            //dd('consultant', Auth::guard('consultant')->user());
            // You can retrieve the authenticated user using 
        }
        // Check if the user is authenticated as a directory user
        if (Auth::guard()->check()) {
            dd('directory user', Auth::guard()->user());
            // You can retrieve the authenticated user using 
        }
    }

    public function indexForConsultantsSupervisor() {
        // Check if the user is authenticated as a consultant
        if (Auth::guard('consultant')->check() && Auth::guard('consultant')->user()->is_supervisor) {
            
            return view('microapps.actions.index_consultant', ['is_supervisor' => true]);
            // You can retrieve the authenticated user using 
            //dd('consultant', Auth::guard('consultant')->user());
        } 
    }

    public function show(Request $request)
    {
        return view('microapps.actions.index_school', compact('request'));
    }
    public function index_school()
    {
        dd('index_school');
        //return view('microapps.actions.index_school');
    }
    
    public function create()
    {
        return view('microapps.actions.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required',
            'title' => 'required'
        ]);

        $action = new Action();
        $action->school_id = Auth::guard('school')->user()->id;
        $action->actiontype_id = $request->category;
        $action->implementing_authority = $request->implementing_authority;
        $action->title = $request->title;
        $action->number_of_teachers = $request->number_of_teachers;
        $action->teachers = $request->teachers;
        $action->records = $request->records;
        $action->comments = $request->comments;
        $action->save();

        return redirect()->route('actions.index_school');
    }
    
    public function edit($id)
    {
        $action = Action::find($id);
        return view('microapps.actions.edit', compact('action'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category' => 'required',
            'title' => 'required'
        ]);

        $action = Action::find($id);
        $action->school_id = Auth::guard('school')->user()->id;
        $action->actiontype_id = $request->category;
        $action->implementing_authority = $request->implementing_authority;
        $action->title = $request->title;
        $action->number_of_teachers = $request->number_of_teachers;
        $action->teachers = $request->teachers;
        $action->records = $request->records;
        $action->comments = $request->comments;
        $action->save();

        return redirect()->route('actions.index_school');
    }
    
    public function destroy($id)
    {
        $actionType = Action::find($id);
        $actionType->delete();
        
        // Update the outings table to set action_id to 0 where action_id matches $id
        DB::table('outings')->where('action_id', $id)->update(['action_id' => 0]);

        return redirect()->route('actions.index_school');
    }

    public function check(Request $request)
    {
        dd('check');
    }
    
        
}

