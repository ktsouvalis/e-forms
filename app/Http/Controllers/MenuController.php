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
                'icon' => $incomingFields['menu_icon'],
                'accepts' => 0,
                'viewable' => 0
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

    public function changeMenuStatus(Request $request){
        if($request->all()['asks_to'] == 'ch_vis_status'){
            $menu = Menu::find($request->all()['menu_id']);
            $menu->visible = $menu->visible==1?0:1;
            $menu->accepts = 0;
            $menu->save();
        }
        if($request->all()['asks_to'] == 'ch_acc_status'){
            $menu = Menu::find($request->all()['menu_id']);
            $menu->accepts = $menu->accepts==1?0:1;
            $menu->save();
        }
        return redirect('/manage_menus');
    }
}   
