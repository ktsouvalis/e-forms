<?php

namespace App\Http\Controllers\microapps;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\microapps\ActionType;

class ActionTypesController extends Controller
{
    //
    public function index()
    {
        return view('microapps.actiontypes.index');
    }

    public function create()
    {
        return view('microapps.actiontypes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required',
            'description' => 'required'
        ]);

        $actionType = new ActionType();
        $actionType->category = $request->category;
        $actionType->description = $request->description;
        $actionType->save();

        return redirect()->route('actiontypes.index');
    }

    public function edit($id)
    {
        $actionType = ActionType::find($id);
        return view('microapps.actiontypes.edit', compact('actionType'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category' => 'required',
            'description' => 'required'
        ]);

        $actionType = ActionType::find($id);
        $actionType->category = $request->category;
        $actionType->description = $request->description;
        $actionType->save();

        return redirect()->route('actiontypes.index');
    }

    public function destroy($id)
    {
        $actionType = ActionType::find($id);
        $actionType->delete();

        return redirect()->route('actiontypes.index');
    }

    
}
