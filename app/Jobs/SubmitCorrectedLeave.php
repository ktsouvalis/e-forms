<?php
namespace App\Jobs;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\microapps\TeacherLeaves;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class SubmitCorrectedLeave implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public $leaveId;
    public $tries = 1;           // Δοκιμάζει 3 φορές
    //public $backoff = [60, 300]; // Retry μετά 1min, 5min

    /**
     * Create a new job instance.
     */
    public function __construct($leaveId)
    {
        $this->leaveId = $leaveId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $leave = TeacherLeaves::find($this->leaveId);
        
        if (!$leave) {
            Log::channel('files')->error("Job: Leave {$this->leaveId} not found");
            return;
        }

        try {
            // Κλήση της υπάρχουσας μεθόδου
            $controller = new \App\Http\Controllers\microapps\LeavesController();
            $protocolResult = $controller->sendLeaveToProtocol($leave);
            
            if ($protocolResult['success']) {
                // Parse το πρωτόκολλο
                $protocolParts = explode(" - ", $protocolResult['message']);
                
                $leave->protocol_number = $protocolParts[0];
                $leave->protocol_date = Carbon::createFromFormat('d/m/Y', $protocolParts[1])->format('Y-m-d');
                $leave->submitted = 1;
                $leave->save();
                
                Log::channel('files')->info("Job: Auto-submitted corrected leave {$leave->id}: {$protocolParts[0]}");
            } else {
                throw new \Exception($protocolResult['message']);
            }
            
        } catch (\Exception $e) {
            Log::channel('files')->error("Job: Failed to submit leave {$this->leaveId}: " . $e->getMessage());
            throw $e; // Θα κάνει retry
        }
    }
}