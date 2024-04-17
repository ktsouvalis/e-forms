<?php

namespace App\Listeners;

use App\Jobs\UpdateEDirectorateJob;
use App\Events\SchoolsTeachersUpdated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SchoolsTeachersUpdatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SchoolsTeachersUpdated $event): void
    {
        //
        dispatch(new UpdateEDirectorateJob('system'));
    }
}
