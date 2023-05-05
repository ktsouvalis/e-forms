<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\UsersMenus;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    //
    public function insertMenu(Request $request){
        
        $incomingFields = $request->all();
        try{
            $record = Menu::create([
                'name' => $incomingFields['menu_name'],
                'url' => $incomingFields['menu_url'],
                'color' => $incomingFields['menu_color'],
                'opacity' => $incomingFields['menu_opacity'],
                'icon' => $incomingFields['menu_icon'],
            ]);
        } 
        catch(QueryException $e){
            return view('menus',['dberror'=>"Κάποιο πρόβλημα προέκυψε κατά την εκτέλεση της εντολής, προσπαθήστε ξανά.", 'old_data'=>$request]);
        }

        foreach($request->all() as $key=>$value){
            if(substr($key,0,4)=='user'){
                UsersMenus::create([
                    'menu_id'=>$record->id,
                    'user_id'=>$value
                ]); 
            }
        }

        UsersMenus::create([
            'menu_id'=>$record->id,
            'user_id'=>1
        ]);
        
         UsersMenus::create([
            'menu_id'=>$record->id,
            'user_id'=>2
        ]); 
        return view('menus',['record'=>$record]);
    }
}   
