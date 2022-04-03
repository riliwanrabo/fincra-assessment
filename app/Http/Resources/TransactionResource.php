<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'reference' => $this->reference,
            'provider_reference' => $this->provider_reference,
            'agent' => $this->user->first_name . ' ' . $this->user->last_name,
            'amount' => $this->total_amount,
            'previous_balance' => $this->history->previous_balance,
            'current_balance' => $this->history->current_balance,
            'status' => $this->status,
            'mode' => $this->history->type,
            'transaction_type' => $this->transaction_type,
            'transaction_date' => $this->created_at->toDateTimeString()
        ];
    }
}
