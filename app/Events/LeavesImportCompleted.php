<?php
namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeavesImportCompleted
{
    use Dispatchable, SerializesModels;

    public $jobCount; // Πόσα jobs έστειλες

    public function __construct($jobCount)
    {
        $this->jobCount = $jobCount;
    }
}