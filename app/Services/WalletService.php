<?php

namespace App\Services;

use App\Models\User;

use App\Enums\Status;
use App\Models\Wallet;
use App\Enums\TransactionType;
use App\Events\WalletCredited;
use App\Events\WalletDebited;
use Illuminate\Support\Facades\DB;

use App\Services\TransactionService;
use Illuminate\Support\Facades\Gate;
use App\Exceptions\MisMatchException;

use App\Exceptions\UnAuthorizedException;
use App\Models\Transaction;

class WalletService
{
    public function __construct(protected TransactionService $transactionService)
    {
    }

    public function creditWallet(Wallet $wallet, float $amount, Transaction $transaction)
    {
        DB::transaction(function () use ($wallet, $amount, $transaction) {

            $wallet->lockForUpdate();

            $previous_balance = $wallet->previous_balance;

            $wallet->previous_balance = $wallet->current_balance;

            $wallet->increment('current_balance', $amount);

            event(new WalletCredited(
                $wallet,
                $amount,
                $previous_balance,
                $wallet->current_balance,
                "",
                $transaction
            ));

            $wallet->save();
        }, 5);

        return $wallet;
    }

    public function debitWallet(Wallet $wallet, float $amount, Transaction $transaction)
    {
        DB::transaction(function () use ($wallet, $amount, $transaction) {

            $wallet->lockForUpdate();

            $previous_balance = $wallet->previous_balance;

            $wallet->previous_balance = $wallet->current_balance;

            $wallet->decrement('current_balance', $amount);

            event(new WalletDebited(
                $wallet,
                $amount,
                $previous_balance,
                $wallet->current_balance,
                "",
                $transaction
            ));

            $wallet->save();
        }, 5);

        return $wallet;
    }

    public function walletTransfer(string $senderWalletId, string $receiverWalletId, float $amount)
    {
        $senderWalletId === $receiverWalletId ?? throw new MisMatchException("Sender and Receiver wallets are same");

        $senderWallet = Wallet::whereId($senderWalletId)->orWhere('reference', $senderWalletId)->firstOrFail();
        $receiverWallet = Wallet::whereId($receiverWalletId)->orWhere('reference', $receiverWalletId)->firstOrFail();

        if (!Gate::allows('do-wallet-transfer', $senderWallet->user->id)) {
            throw new UnAuthorizedException("Not your wallet");
        }

        DB::transaction(function () use ($senderWallet, $amount, $receiverWallet) {
            if ($this->hasEnoughBalance($senderWallet, $amount)) {

                $reference = generate_reference();

                $transactionMeta = [
                    'mode' => 'user',
                    'charge' => 0
                ];

                $senderTransactionEntry = $this->transactionService->logTransaction(
                    $reference,
                    $senderWallet->user,
                    $amount,
                    TransactionType::WALLET_TRANSFER->value,
                    Status::SUCCESSFUL->value,
                    $transactionMeta
                );

                $receiverTransactionEntry = $this->transactionService->logTransaction(
                    $reference,
                    $receiverWallet->user,
                    $amount,
                    TransactionType::WALLET_TRANSFER->value,
                    Status::SUCCESSFUL->value,
                    $transactionMeta
                );

                $creditedWallet =  $this->creditWallet($receiverWallet, $amount, $receiverTransactionEntry);

                $this->debitWallet($senderWallet, $amount, $senderTransactionEntry);

                return $creditedWallet;
            } else {
                throw new MisMatchException("Insufficient wallet balance");
            }
        }, 5);
    }

    public function hasEnoughBalance(Wallet $wallet, float $amount)
    {
        $wallet->lockForUpdate();

        return $wallet->current_balance >= $amount;
    }
}
