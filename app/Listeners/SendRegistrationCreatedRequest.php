<?php

namespace App\Listeners;

use App\Events\RegistrationCreated;

class SendRegistrationCreatedRequest
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
     * @param  RegistrationCreated  $event
     * @return void
     */
    public function handle(RegistrationCreated $event)
    {
        $registration = $event->registration;
        $request = $event->request;

        $params = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'registration_type' => $registration->type->name,
            'unique_id' => $registration->unique_id,
            'attended' => $registration->verification,
        ];

        $noSendData = ['_token', 'email', 'first_name', 'last_name', 'registration_type_id', 'verification'];

        foreach ($request->except($noSendData) as $key => $value) {
            $params[$key] = $value;
        }

        // $this->sendPostRequest('https://smart.conferenciasyformacion.com/trackit/', 'create', $params);
    }
}
