<?php
namespace App\Listeners;

use App\Events\LeavesImportCompleted;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ProcessCorrectedLeaves
{
    public function handle(LeavesImportCompleted $event): void
    {
        Log::channel('files')->info("Processing {$event->jobCount} corrected leaves...");
        
        // Τρέχει τα jobs και σταματάει όταν αδειάσει το queue
        Artisan::call('queue:work --stop-when-empty --tries=3');
        
        Log::channel('files')->info("Finished processing corrected leaves");
    }
}