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
        $schools_update_date = DB::table('last_update_schools')->first();
        $teachers_update_date = DB::table('last_update_teachers')->first();
        if(DB::table('last_update_edirectorate')->first() == null or DB::table('last_update_edirectorate')->first()<max($schools_update_date,$teachers_update_date)){
            $client = new Client();
            try{
                $res = $client->request('GET', 'http://194.63.234.132/eprotocolapi/api/migration/catalogs', [
                    'timeout' => 180,
                ]);
                // $res = $client->request('GET', 'http://10.35.249.10/eprotocolapi/api/migration/catalogs');
            }
            catch(\Exception $e){
                $output = "eDirectorate update failed: ".$e->getMessage();
                Log::channel('commands_executed')->info("server: ".$output);
                return;
            }
            if($res->getStatusCode() == 201){
                DB::table('last_update_edirectorate')->updateOrInsert(
                    ['id' => 1],
                    ['date_updated' => now()]
                );
                $output = "eDirectorate updated successfully: ".$res->getBody();
            }
            else{
                $output = "eDirectorate update failed: ".$res->getBody();  
            }  
        }
        else{
            $output = "eDirectorate already up to date";
        }
        Log::channel('commands_executed')->info("Cron Job: ".$output);
    }
}
