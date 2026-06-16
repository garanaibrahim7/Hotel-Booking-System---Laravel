<?php

namespace App\Notifications;

use App\Models\SubscriptionPlans;
use App\Models\Subscriptions;
use App\Models\SubscriptionsHistory;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionConfirmNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $user;

    public $plan;

    public $subscription;

    public $history;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, SubscriptionPlans $plan, Subscriptions $subscription, SubscriptionsHistory $history)
    {
        $this->user = $user;
        $this->plan = $plan;
        $this->subscription = $subscription;
        $this->history = $history;
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
        return (new MailMessage)
            ->subject('Welcome to Premium! Your Subscription is Active')
            // Make sure this path matches where you saved your blade file (e.g., resources/views/emails/)
            ->view('emails.subscription.confirm', [
                'user' => $this->user,
                'plan' => $this->plan,
                'subscription' => $this->subscription,
                'history' => $this->history,
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
}
