<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;

class InternalRuleSubmitted extends Mailable
{
    use  Queueable, SerializesModels;

    public $who;
    public $md5;
    
    /**
     * Create a new message instance.
     */
    public function __construct($who, $serverFileName)
    {
        //
        $this->who = $who;
        $this->serverFileName = $serverFileName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Υποβολή Εσωτερικού Κανονισμού '. $this->who,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.internal_rule-school-submission',
        );
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $nameAndExtension = array();
        switch ($this->serverFileName) {
            case substr($this->serverFileName, -3) === 'pdf':
                $nameAndExtension = [
                    'as' => 'ΕΚΛ_'.$this->who.'.pdf',
                    'mime' => 'application/pdf',
                ];
                break;
            case substr($this->serverFileName, -4) === 'docx':
                $nameAndExtension = [
                    'as' => 'ΕΚΛ_'.$this->who.'.docx',
                    'mime' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ];
                break;
            case substr($this->serverFileName, -3) === 'doc':
                $nameAndExtension = [
                    'as' => 'ΕΚΛ_'.$this->who.'.doc',
                    'mime' => 'application/msword',
                ];
                break;
        }
        return $this->view('emails.internal_rule-school-submitted')
                    ->with([
                        'who' => $this->who,
                    ])
                    ->attach(storage_path('app/internal_rules/'.$this->serverFileName), $nameAndExtension);
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
