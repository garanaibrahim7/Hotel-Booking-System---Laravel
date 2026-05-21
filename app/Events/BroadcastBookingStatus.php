<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BroadcastBookingStatus implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $bookingId;
    public $success;
    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct(Booking $booking, bool $success, string $message)
    {
        $this->bookingId = $booking->id;
        $this->success = $success;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn()
    {
        return new Channel('booking-tracker.' . $this->bookingId);
    }

    public function broadcastAs()
    {
        return 'PaymentStatusProcessed';
    }
}
