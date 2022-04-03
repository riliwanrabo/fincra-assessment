<?php

namespace App\Enums;

enum TransactionType: string
{
    case TRANSFER = 'transfer';
    case WALLET_TRANSFER = 'wallet-transfer';
    case DEPOSIT = 'deposit';
}
