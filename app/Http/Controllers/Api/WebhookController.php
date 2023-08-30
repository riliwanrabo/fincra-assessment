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
        $signature = $request->header('signature');

        # log type of event and request data
        info("event: $event, \ndata: $request");

        $data = (object)$request->data;

        switch ($event) {
            case 'payout.successful':

                $this->validateWebhook($request->all(), $signature);

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

            case 'virtualaccount.approved':
                info("webhook recieved for va creation\n signature: $signature");

                $this->validateWebhook($request->all(), $signature);
                break;

            case 'collection.successful':
                info("webhook recieved for collection\n signature: $signature");

                $this->validateWebhook($request->all(), $signature);
                break;

                // checkout
            case 'charge.successful':

                info("webhook recieved for checkout\n signature: $signature");

                $this->validateWebhook($request->all(), $signature);

                break;

            default:
                # code...
                break;
        }
    }

    protected function validateWebhook($data, $signature)
    {
        $jsonData = collect($data)->toJson();
        $key = env('FINCRA_WEBHOOK_KEY');

        $hash = hash_hmac('sha512', $jsonData, $key);

        info("data before hashing: $jsonData\n\n signature for comparison: $signature\n\n hash: $hash");

        ($hash === $signature) ? info("***** successful webhook validation *****") : info("***** failed webhook validation *****");
    }
}
