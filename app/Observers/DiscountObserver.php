<?php

namespace App\Observers;

use App\Models\Discount;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DiscountObserver
{
    /**
     * Handle the Discount "created" event.
     */

    private function clearCache($discount)
    {
        // $cacheKey = "city_specials_city_{$userCityId}_curr_{$currencyCode}";
        // Log::channel('debug')->info('Cache is being Clear');
        Cache::clear();
    }


    public function created(Discount $discount): void
    {
        $this->clearCache($discount);
    }

    /**
     * Handle the Discount "updated" event.
     */
    public function updated(Discount $discount): void
    {
        $this->clearCache($discount);
    }

    /**
     * Handle the Discount "deleted" event.
     */
    public function deleted(Discount $discount): void
    {
        $this->clearCache($discount);
    }

    /**
     * Handle the Discount "restored" event.
     */
    public function restored(Discount $discount): void
    {
        $this->clearCache($discount);
    }

    /**
     * Handle the Discount "force deleted" event.
     */
    public function forceDeleted(Discount $discount): void
    {
        $this->clearCache($discount);
    }
}
