<?php

namespace App\Http\Controllers\microapps;

use Illuminate\Http\Request;
use App\Models\microapps\Action;
use App\Http\Controllers\Controller;

class ActionsController extends Controller
{
    public function index()
    {
        return view('microapps.actions.index');
    }
    
    public function create()
    {
        return view('microapps.actions.create');
    }
    
        public function store(Request $request)
        {
            $request->validate([
                'category' => 'required',
                'description' => 'required'
            ]);
    
            $actionType = new Action();
            $actionType->category = $request->category;
            $actionType->description = $request->description;
            $actionType->save();
    
            return redirect()->route('actions.index');
        }
    
        public function edit($id)
        {
            $actionType = ActionType::find($id);
            return view('microapps.actions.edit', compact('action'));
        }
    
        public function update(Request $request, $id)
        {
            $request->validate([
                'category' => 'required',
                'description' => 'required'
            ]);
    
            $actionType = Action::find($id);
            $actionType->category = $request->category;
            $actionType->description = $request->description;
            $actionType->save();
    
            return redirect()->route('action.index');
        }
    
        public function destroy($id)
        {
            $actionType = Action::find($id);
            $actionType->delete();
    
            return redirect()->route('actions.index');
        }
    
        
    }

