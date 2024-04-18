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
    public $username;
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
        $res = $client->request('GET', env('E_DIRECTORATE')."/migration/catalogs", [
            'timeout' => 180,
        ]);
        if($res->getStatusCode() == 201){
            DB::table('last_update_edirectorate')->updateOrInsert(
                ['id' => 1],
                ['date_updated' => now()]
            );
            Log::channel('commands_executed')->info($this->username. " UpdateEDirectorateJob:  ".$res->getBody());
        }
        else{
            throw new \Exception("UpdateEDirectorateJob: ".$res->getBody()); 
        } 
    }
}
