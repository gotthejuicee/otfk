<?php

namespace App\Mail;

use App\Models\FeedbackMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FeedbackReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public FeedbackMessage $feedback)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Нове звернення з сайту' . ($this->feedback->subject ? ': ' . $this->feedback->subject : ''),
            replyTo: $this->feedback->email ? [$this->feedback->email] : [],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.feedback');
    }
}
