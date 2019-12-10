<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Registration;
use App\Traits\AllowedTemplateData;
use Illuminate\Support\Facades\Config;

class RegistrationUpdated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, AllowedTemplateData;

    public $data;
    public $registration;
    public $template;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Registration $registration)
    {
        $this->registration = $registration;

        $this->data = $this->getRegistrationData($registration);

        $this->template = $this->registration->product
            ->templates()
            ->where('state', $registration->state)
            ->firstOrFail();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $fromEmail = $this->registration->product->company->from_email ?: Config::get('mail.from.address');
        $fromName = $this->registration->product->company->from_name ?: Config::get('mail.from.name');

        $subject = $this->template->subject ?: __('messages.subject.registrations.' . $this->registration->state, ['product' => $this->registration->product->name]);

        return $this->subject($subject)
            ->from($fromEmail, $fromName)
            ->view('emails.registrations.template');
    }
}
