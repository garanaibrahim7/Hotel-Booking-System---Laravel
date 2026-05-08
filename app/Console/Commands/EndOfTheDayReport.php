<?php

namespace App\Console\Commands;

use App\Mail\SendReportMailToAdmin;
use App\Models\Booking;
use App\Models\User;
use App\Services\LocationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EndOfTheDayReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:send-report';

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
        $this->info('Generating Daily Booking Report...');

        // 1. Fetch data from last 24 hours
        $bookings = Booking::with(['payment', 'items.room.details', 'hotel'])
            ->where('created_at', '>=', now()->subDay())
            ->get();

        if ($bookings->isEmpty()) {
            $this->warn('No bookings found in the last 24 hours.');

            return 0;
        }

        $reportTitle = 'Daily Bookings Report of '.now()->format('d M, Y - l');
        $userCountry = LocationService::fetchLocation();

        $pdf = Pdf::loadView('admin.booking.daily-report', compact('bookings', 'reportTitle', 'userCountry'));
        $pdfContent = $pdf->output();

        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            Mail::to($admin->email)->send(new SendReportMailToAdmin($pdfContent, $reportTitle));
            $this->info('Report sent successfully to '.$admin->email);
        } else {
            $this->error('Admin user not found.');
        }

        return 0;
    }
}
