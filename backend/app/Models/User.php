<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'phone', 'nickname', 'avatar', 'email', 'password',
        'balance', 'role', 'status', 'real_name', 'id_card',
    ];

    protected $hidden = ['password', 'remember_token', 'id_card'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'balance' => 'decimal:2',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function gameAccounts()
    {
        return $this->hasMany(GameAccount::class);
    }

    public function buyOrders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    public function sellOrders()
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    public function paymentAccounts()
    {
        return $this->hasMany(PaymentAccount::class);
    }

    public function balanceLogs()
    {
        return $this->hasMany(BalanceLog::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }
}
