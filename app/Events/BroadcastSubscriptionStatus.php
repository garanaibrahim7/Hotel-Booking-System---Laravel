<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BroadcastSubscriptionStatus implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sessionId;
    public $success;

    /**
     * Create a new event instance.
     */
    public function __construct($sessionId, $success)
    {
        $this->sessionId = $sessionId;
        $this->success = $success;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('subscription-tracker.'.$this->sessionId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'SubscriptionStatusProcessed';
    }
}
