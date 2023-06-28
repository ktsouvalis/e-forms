<?php

namespace App\Mail;

use App\Models\Microapp;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\MicroappStakeholder;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewMicroappToSubmit extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Microapp $microapp, public MicroappStakeholder $stakeholder)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Νέα Ενότητα για Υποβολή Στοιχείων',
            tags:['new-microapp'],
            metadata:[
                'microapp_id' => $this->microapp->id
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.new-microapp-to-submit',
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
}
