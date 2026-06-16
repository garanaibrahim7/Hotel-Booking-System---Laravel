<?php

namespace App\Notifications;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class BookingConfirmNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected Booking $booking)
    {
        //$this->booking->loadMissing(['user', 'hotel', 'payment']);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $this->booking->loadMissing(['user', 'hotel.city.state.country', 'items.room.details', 'payment']);

        $pdf = Pdf::loadView('client.payment.print-invoice', ['payment' => $this->booking->payment]);
        $pdf->setPaper('a4', 'portrait');

        $pdfData = $pdf->output();

        Log::channel('debug')->alert("Mail sent To {$notifiable->email}, Regarding Booking Reference : {$this->booking->reference_number}, View Booking URL: " . url('/booking/view/' . $this->booking->reference_number));

        return (new MailMessage)
            // ->mailer('mailtrap2')
            ->subject("Your Booking Confirmed - #" . $this->booking->reference_number)
            ->view('emails.bookings.confirmed', [
                'booking' => $this->booking,
                'user'    => $notifiable,
            ])
            ->attachData($pdfData, "invoice_{$this->booking->reference_number}.pdf", [
                'mime' => 'application/pdf',
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    public function failed(\Throwable $exception)
    {
        \Log::channel('failures')->critical('Confirm Booking Notification Failed, Exception Message : '.$exception->getMessage());
    }
}
