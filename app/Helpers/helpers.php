<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

function convertCurrency($amount, $toCurrency, $fromCurrency = 'USD')
{
    return round($amount * currencyExchangeRate($toCurrency, $fromCurrency), 4);
}

function currencyExchangeRate($toCurrency, $fromCurrency = 'USD')
{
    try {
        return Cache::remember(
            "rate_{$fromCurrency}_{$toCurrency}",
            86400,
            function () use ($fromCurrency, $toCurrency) {
                $url = "https://hexarate.paikama.co/api/rates/{$fromCurrency}/{$toCurrency}/latest";

                $response = Http::get($url);

                if ($response->successful()) {
                    return $response['data']['mid'] ?? 1;
                } else {
                    return 1;
                }
            });
    } catch (Exception $e) {
        Log::channel('failures')->warning('Currency Exchange API Failed');

        return 1;
    }
}
