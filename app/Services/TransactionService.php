<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TransactionService
{
    private const HASHABLE_FIELDS = [
        'transactionable_id', 'transactionable_type', 'amount',
        'converted_amount', 'currency', 'converted_currency', 'exchange_rate',
        'type', 'mode', 'note', 'tax', 'tax_amount',
    ];

    public static function create(array $data): bool
    {
        $validator = Validator::make($data, [
            'transactionable_id' => 'required',
            'transactionable_type' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'converted_amount' => 'required|numeric|min:0',
            'currency' => 'required|string|max:5',
            'converted_currency' => 'required|string|max:5',
            'exchange_rate' => 'required|numeric|min:0',
            'type' => 'required|in:credit,debit',
            'mode' => 'required|string',
            'note' => 'nullable|string',
            'tax' => 'required|numeric|min:0|max:100',
            'tax_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return false;
        }

        $validated = $validator->validated();

        // Log::channel('debug')->info('Validated Data : ', $validated);

        $lastTransactions = Transaction::latest('id')->limit(2)->get();

        if ($lastTransactions->count() === 2) {
            $newest = $lastTransactions->first(); // T(n)
            $older = $lastTransactions->last();  // T(n-1)

            if (! self::verifyHash($newest, $older->hash)) {
                Log::critical("Ledger Tampering Detected! Chain is broken at ID: {$newest->id}");

                return false;
            }
        }

        $parentHash = $lastTransactions->isEmpty() ? '' : $lastTransactions->first()->hash;
        $newHash = self::makeHash($validated, $parentHash);

        $transaction = Transaction::create([
            ...$validated,
            'hash' => $newHash,
        ]);

        Log::debug('create hash debug', [
            'parentHash' => $parentHash,
            'hashable' => self::getHashableData($validated),
            'newHash' => $newHash,
        ]);

        return (bool) $transaction;
    }

    private static function makeHash(array $data, string $prevHash = ''): string
    {
        $hashable = self::getHashableData($data);

        return hash('sha256', json_encode($hashable).$prevHash);
    }

    public static function verifyHash(Transaction $transaction, string $prevHash = ''): bool
    {
        $data = $transaction->only(self::HASHABLE_FIELDS);
        $hashable = self::getHashableData($data);

        $calculated = self::makeHash($data, $prevHash);

        Log::debug('verifyHash debug', [
            'id' => $transaction->id,
            'prevHash' => $prevHash,
            'hashable' => $hashable,
            'calculated' => hash('sha256', json_encode($hashable).$prevHash),
            'stored' => $transaction->hash,
        ]);

        return $transaction->hash === $calculated;
    }

    private static function getHashableData(array $data): array
    {
        $filtered = [];

        foreach (self::HASHABLE_FIELDS as $field) {
            $val = $data[$field] ?? '';

            $filtered[$field] = is_numeric($val) ? (float) $val : (string) $val;
        }

        ksort($filtered);

        return $filtered;
    }

    public static function verifyLedger(): bool
    {
        $transactions = Transaction::orderBy('id', 'asc')->get();

        if ($transactions->isEmpty()) {
            return true;
        }

        $previousHash = '';

        foreach ($transactions as $transaction) {

            $data = $transaction->only(self::HASHABLE_FIELDS);

            $calculatedHash = self::makeHash($data, $previousHash);

            if ($transaction->hash !== $calculatedHash) {
                Log::emergency("Ledger Integrity Failure at Transaction ID: {$transaction->id}");

                return false;
            }

            $previousHash = $transaction->hash;
        }

        return true;
    }
}
