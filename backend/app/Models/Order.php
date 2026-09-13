<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_no', 'game_account_id', 'buyer_id', 'seller_id',
        'price', 'deposit', 'total_amount', 'commission', 'rent_days',
        'status', 'paid_at', 'completed_at', 'cancelled_at', 'expire_at', 'remark',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'deposit' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'commission' => 'decimal:2',
        'paid_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'expire_at' => 'datetime',
    ];

    public function gameAccount()
    {
        return $this->belongsTo(GameAccount::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function toApiArray(): array
    {
        return [
            'id' => $this->id,
            'orderNo' => $this->order_no,
            'gameAccountId' => $this->game_account_id,
            'buyerId' => $this->buyer_id,
            'sellerId' => $this->seller_id,
            'price' => $this->price,
            'deposit' => $this->deposit,
            'totalAmount' => $this->total_amount,
            'commission' => $this->commission,
            'rentDays' => $this->rent_days,
            'status' => $this->status,
            'paidAt' => optional($this->paid_at)?->toDateTimeString(),
            'completedAt' => optional($this->completed_at)?->toDateTimeString(),
            'cancelledAt' => optional($this->cancelled_at)?->toDateTimeString(),
            'expireAt' => optional($this->expire_at)?->toDateTimeString(),
            'remark' => $this->remark,
            'createdAt' => $this->created_at?->toDateTimeString(),
            'gameAccount' => $this->gameAccount ? $this->gameAccount->toApiArray($this->status >= 1 && $this->status != 3) : null,
            'buyer' => $this->buyer ? ['id' => $this->buyer->id, 'nickname' => $this->buyer->nickname, 'phone' => $this->buyer->phone] : null,
            'seller' => $this->seller ? ['id' => $this->seller->id, 'nickname' => $this->seller->nickname, 'phone' => $this->seller->phone] : null,
        ];
    }
}
