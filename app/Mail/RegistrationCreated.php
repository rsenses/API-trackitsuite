<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Registration;

class RegistrationCreated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The registration instance.
     *
     * @var Registration
     */
    public $registration;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Registration $registration)
    {
        $this->registration = $registration;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(__('messages.subject.registrations.created', ['product' => $this->registration->product->name]))
            ->view('emails.registrations.created');
    }
}
