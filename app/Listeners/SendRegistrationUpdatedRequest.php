<?php

namespace App\Listeners;

use App\Events\RegistrationUpdated;

class SendRegistrationUpdatedRequest
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
     * @param  RegistrationUpdated  $event
     * @return void
     */
    public function handle(RegistrationUpdated $event)
    {
        $params = [
            'unique_id' => $event->registration->unique_id,
        ];

        // $this->sendPostRequest('https://smart.conferenciasyformacion.com/trackit/', 'attended', $params);
    }
}
