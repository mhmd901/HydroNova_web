<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    public array $data;

    /**
     * Create a new message instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = 'New Contact Message: ' . ($this->data['subject'] ?? '');

        return $this->subject($subject)
                    ->replyTo($this->data['email'] ?? null, $this->data['name'] ?? null)
                    ->view('emails.contact')
                    ->with(['data' => $this->data]);
    }
}

