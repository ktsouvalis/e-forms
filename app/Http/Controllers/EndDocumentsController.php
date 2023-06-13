<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\mEndDocument;
use Illuminate\Http\Request;

class EndDocumentsController extends Controller
{
    //
    public function inform_end_documents(Request $request, Teacher $teacher){
        mEndDocument::updateOrCreate(
            [
                'teacher_id' => $teacher->id
            ],
            [
                'teacher_id' => $teacher->id,
                'checked' => 1
            ]
            );
        
        return redirect(url("/teacher_app/end_documents"))->with('success', 'H Διεύθυνση Πρωτοβάθμιας Εκπαίδευσης Αχαΐας ενημερώθηκε επιτυχώς');

    }
}
