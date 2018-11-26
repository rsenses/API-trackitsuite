<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use SM\Event\SMEvents;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        'App\Events\RegistrationCreated' => [
            'App\Listeners\SendRegistrationCreatedNotification',
            'App\Listeners\SendRegistrationCreatedRequest',
        ],
        'App\Events\RegistrationAuthorized' => [
            'App\Listeners\SendRegistrationAuthorizedNotification',
            'App\Listeners\SendRegistrationAuthorizedRequest',
        ],
        SMEvents::POST_TRANSITION => [
            'App\Listeners\StateHistoryManager@postTransition',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
