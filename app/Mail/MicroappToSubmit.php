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
use romanzipp\QueueMonitor\Traits\IsMonitored;

class MicroappToSubmit extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels, isMonitored;

    /**
     * The maximum number of times the job may be attempted.
     *
     * @var int
     */
    public $username; 
    protected $name;
    /**
     * Create a new message instance.
     */
    public function __construct(public MicroappStakeholder $stakeholder, $username='system')
    {
        //
        $this->name = $this->stakeholder->microapp->name;
        $this->username = $username;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Ενότητα '$this->name' για Υποβολή Στοιχείων",
            tags:['new-microapp'],
            metadata:[
                'microapp_id' => $this->stakeholder->microapp_id
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.microapp-to-submit',
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
