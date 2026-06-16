<?php

namespace App\Console\Commands;

use App\Jobs\SendUpcomingBookingNotificationJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendUpcomingBookingNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-booking-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        SendUpcomingBookingNotificationJob::dispatch();
        Log::channel()->info('Running Upcoming Booking Notification Command');
    }
}
