<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'avatar' => $this->avatar,
            'role' => $this->roles()->first()?->name,
            'wallet' => WalletResource::make($this->wallet),
            'statistics' => [
                'total_transaction_count' => $this->transactions()->count(),
                'successful_transaction_count' => $this->transactions()->successfulTransactions()->count(),
                'failed_transaction_count' => $this->transactions()->failedTransactions()->count(),
            ]
        ];
    }
}
