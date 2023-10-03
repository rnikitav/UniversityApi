<?php

namespace App\Mail\Accelerator;

use App\Models\Accelerator\Case\AcceleratorCase;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CaseUpdateStatus extends Mailable
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
            subject: 'Изменение статуса заявки на кейс на сайте ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.accelerator.case-update-status',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
