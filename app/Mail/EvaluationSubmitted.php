<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EvaluationSubmitted extends Mailable
{
    use Queueable, SerializesModels;
    public $who;
    public $evaluatee;
    public $fileName;
    public $filePath;
    /**
     * Create a new message instance.
     */
    public function __construct($who, $evaluatee, $fileName, $filePath)
    {
        //
        $this->who = $who;
        $this->evaluatee = $evaluatee;
        $this->fileName = $fileName;
        $this->filePath = $filePath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Υποβολή Αξιολόγησης: '. $this->who . ' για ' . $this->evaluatee,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.evaluation-submission',
        );
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $nameAndExtension = array();
        switch ($this->fileName) {
            case substr($this->fileName, -3) === 'pdf':
                $nameAndExtension = [
                    'as' => 'Αξιολόγηση_'.$this->who.'-'.$this->evaluatee.'.pdf',
                    'mime' => 'application/pdf',
                ];
                break;
            case substr($this->serverFileName, -4) === 'docx':
                $nameAndExtension = [
                    'as' => 'Αξιολόγηση_'.$this->who.'.docx',
                    'mime' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ];
                break;
            case substr($this->serverFileName, -3) === 'doc':
                $nameAndExtension = [
                    'as' => 'Αξιολόγηση_'.$this->who.'.doc',
                    'mime' => 'application/msword',
                ];
                break;
        }
        return $this->view('emails.evaluation-submission')
                    ->with([
                        'who' => $this->who,
                        'evaluatee' => $this->evaluatee,
                    ])
                    ->attach($this->filePath, $nameAndExtension);
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
