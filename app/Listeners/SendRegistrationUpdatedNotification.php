<?php

namespace App\Listeners;

use App\Mail\RegistrationUpdated;
use Illuminate\Support\Facades\Mail;
use SM\Event\TransitionEvent;

class SendRegistrationUpdatedNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  TransitionEvent  $event
     * @return void
     */
    public function handle(TransitionEvent $event)
    {
        $state = $event->getStateMachine()->getState();
        $registration = $event->getStateMachine()->getObject();

        if ($registration->product->templates()->where('state', $state)->exists()) {
            Mail::to($registration->customer->email)->queue(new RegistrationUpdated($registration));
        }
    }
}
