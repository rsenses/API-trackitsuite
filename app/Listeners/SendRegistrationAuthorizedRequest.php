<?php

namespace App\Listeners;

use App\Events\RegistrationAuthorized;

class SendRegistrationAuthorizedRequest
{
    use GuzzleRequest;

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
        $params = [
            'unique_id' => $event->registration->unique_id,
        ];

        // $this->sendPostRequest('https://smart.conferenciasyformacion.com/trackit/', 'attended', $params);
    }
}
