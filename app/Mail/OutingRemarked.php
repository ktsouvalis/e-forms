<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OutingRemarked extends Mailable
{
    use SerializesModels;

    public function __construct(public $outing, public $remark) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Παρατήρηση της Διεύθυνσης στην εκδρομή ' . $this->outing->destination,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.outing-remarked');
    }
}