<?php

namespace App\Services;

use App\Enums\Status;
use App\Models\Transaction;
use App\Models\User;

class TransactionService
{
    public function __construct()
    {
    }

    public function fetchTransactions()
    {
        return Transaction::query()->latest();
    }

    public function logTransaction(
        string $reference,
        User $user,
        float $amount,
        string $transactionType,
        string $status = null,
        array $payload
    ) {
        return $user->transactions()->create([
            'reference' => $reference,
            'transaction_type' => $transactionType,
            'status' => $status,
            'amount' => $amount,
            'charge' => isset($payload['charge']) ?? 0,
            'total_amount' => $amount - isset($payload['charge']) ?? 0,
            'mode' => $payload['mode'] ?? 'SYSTEM',
        ]);
    }

    public function makePending($transaction)
    {
        return $transaction->update([
            'status' => Status::PROCESSING->value
        ]);
    }

    public function makeSuccessful($transaction)
    {
        return $transaction->update([
            'status' => Status::SUCCESSFUL->value
        ]);
    }
}
