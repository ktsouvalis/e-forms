<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateDirectorateName extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:udn {d_n : the directorate name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the name field of directorate_info table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $d_n = $this->argument('d_n');
        $error = false;
        try{
            DB::table('directorate_info')->updateOrInsert(['id'=>1],['name'=>$d_n]);
            
        } 
        catch(Exception $e){
            $error=true;
        }
        if($error){
            Log::channel('commands_executed')->info("udn ".$e->getMessage());
            session()->flash('command_output', "Directorate not updated. Check commands_executed log");
        }
        else{
            Log::channel('commands_executed')->info("udn success");
            session()->flash('command_output', "Directorate name updated successfully");
        }
    }
}
