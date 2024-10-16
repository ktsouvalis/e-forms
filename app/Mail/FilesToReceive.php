<?php

namespace App\Mail;

use App\Models\Fileshare;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\FileshareStakeholder;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class FilesToReceive extends Mailable implements ShouldQueue
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
    public function __construct(public Fileshare $fileshare, public FileshareStakeholder $stakeholder, $username='system')
    {
        //
        $this->name = $this->fileshare->name;
        $this->username = $username;
    }
    
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Αρχεία για παραλαβή: $this->name",
            tags:['new-fileshare'],
            metadata:[
                'fileshare_id' => $this->fileshare->id
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.files-to-receive',
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
