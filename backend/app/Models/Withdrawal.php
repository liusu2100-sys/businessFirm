<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $fillable = [
        'user_id', 'payment_account_id', 'amount', 'fee', 'actual_amount',
        'status', 'remark', 'admin_remark', 'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fee' => 'decimal:2',
        'actual_amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentAccount()
    {
        return $this->belongsTo(PaymentAccount::class);
    }

    public function toApiArray(): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'fee' => $this->fee,
            'actualAmount' => $this->actual_amount,
            'status' => $this->status,
            'remark' => $this->remark,
            'adminRemark' => $this->admin_remark,
            'processedAt' => optional($this->processed_at)?->toDateTimeString(),
            'createdAt' => $this->created_at?->toDateTimeString(),
            'paymentAccount' => $this->paymentAccount,
        ];
    }
}
