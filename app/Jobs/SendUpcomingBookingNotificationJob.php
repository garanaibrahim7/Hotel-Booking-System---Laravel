<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Notifications\BookingUpcomingNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendUpcomingBookingNotificationJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $date = now()->addDay()->format('Y-m-d');

        $items = BookingItem::where('check_in', $date)
            ->whereHas('booking', function ($query) {
                $query->where('status', Booking::STATUS_CONFIRMED);
            })
            ->with('booking.user')
            ->get();
        $items->map(function ($item) {
            return $item->booking?->user->notify(new BookingUpcomingNotification($item->booking));
        });
    }
}
