<?php

namespace App\Listeners;

use App\Events\RegistrationUpdated;
use App\Mail\RegistrationAuthorized as Maillable;
use Illuminate\Support\Facades\Mail;

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
     * @param  RegistrationUpdated  $event
     * @return void
     */
    public function handle(RegistrationUpdated $event)
    {
        $registration = $event->registration;

        if ($registration->is_authorized && $registration->product->templates()->where('state', $registration->state)->exists()) {
            Mail::to($registration->customer->email)->queue(new Maillable($registration));
        }
    }
}
