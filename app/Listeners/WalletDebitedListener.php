<?php

namespace App\Listeners;

use App\Events\WalletDebited;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class WalletDebitedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\WalletDebited  $event
     * @return void
     */
    public function handle(WalletDebited $event)
    {
        $event->wallet->history()->create([
            'transaction_id' => $event->transaction?->id,
            'amount' => $event->amount,
            'type' => 'DEBIT',
            'previous_balance' => $event->previous_balance,
            'current_balance' => $event->current_balance,
            'description' => $event->description,
        ]);
    }
}
