<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Customer;

class ConfirmEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Customer $customer,
    )
    {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address( env('MAIL_FROM_ADDRESS', 'donotreply@example.com'), env('MAIL_FROM_NAME', 'Example')),
            subject: env('MAIL_SUBJECT', 'Confirm Your Email Address to Keep Reading'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mail.confirmEmail',
            with: [
                'customer' => $this->customer,
                'url' => $this->createUrl( $this->customer ),
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    private function createUrl( Customer $customer ): string {
        return env('FRONTEND_URL', 'https://allegro.alley.test') . '/confirm-email?email=' . urlencode($customer->email) . '&token=' . md5($customer->email);
    }
}
