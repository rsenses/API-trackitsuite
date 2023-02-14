<?php

namespace App\Listeners;

use SM\Event\TransitionEvent;
use App\Traits\GuzzleRequest;

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
        $transition = $event->getTransition();
        $registration = $event->getStateMachine()->getObject();
        $api = $registration->product->company->api;

        $params = [
            'first_name' => $registration->customer->first_name,
            'last_name' => $registration->customer->last_name,
            'email' => $registration->customer->email,
            'company' => $registration->customer->metas->where('meta_key', 'company')->count() ? $registration->customer->metas->where('meta_key', 'company')->first()->meta_value : '',
            'position' => $registration->customer->metas->where('meta_key', 'position')->count() ? $registration->customer->metas->where('meta_key', 'position')->first()->meta_value : '',
            'product_id' => $registration->product_id,
            'registration_type' => $registration->type,
            'unique_id' => $registration->unique_id,
        ];

        if ($transition && $api && $registration->product->send_to_api) {
            $this->sendPostRequest($api, $transition, $params);
        }
    }
}
