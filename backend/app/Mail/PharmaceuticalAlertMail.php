<?php

namespace App\Mail;

use App\Models\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PharmaceuticalAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Customer $customer,
        public string $lotNumber,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'IMPORTANT: Medication Recall Notice - Lot #' . $this->lotNumber,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.pharmaceutical-alert',
            with: [
                'customerName' => $this->customer->name,
                'lotNumber'    => $this->lotNumber,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
