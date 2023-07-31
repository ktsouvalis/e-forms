<?php

namespace App\Console\Commands;

use App\Models\Microapp;
use Illuminate\Console\Command;

class ChangeMicroappAcceptStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'microapps:accept_not';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Sets the accepts field of a microapp 0 according to closes_at of the microapps. Scheduled to run daily";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $microapps_to_close = Microapp::whereDate('closes_at', '<', now())->get();

        foreach($microapps_to_close as $microapp) {
            $microapp->update(['accepts' => 0]);
        }
        $now = Carbon::now();
        $this->info("$now: Microapps updated successfully.");
        
    }
}
