<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class UpdateEDirectorateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $username;
    /**
     * Create a new job instance.
     */
    public function __construct($username)
    {
        //
        $this->username = $username;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $client = new Client();
        try{
            $res = $client->request('GET', 'http://194.63.234.132/eprotocolapi/api/migration/catalogs', [
                'timeout' => 180,
            ]);
        }
        catch(\Exception $e){
            Log::channel('commands_executed')->error($this->username. " Queue Job UpdateEDirectorateJob: ".$e->getMessage());
            return;
        }
        if($res->getStatusCode() == 201){
            DB::table('last_update_edirectorate')->updateOrInsert(
                ['id' => 1],
                ['date_updated' => now()]
            );
            $output = "eDirectorate updated successfully ".$res->getBody();
        }
        else{
            $output = "eDirectorate update failed ".$res->getBody();  
        }
        Log::channel('commands_executed')->info($this->username. " Queue Job UpdateEDirectorateJob: ".$output);
    }
}
