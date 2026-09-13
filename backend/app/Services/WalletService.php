<?php

namespace App\Services;

use App\Models\BalanceLog;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class WalletService
{
    public function change(User $user, string $type, float $amount, ?string $remark = null, ?string $relatedType = null, ?int $relatedId = null): BalanceLog
    {
        return DB::transaction(function () use ($user, $type, $amount, $remark, $relatedType, $relatedId) {
            $locked = User::where('id', $user->id)->lockForUpdate()->first();
            $before = (float) $locked->balance;
            $after = round($before + $amount, 2);
            if ($after < 0) {
                throw new RuntimeException('余额不足');
            }
            $locked->balance = $after;
            $locked->save();

            return BalanceLog::create([
                'user_id' => $locked->id,
                'type' => $type,
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $after,
                'related_type' => $relatedType,
                'related_id' => $relatedId,
                'remark' => $remark,
            ]);
        });
    }
}
