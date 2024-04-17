<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use App\Events\SchoolsTeachersUpdated;
use Illuminate\Auth\Events\Registered;
use App\Listeners\SchoolsTeachersUpdatedListener;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        'Illuminate\Mail\Events\MessageSent' => [
            'App\Listeners\MailSentListener',
        ],
        'Illuminate\Queue\Events\JobFailed' => [
            'App\Listeners\JobFailedListener',
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
        Event::listen(
            SchoolsTeachersUpdated::class, 
            SchoolsTeachersUpdatedListener::class
        );
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
