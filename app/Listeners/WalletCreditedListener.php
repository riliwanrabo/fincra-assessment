<?php

namespace App\Listeners;

use App\Events\WalletCredited;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class WalletCreditedListener
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
     * @param  \App\Events\WalletCredited  $event
     * @return void
     */
    public function handle(WalletCredited $event)
    {
        $event->wallet->history()->create([
            'transaction_id' => $event->transaction?->id,
            'amount' => $event->amount,
            'type' => 'CREDIT',
            'previous_balance' => $event->previous_balance,
            'current_balance' => $event->current_balance,
            'description' => $event->description,
        ]);
    }
}
