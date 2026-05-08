<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class AlertAdminPaymentFail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(protected Payment $payment)
    {
        // Log::channel('debug')->info('Notification to Admin : '.$payment->id);

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toDatabase(object $notifiable): array
    {
        // Log::channel('debug')->info('Saving Notification for Admin: '.$notifiable->email);

        return [
            'title' => '⚠️ Payment Failed',
            'message' => 'Payment failed for booking #'.$this->payment->booking->reference_number,
            'booking_id' => $this->payment->booking_id,
            'amount' => $this->payment->converted_amount.' '.$this->payment->paid_currency,
            'user_name' => $this->payment->booking->user->name,
            'type' => 'danger', // Helpful for your luxury toast/navbar icon
            'url' => route('admin.bookings.index', ['search' => $this->payment->booking->reference_number]),
        ];
    }

    // You can keep toArray as a fallback if you use other channels like 'broadcast'
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
        ->subject('⚠️ URGENT: Payment Failed - #' . $this->payment->id)
        ->markdown('emails.admin-alert', [
            'payment' => $this->payment
        ]);
    }
}
