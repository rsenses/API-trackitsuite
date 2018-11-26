<?php

namespace App\Listeners;

use App\Events\RegistrationAuthorized;
use App\Mail\RegistrationAuthorized as Maillable;
use Illuminate\Support\Facades\Mail;

class SendRegistrationAuthorizedNotification
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
     * @param  RegistrationAuthorized  $event
     * @return void
     */
    public function handle(RegistrationAuthorized $event)
    {
        $registration = $event->registration;

        if ($registration->is_authorized && $registration->product->templates()->where('event', 'registration.authorized')->exists()) {
            Mail::to($registration->customer->email)->queue(new Maillable($registration));
        }
    }
}
