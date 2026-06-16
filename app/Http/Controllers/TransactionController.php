<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Transaction;
use App\Services\TransactionService;

class TransactionController extends Controller
{
    public function index($currency = 'usd')
    {
        if (TransactionService::verifyLedger()) {

            $currencies = Country::all('name', 'currency_code')->pluck('currency_code', 'name');

            $exchangeRate = currencyExchangeRate($currency);
            $transactions = Transaction::with('transactionable')
                ->latest('id')
                ->paginate(20)
                ->through(function ($transaction) use ($currency) {
                    $exchangeRate = currencyExchangeRate($currency, $transaction->currency);
                    $transaction->amount = $transaction->amount * $exchangeRate;

                    return $transaction;
                });

            $totalCredit = Transaction::where('type', 'credit')
                ->get()
                ->sum(function ($transaction) use ($currency) {
                    $exchangeRate = currencyExchangeRate($currency, $transaction->currency);

                    return $transaction->amount * $exchangeRate;
                });
            $totalDebit = Transaction::where('type', 'debit')
                ->get()
                ->sum(function ($transaction) use ($currency) {
                    $exchangeRate = currencyExchangeRate($currency, $transaction->currency);

                    return $transaction->amount * $exchangeRate;
                });

            $verified = true;

            return view('admin.transactions.list', compact('transactions', 'verified', 'totalCredit', 'totalDebit', 'currency', 'currencies'));
        }

        return view('admin.transactions.list', ['verified' => false]);
    }
}
