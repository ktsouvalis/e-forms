<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\MailFailed;
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
        $jobName = $event->job->resolveName();
        $payload = json_decode($event->job->getRawBody(), true);
        $command = unserialize($payload['data']['command']);
        if(str_contains($jobName, 'App\Mail')){
            $email = $command->mailable->to[0]['address'];
            $username = $command->mailable->username; //this is the username of the user that triggered the job. it is a public property of all mailable classes
            Log::channel('mails')->error("$jobName by $username failed: " . $email. " " . $event->exception->getMessage());
            $user = User::where('username', $username)->first();
            $user->notify(new MailFailed($event->exception->getMessage(), "Αποτυχία email στο $email"));
        }
        else if(str_contains($jobName, 'App\Jobs')){
            $username = $command->username; //this is the username of the user that triggered the job. it is a public property of the job class
            Log::channel('commands_executed')->error("$jobName by $username failed: " . $event->exception->getMessage());
        }
    }
}
