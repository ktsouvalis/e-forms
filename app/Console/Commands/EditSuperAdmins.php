<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Superadmin;
use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class EditSuperAdmins extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'super {u_n : the username of the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds/Removes the user in/from superadmins';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $u_n = $this->argument('u_n');
        $user = User::where('username', $u_n)->first();
        if($user){
            if(Superadmin::where('user_id', $user->id)->count()){
            // if($user->isAdmin()){
                Superadmin::where('user_id', $user->id)->first()->delete();
                $string = "removed from";
            }
            else{
                Superadmin::create([
                    'user_id' => $user->id
                ]);
                $string = "added to";
            }
            $output = "User $u_n $string superadmins";
            Log::channel('commands_executed')->info("server: ".$output);
        }
        else{
            $output = "User $u_n not found";
            Log::channel('commands_executed')->warning("server: ".$output);
        }
        session()->flash('command_output', $output);
    }
}
