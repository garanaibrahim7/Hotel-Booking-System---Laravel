<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendReportMailToAdmin extends Mailable
{
    use SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public $pdfContent, public $reportTitle)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->reportTitle,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin.daily-report',
        );
    }

    public function attachments(): array
    {
        // return [];
        return [
            Attachment::fromData(fn () => $this->pdfContent, 'Daily_Report_of_'.now()->format('d-M-y').'.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
