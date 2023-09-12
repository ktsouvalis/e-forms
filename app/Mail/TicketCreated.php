<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\microapps\Ticket;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class TicketCreated extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    protected $id;
    /**
     * Create a new message instance.
     */
    public function __construct(public Ticket $ticket)
    {
        //
        $this->id = $this->ticket->id;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "#$this->id: Νέο Δελτίο Τεχνικής Υποστήριξης",
            tags:['new-ticket'],
            metadata:[
                'ticket_id' => $this->id
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-created',
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
