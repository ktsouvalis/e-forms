<?php

namespace App\Jobs;

use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use App\Notifications\UserNotification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class UpdateEDirectorateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, isMonitored;
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
        try {
            $res = $client->request('GET', env('E_DIRECTORATE')."/migration/catalogs", [
                'timeout' => 180,
            ]);

            if ($res->getStatusCode() == 201) {
                DB::table('last_update_edirectorate')->updateOrInsert(
                    ['id' => 1],
                    ['date_updated' => now()]
                );
                Log::channel('commands_executed')->info($this->username. " UpdateEDirectorateJob success:  ".$res->getBody());
                foreach(User::all() as $user){
                    if(Superadmin::where('user_id', $user->id)->exists() or $user->username == $this->username){
                        $user->notify(new UserNotification("Η εφαρμογή πρωτοκόλλου ενημερώθηκε επιτυχώς για τις αλλαγές στη Βάση Δεδομένων. Το api request έγινε από $this->username", 'Επιτυχία API UpdateEDirectorate'));
                    }
                }
            } 
            else {
                throw new \Exception("UpdateEDirectorateJob: ".$res->getBody());
            }
        } 
        catch (\Exception $e) {
            Log::channel('commands_executed')->error($this->username. " UpdateEDirectorateJob failed:  ".$e->getMessage());
            throw $e;
        }
    }   
}
