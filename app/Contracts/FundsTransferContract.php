<?php

namespace App\Contracts;

interface FundsTransferContract
{
    public function resolveAccount(string $accountNumber, string $bankCode);
    public function transfer(string $accountNumber, string $bankCode, float $amount);
}
