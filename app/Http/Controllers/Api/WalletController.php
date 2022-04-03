<?php

namespace App\Http\Controllers\Api;

use App\Enums\Status;
use App\Enums\TransactionType;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Services\WalletService;
use App\Http\Controllers\Controller;
use App\Http\Resources\WalletResource;
use App\Services\TransactionService;

class WalletController extends Controller
{
    public function __construct(
        protected WalletService $walletService,
        protected TransactionService $transactionService,
    ) {
        $this->middleware('auth:api');
        $this->middleware('role:admin')->only('fundWallet', 'debitWallet', 'update', 'destroy');
    }

    public function fundWallet(Request $request)
    {
        $request->validate([
            'wallet_id' => ['required',],
            'amount' => ['required', 'numeric']
        ]);

        $wallet = Wallet::where('id', $request->wallet_id)
            ->orWhere('reference', $request->wallet_id)
            ->firstOrFail();

        $reference = generate_reference();

        $transactionMeta = [
            'mode' => null
        ];

        $transaction = $this->transactionService->logTransaction(
            $reference,
            $wallet->user,
            $request->amount,
            TransactionType::DEPOSIT->value,
            Status::SUCCESSFUL->value,
            $transactionMeta
        );

        $fundedWallet = $this->walletService->creditWallet($wallet, $request->amount, $transaction);

        return response()->ok([
            'wallet' => WalletResource::make($fundedWallet),
            'message' => __("Wallet funded")
        ]);
    }

    public function debitWallet(Request $request)
    {
        $request->validate([
            'wallet_id' => ['required',],
            'amount' => ['required', 'numeric']
        ]);

        $wallet = Wallet::where('id', $request->wallet_id)
            ->orWhere('reference', $request->wallet_id)
            ->firstOrFail();

        $fundedWallet = $this->walletService->debitWallet($wallet, $request->amount);

        return response()->ok([
            'wallet' => WalletResource::make($fundedWallet),
            'message' => __("Wallet debited")
        ]);
    }

    public function walletTransfer(Request $request)
    {
        $request->validate([
            'sender_wallet_id' => ['required'],
            'receiver_wallet_id' => ['required'],
            'amount' => ['required', 'numeric', 'min:1']
        ]);

        $this->walletService->walletTransfer(
            $request->sender_wallet_id,
            $request->receiver_wallet_id,
            $request->amount
        );

        return response()->ok([
            'message' => __("Wallet to wallet transfer successful")
        ]);
    }
}
