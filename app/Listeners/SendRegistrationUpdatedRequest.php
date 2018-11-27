<?php

namespace App\Listeners;

use SM\Event\TransitionEvent;

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
     * @param  TransitionEvent  $event
     * @return void
     */
    public function handle(TransitionEvent $event)
    {
        $registration = $event->getStateMachine()->getObject();

        $params = [
            'unique_id' => $registration->unique_id,
        ];

        // $this->sendPostRequest('https://smart.conferenciasyformacion.com/trackit/', 'attended', $params);
    }
}
