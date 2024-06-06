<?php

namespace App\Http\Controllers;

use App\Models\Interaction;
use Illuminate\Http\Request;
use App\Models\InteractionType;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FilesController;

class InteractionController extends Controller
{
    //

    public function catalogue(InteractionType $interactionType)
    {
        $interactions = Interaction::where('interaction_type_id', $interactionType->id)->get();
        return view('interactions.catalogue', compact('interactions'));
    }

    public function store(Request $request){
        $user = Auth::guard('teacher')->check() ? Auth::guard('teacher')->user() : Auth::guard('school')->user();
        $data = request()->validate([
            'file' => 'mimetypes:application/pdf,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
        // dd($request->all());
        $interaction = new Interaction();
        $request->all()['interaction_type_id'] == '0' ? $interaction->interaction_type_id = null : $interaction->interaction_type_id = $request->all()['interaction_type_id'];
        $allowedTags = '<p><ul><ol><li><b><i><u>';
        $cleanText = strip_tags($request->all()['text'], $allowedTags);
        $interaction->text = $cleanText;
        $interaction->stakeholder_id = $user->id;
        $interaction->stakeholder_type = get_class($user);
        $interaction->save();
        if(request()->hasFile('files')){
            $files = request()->file('files');
            $fileData = [];
            foreach($files as $index => $file){
                $directory = "interactions/$interaction->id";
                $fileHandler = new FilesController();
                $filename = $file->getClientOriginalName();
                $upload  = $fileHandler->upload_file($directory, $file, 'local');
                $fileData[] = ['index' => $index + 1, 'filename' => $filename];
            }
            $interaction->files = json_encode($fileData);
        }   
        $interaction->save();
        
        return redirect(route('interactions.create'))->with('success', 'Η αίτηση καταχωρήθηκε επιτυχώς');
    }
    public function create()
    {
        //
        if(Auth::guard('school')->check()){
            return view('interactions.create-school');
        }
        else if(Auth::guard('teacher')->check()){
            return view('interactions.create-teacher');
        }
    }
}
