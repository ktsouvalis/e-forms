<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InteractionType;

class InteractionTypesController extends Controller
{
    //
    public function index()
    {
        $interactionTypes = InteractionType::all()->filter(function ($interactionType) {
            return auth()->user()->can('view', $interactionType);
        });
        return view('interaction_types', compact('interactionTypes'));
    }

    public function store(Request $request){
        if(!auth()->user()->isAdmin())
            $department_id = auth()->user()->department->id;
        else
            $department_id = $request->all()['department_id'];

        $interactionType = new InteractionType;
        $interactionType->name = $request->all()['name'];
        $interactionType->folder = $request->all()['folder'];
        $interactionType->department_id = $department_id;
        $interactionType->stakes_to = $request->all()['stakes_to'];
        $interactionType->save();

        return redirect()->route('interaction_types.index')->with('success', 'Τύπος Επικοινωνίας δημιουργήθηκε επιτυχώς');
    }

    public function update(Request $request, InteractionType $interaction_type){
        $field = $request->field;
        $interaction_type->$field = $request->value;
        if($interaction_type->isDirty()){
            $interaction_type->save();
            return response()->json(['success' => 'Status changed successfully.', 'field_type' => $request->field, 'field_value' => $request->value]);
        }
        else{
            return response()->json(['success' => 'No changes made.']);
        }
    }

    public function destroy(InteractionType $interaction_type){
        $interaction_type->delete();
        return redirect()->route('interaction_types.index')->with('success', 'Διαγράφηκε επιτυχώς');
    }

    public function changeStatus(Request $request, $id){
        $interactionType = InteractionType::findOrFail($id);
        $interactionType->active = $request->active;
        $interactionType->save();

        return response()->json(['success' => 'Status changed successfully.', 'active' => $request->active]);
    }
}
