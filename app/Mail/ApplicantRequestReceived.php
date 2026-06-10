<?php

namespace App\Mail;

use App\Models\ApplicantRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicantRequestReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ApplicantRequest $applicant)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Нова заявка абітурієнта: ' . $this->applicant->name,
            replyTo: $this->applicant->email ? [$this->applicant->email] : [],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.applicant');
    }
}
