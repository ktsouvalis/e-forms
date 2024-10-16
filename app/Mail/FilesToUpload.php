<?php

namespace App\Mail;

use App\Models\Filecollect;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\FilecollectStakeholder;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class FilesToUpload extends Mailable implements ShouldQueue
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
    public function __construct(public Filecollect $filecollect, public FilecollectStakeholder $stakeholder, $username='system')
    {
        //
        $this->name = $this->filecollect->name;
        $this->username = $username;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Υποβολή Αρχείου '$this->name'",
            tags:['new-filecollect'],
            metadata:[
                'filecollect_id' => $this->filecollect->id
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.files-to-upload',
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
