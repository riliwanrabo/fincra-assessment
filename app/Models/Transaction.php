<?php

namespace App\Models;

use App\Enums\Status;
use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    protected $guarded = ['null'];

    protected $casts = [
        'amount' => 'float',
        'charge' => 'float',
        'total_amount' => 'float'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function history()
    {
        return $this->hasOne(WalletHistory::class);
    }

    // scopes
    public function scopeSuccessfulTransactions(Builder $transactions)
    {
        return $transactions->whereStatus(Status::SUCCESSFUL->value);
    }

    public function scopeFailedTransactions(Builder $transactions)
    {
        return $transactions->whereStatus(Status::FAILED->value);
    }
}
