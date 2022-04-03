<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wallet extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    protected $guarded = ['null'];

    protected $casts = [
        'previous_balance' => 'float',
        'current_balance' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function history()
    {
        return $this->hasMany(WalletHistory::class, 'wallet_id');
    }
}
