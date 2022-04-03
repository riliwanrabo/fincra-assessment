<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\WalletService;
use App\Http\Controllers\Controller;
use App\Exceptions\MisMatchException;
use App\Contracts\FundsTransferContract;

class FundsTransferController extends Controller
{
    public function __construct(
        protected FundsTransferContract $fundsTransferContract,
        protected WalletService $walletService
    ) {
        $this->middleware('auth:api');
        $this->middleware('role:agent')->only('transfer', 'transferBulk');
    }

    public function resolveAccount(Request $request)
    {
        $request->validate([
            'account_number' => ['required', 'min:10'],
            'bank_code' => ['required',]
        ]);

        return $this->fundsTransferContract->resolveAccount(
            $request->account_number,
            $request->bank_code
        );
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'account_number' => ['required', 'min:10'],
            'bank_code' => ['required',],
            'amount' => ['required', 'numeric']
        ]);

        return $this->fundsTransferContract->transfer(
            $request->account_number,
            $request->bank_code,
            $request->amount
        );
    }

    public function transferBulk(Request $request)
    {
        $request->validate([
            'transfers' => ['required', 'array'],
            'transfers.*.account_number' => ['required', 'min:10'],
            'transfers.*.bank_code' => ['required',],
            'transfers.*.amount' => ['required', 'numeric']
        ]);

        $this->walletService->hasEnoughBalance(
            auth()->user()->wallet,
            collect($request->transfers)->sum('amount')
        ) ?: throw new MisMatchException("Insufficient Balance");

        $transfers = $request->transfers;

        dispatch(function () use ($transfers) {
            foreach ($transfers as $transfer) {
                $this->fundsTransferContract->transfer(
                    $transfer['account_number'],
                    $transfer['bank_code'],
                    $transfer['amount'],
                );
            }
        });

        return response()->json([
            'message' => __(count($transfers) . " transfer(s) processed")
        ]);
    }
}
