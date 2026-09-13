<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\PaymentAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentAccountController extends Controller
{
    public function list(Request $request)
    {
        $list = $request->user()->paymentAccounts()->orderByDesc('is_default')->orderByDesc('id')->get();
        return ApiResponse::success($list->map->toApiArray());
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'type' => 'required|in:alipay,wechat,bank',
            'accountName' => 'required|string|max:50',
            'accountNo' => 'required|string|max:100',
        ]);
        if ($v->fails()) {
            return ApiResponse::error($v->errors()->first(), 422);
        }
        $pa = PaymentAccount::create([
            'user_id' => $request->user()->id,
            'type' => $request->type,
            'account_name' => $request->accountName,
            'account_no' => $request->accountNo,
            'bank_name' => $request->bankName,
            'is_default' => (bool) $request->input('isDefault', false),
        ]);
        if ($pa->is_default) {
            PaymentAccount::where('user_id', $request->user()->id)->where('id', '!=', $pa->id)->update(['is_default' => false]);
        }
        return ApiResponse::success($pa->toApiArray(), '添加成功');
    }

    public function update(Request $request, $id)
    {
        $pa = PaymentAccount::where('user_id', $request->user()->id)->find($id);
        if (!$pa) {
            return ApiResponse::error('不存在', 404);
        }
        $pa->fill([
            'type' => $request->input('type', $pa->type),
            'account_name' => $request->input('accountName', $pa->account_name),
            'account_no' => $request->input('accountNo', $pa->account_no),
            'bank_name' => $request->input('bankName', $pa->bank_name),
        ]);
        $pa->save();
        return ApiResponse::success($pa->toApiArray(), '更新成功');
    }

    public function destroy(Request $request, $id)
    {
        PaymentAccount::where('user_id', $request->user()->id)->where('id', $id)->delete();
        return ApiResponse::success(null, '已删除');
    }

    public function setDefault(Request $request, $id)
    {
        $pa = PaymentAccount::where('user_id', $request->user()->id)->find($id);
        if (!$pa) {
            return ApiResponse::error('不存在', 404);
        }
        PaymentAccount::where('user_id', $request->user()->id)->update(['is_default' => false]);
        $pa->is_default = true;
        $pa->save();
        return ApiResponse::success(null, '已设为默认');
    }
}
