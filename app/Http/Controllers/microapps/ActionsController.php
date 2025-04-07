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
        return view('microapps.actions.index');
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
    
        
    }

