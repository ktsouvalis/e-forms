<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use App\Models\microapps\Ticket;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class TicketUpdated extends Mailable
{
    use SerializesModels;
    
    protected $id;
    
    /**
     * Create a new message instance.
     */
    public function __construct(public Ticket $ticket, public $new_string, public $who_updated, public $link=null)
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
            subject: "#$this->id: Ενημέρωση Δελτίου Τεχνικής Υποστήριξης",
            tags:['update-ticket'],
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
            view: 'emails.ticket-updated',
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
