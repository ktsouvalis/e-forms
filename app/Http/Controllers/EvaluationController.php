<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EvaluationController extends Controller
{
    //
    public function upload_csv(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);
        if($request->hasFile('file')){
            $file = $request->file('file');
            $file_name = $file->getClientOriginalName();
            switch ($request->action) {
                case 'a1':
                    
                    $file->move(storage_path('evaluation'), 'a1.csv');
                    break;
                case 'a2':
                    $file->move(storage_path('evaluation'), 'a1.csv');
                    break;
                case 'b':
                    $file->move(storage_path('evaluation'), 'a1.csv');
                    break;
            }
        }
        

        return back()->with('success', 'File uploaded successfully');
    }
}
