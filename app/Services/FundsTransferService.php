<?php

namespace App\Services;

use App\Enums\Status;
use App\Models\BankAccount;
use App\Models\Transaction;
use App\Enums\TransactionType;
use Illuminate\Support\Facades\DB;
use App\Exceptions\MisMatchException;
use App\Services\Fincra\FincraService;
use App\Contracts\FundsTransferContract;
use App\Http\Resources\TransactionResource;

class FundsTransferService implements FundsTransferContract
{
    private string $ftService;
    private $user;
    public function __construct(
        protected FincraService $fincraService,
        protected TransactionService $transactionService,
        protected WalletService $walletService,
    ) {
        $this->ftService = env('FT_SERVICE', 'fincra');
        $this->user = auth()->user();
    }
    public function resolveAccount(string $accountNumber, string $bankCode)
    {
        switch ($this->ftService) {
            case 'fincra':
                $bankAccount = BankAccount::where([
                    'account_number' => $accountNumber,
                    'bank_code' => $bankCode
                ])->first();

                if (!$bankAccount) {
                    $response = $this->fincraService->resolveAccount(
                        $accountNumber,
                        $bankCode
                    );

                    return BankAccount::updateOrCreate([
                        'account_number' => $response->accountNumber,
                        'bank_code' => $bankCode,
                        'account_name' => $response->accountName
                    ]);
                }

                return $bankAccount;


                break;
        }
    }

    public function transfer(string $accountNumber, string $bankCode, float $amount)
    {
        switch ($this->ftService) {
            case 'fincra':

                $bankAccount = BankAccount::where([
                    'account_number' => $accountNumber,
                    'bank_code' => $bankCode
                ])->first();

                $bankAccount ?: throw new MisMatchException("Resolve Account before payout");

                $reference = generate_reference();

                $fundsTransferResponse  = DB::transaction(function () use ($reference, $accountNumber, $bankAccount, $bankCode, $amount) {
                    $transaction = $this->transactionService->logTransaction(
                        $reference,
                        $this->user,
                        $amount,
                        TransactionType::TRANSFER->value,
                        Status::PROCESSING->value,
                        []
                    );

                    $this->walletService->debitWallet(
                        $this->user->wallet,
                        $amount,
                        $transaction
                    );

                    return $this->fincraService->transfer(
                        $reference,
                        $accountNumber,
                        $bankCode,
                        $amount,
                        $bankAccount
                    );
                }, 5);


                $transaction = Transaction::whereReference($reference)->first();

                $transaction->update([
                    'provider_reference' => $fundsTransferResponse->reference
                ]);

                return TransactionResource::make($transaction->refresh());

                break;
        }
    }
}
