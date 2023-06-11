<?php

namespace App\Http\Controllers;

use App\Models\Form;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function getFormElementAttributes(Request $request){
        return response()->json(["type" => "text", "attributes"=>"label"]);
    }

    public function insertForm(Request $request){
        $incoming_fields = $request->all();
        print_r($incoming_fields);
        $form = new Form;
        $form->name = $request->name;
        $form->description = $request->description;
        $form->color = $request->color;
        $form->icon = isset($request->icon)?$request->icon:"";
        $form->active = ($request->active=="on")?1:0;
        $form->visible = ($request->visible=="on")?1:0;
        $form->accepts = ($request->accepts=="on")?1:0;
        $questionsArr=array();
        //format json
        if(isset($request->question1)){
            array_push($questionsArr, [$request->question1]);
        }
        if(isset($request->question2)){
            array_push($questionsArr, [$request->question2]);
        }
        $elements = json_encode($questionsArr);
        $form->elements = $elements;
        $form->opens_at = $request->opens_at;
        $form->closes_at = $request->closes_at;
        $form->save();
        return redirect(url('/form_edit1_settings'))->with('success', 'Η φόρμα αποθηκεύτηκε. Προχωρήστε στην ενότητα: Σχολεία ή Εκπαιδευτικοί.');
       
    }
}
