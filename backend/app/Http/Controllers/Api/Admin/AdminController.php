<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Banner;
use App\Models\Complaint;
use App\Models\CsSession;
use App\Models\GameAccount;
use App\Models\Order;
use App\Models\SiteConfig;
use App\Models\User;
use App\Models\Withdrawal;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminController extends Controller
{
    public function dashboard()
    {
        return ApiResponse::success([
            'users' => User::count(),
            'accountsPending' => GameAccount::where('status', 0)->count(),
            'accountsOnSale' => GameAccount::where('status', 1)->count(),
            'ordersTrading' => Order::where('status', 1)->count(),
            'ordersToday' => Order::whereDate('created_at', today())->count(),
            'withdrawalsPending' => Withdrawal::where('status', 0)->count(),
            'csOpen' => CsSession::where('status', 1)->count(),
            'complaintsOpen' => Complaint::where('status', 0)->count(),
        ]);
    }

    public function users(Request $request)
    {
        $q = User::query()->orderByDesc('id');
        if ($request->filled('keyword')) {
            $kw = $request->keyword;
            $q->where(fn ($qq) => $qq->where('phone', 'like', "%{$kw}%")->orWhere('nickname', 'like', "%{$kw}%"));
        }
        $page = $q->paginate($request->input('pageSize', 20));
        return ApiResponse::success([
            'list' => collect($page->items())->map(fn ($u) => [
                'id' => $u->id, 'phone' => $u->phone, 'nickname' => $u->nickname,
                'balance' => $u->balance, 'role' => $u->role, 'status' => $u->status,
                'createdAt' => $u->created_at?->toDateTimeString(),
            ]),
            'total' => $page->total(),
        ]);
    }

    public function credit(Request $request, WalletService $wallet)
    {
        $user = User::find($request->userId);
        if (!$user) {
            return ApiResponse::error('用户不存在', 404);
        }
        $amount = (float) $request->amount;
        if ($amount == 0) {
            return ApiResponse::error('金额不能为0', 422);
        }
        $type = $amount > 0 ? 'admin_credit' : 'admin_debit';
        try {
            $wallet->change($user, $type, $amount, $request->input('remark', '管理员调账'), 'admin', $request->user()->id);
        } catch (\Throwable $e) {
            return ApiResponse::error($e->getMessage(), 400);
        }
        return ApiResponse::success(['balance' => $user->fresh()->balance], '调账成功');
    }

    public function accounts(Request $request)
    {
        $q = GameAccount::with('user')->orderByDesc('id');
        if ($request->filled('status') && $request->status !== '') {
            $q->where('status', $request->status);
        }
        $page = $q->paginate($request->input('pageSize', 20));
        return ApiResponse::success([
            'list' => collect($page->items())->map->toApiArray(),
            'total' => $page->total(),
        ]);
    }

    public function auditAccount(Request $request, $id)
    {
        $item = GameAccount::find($id);
        if (!$item) {
            return ApiResponse::error('不存在', 404);
        }
        $status = (int) $request->input('status'); // 1通过 2拒绝下架
        if (!in_array($status, [1, 2])) {
            return ApiResponse::error('状态无效', 422);
        }
        $item->status = $status;
        $item->save();
        return ApiResponse::success(null, $status == 1 ? '已上架' : '已拒绝');
    }

    public function orders(Request $request)
    {
        $q = Order::with(['gameAccount', 'buyer', 'seller'])->orderByDesc('id');
        if ($request->filled('status') && $request->status !== '') {
            $q->where('status', $request->status);
        }
        $page = $q->paginate($request->input('pageSize', 20));
        return ApiResponse::success([
            'list' => collect($page->items())->map->toApiArray(),
            'total' => $page->total(),
        ]);
    }

    public function withdrawals(Request $request)
    {
        $q = Withdrawal::with(['user', 'paymentAccount'])->orderByDesc('id');
        if ($request->filled('status') && $request->status !== '') {
            $q->where('status', $request->status);
        }
        $page = $q->paginate($request->input('pageSize', 20));
        return ApiResponse::success([
            'list' => collect($page->items())->map(fn ($w) => array_merge($w->toApiArray(), [
                'user' => ['id' => $w->user->id, 'phone' => $w->user->phone, 'nickname' => $w->user->nickname],
            ])),
            'total' => $page->total(),
        ]);
    }

    public function processWithdrawal(Request $request, $id, WalletService $wallet)
    {
        $w = Withdrawal::find($id);
        if (!$w || $w->status != 0) {
            return ApiResponse::error('记录不存在或已处理', 400);
        }
        $status = (int) $request->input('status'); // 1通过 2拒绝
        if ($status == 1) {
            $w->status = 1;
            $w->admin_remark = $request->remark;
            $w->processed_at = now();
            $w->save();
            return ApiResponse::success(null, '已打款');
        }
        if ($status == 2) {
            $wallet->change($w->user, 'refund', (float) $w->amount, '提现驳回退回', 'withdrawal', $w->id);
            $w->status = 2;
            $w->admin_remark = $request->remark;
            $w->processed_at = now();
            $w->save();
            return ApiResponse::success(null, '已拒绝并退回余额');
        }
        return ApiResponse::error('状态无效', 422);
    }

    public function complaints(Request $request)
    {
        $page = Complaint::with('user')->orderByDesc('id')->paginate($request->input('pageSize', 20));
        return ApiResponse::success([
            'list' => collect($page->items())->map(fn ($c) => array_merge($c->toApiArray(), [
                'user' => ['id' => $c->user->id, 'nickname' => $c->user->nickname, 'phone' => $c->user->phone],
            ])),
            'total' => $page->total(),
        ]);
    }

    public function replyComplaint(Request $request, $id)
    {
        $c = Complaint::find($id);
        if (!$c) {
            return ApiResponse::error('不存在', 404);
        }
        $c->reply = $request->reply;
        $c->status = (int) $request->input('status', 2);
        $c->save();
        return ApiResponse::success(null, '已回复');
    }

    public function announcements(Request $request)
    {
        $page = Announcement::orderByDesc('id')->paginate(20);
        return ApiResponse::success([
            'list' => collect($page->items())->map->toApiArray(),
            'total' => $page->total(),
        ]);
    }

    public function saveAnnouncement(Request $request, $id = null)
    {
        $data = [
            'title' => $request->title,
            'content' => $request->content,
            'type' => $request->input('type', 'notice'),
            'is_top' => (bool) $request->input('isTop', false),
            'status' => (int) $request->input('status', 1),
            'sort' => (int) $request->input('sort', 0),
        ];
        if ($id) {
            $a = Announcement::findOrFail($id);
            $a->update($data);
        } else {
            $a = Announcement::create($data);
        }
        return ApiResponse::success($a->toApiArray(), '保存成功');
    }

    public function updateAnnouncement(Request $request, $id)
    {
        return $this->saveAnnouncement($request, $id);
    }

    public function updateBanner(Request $request, $id)
    {
        return $this->saveBanner($request, $id);
    }

    public function deleteAnnouncement($id)
    {
        Announcement::where('id', $id)->delete();
        return ApiResponse::success(null, '已删除');
    }

    public function banners()
    {
        return ApiResponse::success(Banner::orderByDesc('sort')->get()->map->toApiArray());
    }

    public function saveBanner(Request $request, $id = null)
    {
        $data = [
            'title' => $request->title,
            'image' => $request->image,
            'link' => $request->link,
            'sort' => (int) $request->input('sort', 0),
            'status' => (int) $request->input('status', 1),
        ];
        if ($id) {
            $b = Banner::findOrFail($id);
            $b->update($data);
        } else {
            $b = Banner::create($data);
        }
        return ApiResponse::success($b->toApiArray());
    }

    public function deleteBanner($id)
    {
        Banner::where('id', $id)->delete();
        return ApiResponse::success(null, '已删除');
    }

    public function configs()
    {
        return ApiResponse::success(SiteConfig::allCached());
    }

    public function saveConfigs(Request $request)
    {
        $data = $request->input('configs', $request->all());
        foreach ($data as $k => $v) {
            if (in_array($k, ['_token'])) continue;
            SiteConfig::setValue($k, $v);
        }
        Cache::forget('site_configs');
        return ApiResponse::success(SiteConfig::allCached(), '配置已保存');
    }

    public function csSessions()
    {
        $list = CsSession::with('user')->orderByDesc('last_message_at')->limit(100)->get();
        return ApiResponse::success($list->map(fn ($s) => [
            'id' => $s->id,
            'userId' => $s->user_id,
            'status' => $s->status,
            'nickname' => $s->user?->nickname,
            'phone' => $s->user?->phone,
            'lastMessageAt' => optional($s->last_message_at)?->toDateTimeString(),
        ]));
    }
}
