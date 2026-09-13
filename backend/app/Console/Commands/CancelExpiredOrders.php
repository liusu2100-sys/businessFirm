<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class CancelExpiredOrders extends Command
{
    protected $signature = 'orders:cancel-expired';
    protected $description = 'Cancel unpaid orders past expire_at';

    public function handle(): int
    {
        $count = Order::where('status', 0)
            ->whereNotNull('expire_at')
            ->where('expire_at', '<', now())
            ->update([
                'status' => 3,
                'cancelled_at' => now(),
            ]);
        $this->info("Cancelled {$count} expired unpaid orders.");
        return self::SUCCESS;
    }
}
