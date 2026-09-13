<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BalanceLog extends Model
{
    protected $fillable = [
        'user_id', 'type', 'amount', 'balance_before', 'balance_after',
        'related_type', 'related_id', 'remark',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function toApiArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'amount' => $this->amount,
            'balanceBefore' => $this->balance_before,
            'balanceAfter' => $this->balance_after,
            'relatedType' => $this->related_type,
            'relatedId' => $this->related_id,
            'remark' => $this->remark,
            'createdAt' => $this->created_at?->toDateTimeString(),
        ];
    }
}
