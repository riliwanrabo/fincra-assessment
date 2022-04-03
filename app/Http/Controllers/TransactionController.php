<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(protected TransactionService $transactionService)
    {
        $this->middleware('auth:api');
        $this->middleware('role:admin')->only('index');
    }

    public function index()
    {
        $transactions = $this->transactionService->fetchTransactions();

        return TransactionResource::collection($transactions->paginate());
    }
}
