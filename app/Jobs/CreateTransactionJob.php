<?php

namespace App\Jobs;

use App\Models\Booking;
use App\Services\TransactionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CreateTransactionJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected array $payload)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        TransactionService::create($this->payload);
    }
}
