<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\ImMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ImController extends Controller
{
    public function conversations(Request $request)
    {
        $userId = $request->user()->id;
        $convs = Conversation::whereHas('participants', fn ($q) => $q->where('user_id', $userId))
            ->with(['participants', 'messages' => fn ($q) => $q->latest('id')->limit(1)])
            ->orderByDesc('last_message_at')
            ->get();

        $list = $convs->map(function ($c) use ($userId) {
            $other = $c->participants->firstWhere('id', '!=', $userId);
            $last = $c->messages->first();
            return [
                'id' => $c->id,
                'type' => $c->type,
                'title' => $c->title ?: ($other?->nickname ?? '会话'),
                'gameAccountId' => $c->game_account_id,
                'otherUser' => $other ? ['id' => $other->id, 'nickname' => $other->nickname, 'avatar' => $other->avatar] : null,
                'lastMessage' => $last ? $last->toApiArray() : null,
                'lastMessageAt' => optional($c->last_message_at)?->toDateTimeString(),
            ];
        });
        return ApiResponse::success($list);
    }

    public function messages(Request $request)
    {
        $cid = $request->input('conversationId');
        $userId = $request->user()->id;
        $conv = Conversation::whereHas('participants', fn ($q) => $q->where('user_id', $userId))->find($cid);
        if (!$conv) {
            return ApiResponse::error('会话不存在', 404);
        }
        $page = ImMessage::with('user')->where('conversation_id', $cid)->orderByDesc('id')
            ->paginate($request->input('pageSize', 50));
        return ApiResponse::success([
            'list' => collect($page->items())->reverse()->values()->map->toApiArray(),
            'total' => $page->total(),
        ]);
    }

    public function send(Request $request)
    {
        $cid = $request->input('conversationId');
        $content = $request->input('content');
        if (!$content) {
            return ApiResponse::error('消息不能为空', 422);
        }
        $userId = $request->user()->id;
        $conv = Conversation::whereHas('participants', fn ($q) => $q->where('user_id', $userId))->find($cid);
        if (!$conv) {
            return ApiResponse::error('会话不存在', 404);
        }
        $msg = ImMessage::create([
            'conversation_id' => $cid,
            'user_id' => $userId,
            'type' => $request->input('type', 'text'),
            'content' => $content,
        ]);
        $conv->last_message_at = now();
        $conv->save();
        return ApiResponse::success($msg->load('user')->toApiArray(), '发送成功');
    }

    public function single(Request $request)
    {
        $targetId = (int) $request->input('userId');
        $userId = $request->user()->id;
        if ($targetId === $userId) {
            return ApiResponse::error('不能与自己对话', 400);
        }

        $conv = Conversation::where('type', 'single')
            ->whereHas('participants', fn ($q) => $q->where('user_id', $userId))
            ->whereHas('participants', fn ($q) => $q->where('user_id', $targetId))
            ->first();

        if (!$conv) {
            $conv = DB::transaction(function () use ($userId, $targetId) {
                $c = Conversation::create(['type' => 'single', 'last_message_at' => now()]);
                $c->participants()->attach([$userId, $targetId]);
                return $c;
            });
        }
        return ApiResponse::success(['id' => $conv->id]);
    }

    public function productConsult(Request $request)
    {
        $accountId = (int) $request->input('gameAccountId');
        $account = \App\Models\GameAccount::find($accountId);
        if (!$account) {
            return ApiResponse::error('商品不存在', 404);
        }
        $userId = $request->user()->id;
        $sellerId = $account->user_id;

        $conv = Conversation::where('type', 'product')
            ->where('game_account_id', $accountId)
            ->whereHas('participants', fn ($q) => $q->where('user_id', $userId))
            ->first();

        if (!$conv) {
            $conv = DB::transaction(function () use ($userId, $sellerId, $accountId, $account) {
                $c = Conversation::create([
                    'type' => 'product',
                    'title' => '咨询：' . ($account->account_no ?: ('账号#' . $accountId)),
                    'game_account_id' => $accountId,
                    'last_message_at' => now(),
                ]);
                $c->participants()->attach(array_unique([$userId, $sellerId]));
                return $c;
            });
        }
        return ApiResponse::success(['id' => $conv->id]);
    }
}
