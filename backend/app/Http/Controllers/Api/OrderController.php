<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\GameAccount;
use App\Models\Order;
use App\Models\SiteConfig;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function list(Request $request)
    {
        $type = $request->input('type', 'buy');
        $q = Order::with(['gameAccount.user', 'buyer', 'seller']);
        if ($type === 'sell') {
            $q->where('seller_id', $request->user()->id);
        } else {
            $q->where('buyer_id', $request->user()->id);
        }
        if ($request->filled('status') && $request->status !== '') {
            $q->where('status', $request->status);
        }
        $page = $q->orderByDesc('id')->paginate($request->input('pageSize', 20));
        return ApiResponse::success([
            'list' => collect($page->items())->map->toApiArray(),
            'total' => $page->total(),
        ]);
    }

    public function store(Request $request)
    {
        $accountId = $request->input('gameAccountId');
        $account = GameAccount::where('status', 1)->find($accountId);
        if (!$account) {
            return ApiResponse::error('商品不存在或未上架', 404);
        }
        if ($account->user_id === $request->user()->id) {
            return ApiResponse::error('不能租赁自己的账号', 400);
        }

        $rentDays = (int) ($request->input('rentDays') ?: $account->rent_days ?: 1);
        $price = (float) $account->price;
        $deposit = (float) $account->deposit;
        $commissionRate = (float) SiteConfig::getValue('commission_rate', 0.05);
        $total = $price + $deposit;
        $commission = round($price * $commissionRate, 2);

        $order = Order::create([
            'order_no' => 'SS' . date('YmdHis') . Str::upper(Str::random(4)),
            'game_account_id' => $account->id,
            'buyer_id' => $request->user()->id,
            'seller_id' => $account->user_id,
            'price' => $price,
            'deposit' => $deposit,
            'total_amount' => $total,
            'commission' => $commission,
            'rent_days' => $rentDays,
            'status' => 0,
            'expire_at' => now()->addMinutes((int) SiteConfig::getValue('order_pay_timeout_minutes', 30)),
            'remark' => $request->remark,
        ]);

        return ApiResponse::success($order->load(['gameAccount', 'buyer', 'seller'])->toApiArray(), '订单已创建，请支付');
    }

    public function pay(Request $request, $id, WalletService $wallet)
    {
        $order = Order::where('buyer_id', $request->user()->id)->find($id);
        if (!$order) {
            return ApiResponse::error('订单不存在', 404);
        }
        if ($order->status != 0) {
            return ApiResponse::error('订单状态不可支付', 400);
        }
        if ($order->expire_at && $order->expire_at->isPast()) {
            $order->status = 3;
            $order->cancelled_at = now();
            $order->save();
            return ApiResponse::error('订单已超时取消', 400);
        }

        try {
            DB::transaction(function () use ($order, $wallet, $request) {
                $wallet->change($request->user()->fresh(), 'pay', -(float) $order->total_amount, '支付订单 ' . $order->order_no, 'order', $order->id);
                $order->status = 1;
                $order->paid_at = now();
                $order->save();
                // 下架商品防止重复租
                GameAccount::where('id', $order->game_account_id)->update(['status' => 2]);
            });
        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage(), 400);
        }

        return ApiResponse::success($order->fresh()->load(['gameAccount', 'buyer', 'seller'])->toApiArray(), '支付成功');
    }

    public function complete(Request $request, $id, WalletService $wallet)
    {
        $order = Order::find($id);
        if (!$order) {
            return ApiResponse::error('订单不存在', 404);
        }
        $uid = $request->user()->id;
        if ($order->buyer_id !== $uid && $order->seller_id !== $uid && !$request->user()->isAdmin()) {
            return ApiResponse::error('无权操作', 403);
        }
        if ($order->status != 1) {
            return ApiResponse::error('订单状态不可完成', 400);
        }

        try {
            DB::transaction(function () use ($order, $wallet) {
                $seller = $order->seller()->first();
                $income = (float) $order->price - (float) $order->commission;
                $wallet->change($seller, 'income', $income, '订单收入 ' . $order->order_no, 'order', $order->id);
                // 退还押金给买家
                if ((float) $order->deposit > 0) {
                    $buyer = $order->buyer()->first();
                    $wallet->change($buyer, 'refund', (float) $order->deposit, '退还押金 ' . $order->order_no, 'order', $order->id);
                }
                $order->status = 5;
                $order->completed_at = now();
                $order->save();
            });
        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage(), 400);
        }

        return ApiResponse::success($order->fresh()->toApiArray(), '订单已完成');
    }

    public function earlySettle(Request $request, $id, WalletService $wallet)
    {
        // 提前结算：同 complete，可按比例结算租金
        return $this->complete($request, $id, $wallet);
    }

    public function cancel(Request $request, $id, WalletService $wallet)
    {
        $order = Order::find($id);
        if (!$order) {
            return ApiResponse::error('订单不存在', 404);
        }
        $uid = $request->user()->id;
        if ($order->buyer_id !== $uid && !$request->user()->isAdmin()) {
            return ApiResponse::error('无权操作', 403);
        }

        if ($order->status == 0) {
            $order->status = 3;
            $order->cancelled_at = now();
            $order->save();
            return ApiResponse::success(null, '订单已取消');
        }

        if ($order->status == 1) {
            try {
                DB::transaction(function () use ($order, $wallet) {
                    $buyer = $order->buyer()->first();
                    $wallet->change($buyer, 'refund', (float) $order->total_amount, '取消退款 ' . $order->order_no, 'order', $order->id);
                    $order->status = 4;
                    $order->cancelled_at = now();
                    $order->save();
                    GameAccount::where('id', $order->game_account_id)->update(['status' => 1]);
                });
            } catch (\Throwable $e) {
                return ApiResponse::error($e->getMessage(), 400);
            }
            return ApiResponse::success(null, '已退款取消');
        }

        return ApiResponse::error('当前状态不可取消', 400);
    }
}
