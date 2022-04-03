<?php

namespace App\Http\Controllers\Api;

use App\Enums\Status;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\TransactionService;

class WebhookController extends Controller
{
    public function __construct(protected TransactionService $transactionService)
    {
    }
    public function fincra(Request $request)
    {
        $event = $request->event;

        $data = (object)$request->data;

        switch ($event) {
            case 'payout.successful':
                $transaction = Transaction::whereProviderReference($data->reference)->first();

                if (!$transaction) return;

                $transaction->update([
                    'status' => Status::SUCCESSFUL->value
                ]);

                break;

            case 'payout.failed':
                $transaction = Transaction::whereProviderReference($data->reference)->first();

                if (!$transaction) return;

                $transaction->update([
                    'status' => Status::FAILED->value
                ]);
                break;

            default:
                # code...
                break;
        }
    }
}
