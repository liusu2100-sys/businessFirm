<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\GameAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GameAccountController extends Controller
{
    private function mapInput(array $data): array
    {
        $map = [
            'accountNo' => 'account_no', 'loginType' => 'login_type', 'accountPwd' => 'account_pwd',
            'havCoin' => 'hav_coin', 'insuranceBox' => 'insurance_box',
            'staminaLevel' => 'stamina_level', 'weightLevel' => 'weight_level', 'fhLevel' => 'fh_level',
            'kdValue' => 'kd_value', 'awmAmmo' => 'awm_ammo', 'helmetL6' => 'helmet_l6',
            'armorL6' => 'armor_l6', 'slot9Card' => 'slot9_card',
            'meleeSkins' => 'melee_skins', 'weaponSkins' => 'weapon_skins', 'operatorSkins' => 'operator_skins',
            'trainSixGrid' => 'train_six_grid', 'tradeStartTime' => 'trade_start_time',
            'tradeEndTime' => 'trade_end_time', 'ban90Days' => 'ban_90_days',
            'commonLoginArea' => 'common_login_area', 'rankLevel' => 'rank_level',
            'rentalDuration' => 'rental_duration', 'priceRatio' => 'price_ratio',
            'rentDays' => 'rent_days', 'faceIsSelf' => 'face_is_self',
            'dailyConsume' => 'daily_consume', 'liquidAssets' => 'liquid_assets',
        ];
        $out = [];
        foreach ($data as $k => $v) {
            $out[$map[$k] ?? $k] = $v;
        }
        return $out;
    }

    public function list(Request $request)
    {
        $q = GameAccount::with('user')->where('status', 1);

        if ($request->filled('keyword')) {
            $kw = $request->keyword;
            $q->where(function ($qq) use ($kw) {
                $qq->where('account_no', 'like', "%{$kw}%")
                    ->orWhere('remark', 'like', "%{$kw}%")
                    ->orWhere('rank_level', 'like', "%{$kw}%");
            });
        }
        if ($request->filled('loginType')) {
            $q->where('login_type', $request->loginType);
        }
        if ($request->filled('rankLevel')) {
            $q->where('rank_level', $request->rankLevel);
        }
        if ($request->filled('minPrice')) {
            $q->where('price', '>=', $request->minPrice);
        }
        if ($request->filled('maxPrice')) {
            $q->where('price', '<=', $request->maxPrice);
        }
        if ($request->filled('minHavCoin')) {
            $q->where('hav_coin', '>=', $request->minHavCoin);
        }
        if ($request->boolean('trainSixGrid')) {
            $q->where('train_six_grid', true);
        }
        if ($request->boolean('faceIsSelf')) {
            $q->where('face_is_self', true);
        }
        if ($request->filled('commonLoginArea')) {
            $q->where('common_login_area', 'like', '%' . $request->commonLoginArea . '%');
        }

        $sort = $request->input('sort', 'newest');
        match ($sort) {
            'price_asc' => $q->orderBy('price'),
            'price_desc' => $q->orderByDesc('price'),
            'coin_desc' => $q->orderByDesc('hav_coin'),
            default => $q->orderByDesc('id'),
        };

        $page = $q->paginate($request->input('pageSize', 12));
        return ApiResponse::success([
            'list' => collect($page->items())->map->toApiArray(),
            'total' => $page->total(),
            'page' => $page->currentPage(),
            'pageSize' => $page->perPage(),
        ]);
    }

    public function show($id)
    {
        $item = GameAccount::with('user')->find($id);
        if (!$item || ($item->status != 1 && !(auth('sanctum')->id() === $item->user_id))) {
            // allow owner or public listed
            if (!$item) {
                return ApiResponse::error('商品不存在', 404);
            }
            if ($item->status != 1 && auth('sanctum')->id() !== $item->user_id) {
                return ApiResponse::error('商品不存在或已下架', 404);
            }
        }
        $item->increment('view_count');
        return ApiResponse::success($item->toApiArray());
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'price' => 'required|numeric|min:0',
            'loginType' => 'required|string',
        ]);
        if ($v->fails()) {
            return ApiResponse::error($v->errors()->first(), 422);
        }
        $data = $this->mapInput($request->all());
        $data['user_id'] = $request->user()->id;
        $data['status'] = 0; // 待审
        $item = GameAccount::create($data);
        return ApiResponse::success($item->toApiArray(), '发布成功，等待审核');
    }

    public function update(Request $request, $id)
    {
        $item = GameAccount::where('user_id', $request->user()->id)->find($id);
        if (!$item) {
            return ApiResponse::error('商品不存在', 404);
        }
        $data = $this->mapInput($request->all());
        unset($data['user_id'], $data['status']);
        $item->fill($data);
        if ($item->status == 2) {
            $item->status = 0; // 重新待审
        }
        $item->save();
        return ApiResponse::success($item->toApiArray(), '更新成功');
    }

    public function offShelf(Request $request, $id)
    {
        $item = GameAccount::where('user_id', $request->user()->id)->find($id);
        if (!$item) {
            return ApiResponse::error('商品不存在', 404);
        }
        $item->status = 2;
        $item->save();
        return ApiResponse::success(null, '已下架');
    }

    public function batchDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids) || empty($ids)) {
            return ApiResponse::error('请选择商品', 422);
        }
        GameAccount::where('user_id', $request->user()->id)->whereIn('id', $ids)->delete();
        return ApiResponse::success(null, '删除成功');
    }

    public function myList(Request $request)
    {
        $q = GameAccount::where('user_id', $request->user()->id)->orderByDesc('id');
        if ($request->filled('status') && $request->status !== '') {
            $q->where('status', $request->status);
        }
        $page = $q->paginate($request->input('pageSize', 20));
        return ApiResponse::success([
            'list' => collect($page->items())->map->toApiArray(),
            'total' => $page->total(),
        ]);
    }
}
