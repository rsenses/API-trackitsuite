<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Http\Request;
use App\Registration;

class RegistrationCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $request;
    public $registration;

    /**
     * Create a new event instance.
     *
     * @param  Illuminate\Http\Request  $request
     * @param  \App\Registration  $registration
     * @return void
     */
    public function __construct(Request $request, Registration $registration)
    {
        $this->request = $request;
        $this->registration = $registration;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
