<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;

class DynamicGeneralMail extends Mailable
{
    /**
     * Create a new message instance.
     */
    public function __construct(
        public string $subjectText,
        public string $htmlBody,
        public ?string $replyToEmail = null
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $envelope = new Envelope(subject: $this->subjectText);

        if ($this->replyToEmail) {
            $envelope->replyTo = [new Address($this->replyToEmail)];
        }

        return $envelope;
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Renders raw HTML strings directly, mimicking your original script
        return new Content(htmlString: $this->htmlBody);
    }
}