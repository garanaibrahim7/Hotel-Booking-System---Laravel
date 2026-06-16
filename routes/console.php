<?php

use App\Console\Commands\SendUpcomingBookingNotification;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:send-booking-notification')
    // ->everyMinute()
    // ->dailyAt('18:07')
    ->runInBackground();
