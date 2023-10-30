<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
        Operation::class => OperationPolicy::class,
        Teacher::class => TeacherPolicy::class,
        School::class => SchoolPolicy::class,
        Form::class => FormPolicy::class,
        Microapp::class => MicroappPolicy::class,
        Fileshare::class => FilesharePolicy::class,
        Ticket::class => TicketPolicy::class,
        Consultant::class => ConsultantPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
