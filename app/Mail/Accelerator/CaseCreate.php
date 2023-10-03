<?php

namespace App\Mail\Accelerator;

use App\Models\Accelerator\Case\AcceleratorCase;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CaseCreate extends Mailable
{
    use Queueable, SerializesModels;

    public AcceleratorCase $case;

    public function __construct(AcceleratorCase $case)
    {
        $this->case = $case;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Заявка на кейс на сайте ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.accelerator.case-create',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
