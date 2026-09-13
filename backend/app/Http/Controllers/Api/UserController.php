<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\SiteConfig;
use App\Models\Withdrawal;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function profile(Request $request)
    {
        $u = $request->user();
        return ApiResponse::success([
            'id' => $u->id,
            'phone' => $u->phone,
            'nickname' => $u->nickname,
            'avatar' => $u->avatar,
            'balance' => $u->balance,
            'role' => $u->role,
            'realName' => $u->real_name,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $u = $request->user();
        $u->fill($request->only(['nickname', 'avatar', 'real_name']));
        if ($request->has('realName')) {
            $u->real_name = $request->realName;
        }
        $u->save();
        return ApiResponse::success(null, '更新成功');
    }

    public function updatePassword(Request $request)
    {
        $v = Validator::make($request->all(), [
            'oldPassword' => 'required',
            'newPassword' => 'required|min:6',
        ]);
        if ($v->fails()) {
            return ApiResponse::error($v->errors()->first(), 422);
        }
        $u = $request->user();
        if (!Hash::check($request->oldPassword, $u->password)) {
            return ApiResponse::error('原密码错误', 400);
        }
        $u->password = $request->newPassword;
        $u->save();
        return ApiResponse::success(null, '密码已修改');
    }

    public function balanceLog(Request $request)
    {
        $logs = $request->user()->balanceLogs()->orderByDesc('id')
            ->paginate($request->input('pageSize', 20));
        return ApiResponse::success([
            'list' => collect($logs->items())->map->toApiArray(),
            'total' => $logs->total(),
            'page' => $logs->currentPage(),
            'pageSize' => $logs->perPage(),
        ]);
    }

    public function withdrawal(Request $request, WalletService $wallet)
    {
        $v = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'paymentAccountId' => 'required|integer',
        ]);
        if ($v->fails()) {
            return ApiResponse::error($v->errors()->first(), 422);
        }

        $user = $request->user();
        $pa = $user->paymentAccounts()->find($request->paymentAccountId);
        if (!$pa) {
            return ApiResponse::error('收款账户不存在', 404);
        }

        $feeRate = (float) (SiteConfig::getValue('withdraw_fee_rate', 0.02));
        $amount = (float) $request->amount;
        $fee = round($amount * $feeRate, 2);
        $minWithdraw = (float) (SiteConfig::getValue('min_withdraw', 10));
        if ($amount < $minWithdraw) {
            return ApiResponse::error("最低提现{$minWithdraw}元", 400);
        }
        if ($user->balance < $amount) {
            return ApiResponse::error('余额不足', 400);
        }

        try {
            $wallet->change($user, 'withdraw', -$amount, '申请提现', 'withdrawal', null);
        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage(), 400);
        }

        $w = Withdrawal::create([
            'user_id' => $user->id,
            'payment_account_id' => $pa->id,
            'amount' => $amount,
            'fee' => $fee,
            'actual_amount' => $amount - $fee,
            'status' => 0,
            'remark' => $request->remark,
        ]);

        // link related id
        $user->balanceLogs()->where('type', 'withdraw')->whereNull('related_id')->latest('id')->first()
            ?->update(['related_id' => $w->id]);

        return ApiResponse::success($w->toApiArray(), '提现申请已提交');
    }

    public function withdrawalList(Request $request)
    {
        $list = $request->user()->withdrawals()->with('paymentAccount')->orderByDesc('id')
            ->paginate($request->input('pageSize', 20));
        return ApiResponse::success([
            'list' => collect($list->items())->map->toApiArray(),
            'total' => $list->total(),
        ]);
    }
}
