<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateEDirectorate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-e-directorate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tell eDirectorate API to update its data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $done = false;
        $schools_update_date = DB::table('last_update_schools')->first();
        $teachers_update_date = DB::table('last_update_teachers')->first();
        if(DB::table('last_update_edirectorate')->first() == null or DB::table('last_update_edirectorate')->first()<max($schools_update_date,$teachers_update_date)){
            $client = new Client();
            $res = $client->request('GET', 'http://194.63.234.132/eprotocolapi/api/migration/catalogs', [
                'timeout' => 180, // Set the timeout value in seconds
            ]);
            // $res = $client->request('GET', 'http://10.35.249.10/eprotocolapi/api/migration/catalogs');
            $done = true;
        }
        if($done){
            if($res->getStatusCode() == 201){
                DB::table('last_update_edirectorate')->updateOrInsert(
                    ['id' => 1],
                    ['date_updated' => now()]
                );
                $output = "eDirectorate updated successfully";
            }
            else{
                $output = "eDirectorate update failed";  
            }
        }
        else{
            $output = "eDirectorate already up to date";
        }
        Log::channel('commands_executed')->info("server: ".$output);
        session()->flash('command_output', $output);
    }
}
