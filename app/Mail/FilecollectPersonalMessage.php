<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\FilecollectStakeholder;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class FilecollectPersonalMessage extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, isMonitored;

    public FilecollectStakeholder $stakeholder;
    /**
     * Create a new message instance.
     */
    public function __construct(FilecollectStakeholder $stakeholder)
    {
        //
        $this->stakeholder = $stakeholder;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Νέο Μήνυμα στην ενότητα ".$this->stakeholder->filecollect->name,
            tags:['filecollect-personal-message'],
            metadata:[
                'filecollect_id' => $this->stakeholder->filecollect->id
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.filecollect-personal-message',
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
