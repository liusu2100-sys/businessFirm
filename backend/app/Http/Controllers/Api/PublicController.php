<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Banner;
use App\Models\GameAccount;
use App\Models\Order;
use App\Models\SiteConfig;
use App\Models\User;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function announcements(Request $request)
    {
        $type = $request->input('type');
        $q = Announcement::where('status', 1)->orderByDesc('is_top')->orderByDesc('sort')->orderByDesc('id');
        if ($type) {
            $q->where('type', $type);
        }
        $list = $q->paginate($request->input('pageSize', 20));
        return ApiResponse::success([
            'list' => collect($list->items())->map->toApiArray(),
            'total' => $list->total(),
        ]);
    }

    public function announcementShow($id)
    {
        $a = Announcement::where('status', 1)->find($id);
        if (!$a) {
            return ApiResponse::error('公告不存在', 404);
        }
        return ApiResponse::success($a->toApiArray());
    }

    public function banners()
    {
        $list = Banner::where('status', 1)->orderByDesc('sort')->orderByDesc('id')->get();
        return ApiResponse::success($list->map->toApiArray());
    }

    public function config()
    {
        $cfg = SiteConfig::allCached();
        return ApiResponse::success($cfg);
    }

    public function rechargeConfig()
    {
        return ApiResponse::success([
            'qrcode' => SiteConfig::getValue('recharge_qrcode', '/placeholder-qr.png'),
            'tips' => SiteConfig::getValue('recharge_tips', '请扫码转账后联系客服充值，备注手机号'),
            'amounts' => SiteConfig::getValue('recharge_amounts', [50, 100, 200, 500, 1000]),
            'contact' => SiteConfig::getValue('cs_contact', '客服微信：sssh_cs'),
        ]);
    }

    public function stats()
    {
        return ApiResponse::success([
            'accountCount' => GameAccount::where('status', 1)->count(),
            'orderCount' => Order::where('status', 5)->count(),
            'userCount' => User::where('role', 'user')->count(),
            'todayOrders' => Order::whereDate('created_at', today())->count(),
        ]);
    }

    public function help()
    {
        $list = Announcement::where('status', 1)->where('type', 'help')
            ->orderByDesc('sort')->orderByDesc('id')->get();
        return ApiResponse::success($list->map->toApiArray());
    }
}
