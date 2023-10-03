<?php

namespace App\Mail\Accelerator;

use App\Models\Accelerator\Case\AcceleratorCaseEvent;
use App\Models\Accelerator\Case\AcceleratorCaseEventType;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventCreate extends Mailable
{
    use Queueable, SerializesModels;

    public AcceleratorCaseEvent $event;

    public function __construct(AcceleratorCaseEvent $event)
    {
        $this->event = $event;
    }

    public function envelope(): Envelope
    {
        $type = $this->event->type_id == AcceleratorCaseEventType::enter() ? 'участие в' : 'выход из';
        return new Envelope(
            subject: sprintf('Заявка на %s команде на сайте %s', $type, config('app.name')),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.accelerator.event-create',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
