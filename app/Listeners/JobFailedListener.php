<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class JobFailedListener
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
     *
     * @param  JobFailed  $event
     * @return void
     */
    public function handle(JobFailed $event)
    {
        if(str_contains($event->job->resolveName(), 'App\Mail')){
            $payload = json_decode($event->job->getRawBody(), true);
            $command = unserialize($payload['data']['command']);
            $email = $command->mailable->to[0]['address'];
            Log::channel('mails')->error("Mail failed: " . $email. " " . $event->exception->getMessage());
        }
    }
}
