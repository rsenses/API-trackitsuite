<?php

namespace App\Listeners;

use App\Events\RegistrationCreated;
use App\Mail\RegistrationCreated as Maillable;
use Illuminate\Support\Facades\Mail;

class SendRegistrationCreatedNotification
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
     * @param  RegistrationCreated  $event
     * @return void
     */
    public function handle(RegistrationCreated $event)
    {
        $registration = $event->registration;

        if (!$registration->is_authorized) {
            Mail::to($registration->customer->email)->queue(new Maillable($registration));
        }
    }
}
