<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentAccount extends Model
{
    protected $fillable = [
        'user_id', 'type', 'account_name', 'account_no', 'bank_name', 'is_default',
    ];

    protected $casts = ['is_default' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function toApiArray(): array
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'accountName' => $this->account_name,
            'accountNo' => $this->account_no,
            'bankName' => $this->bank_name,
            'isDefault' => $this->is_default,
            'createdAt' => $this->created_at?->toDateTimeString(),
        ];
    }
}
