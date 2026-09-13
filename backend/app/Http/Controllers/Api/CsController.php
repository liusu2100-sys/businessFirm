<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\CsMessage;
use App\Models\CsSession;
use Illuminate\Http\Request;

class CsController extends Controller
{
    public function start(Request $request)
    {
        $session = CsSession::firstOrCreate(
            ['user_id' => $request->user()->id, 'status' => 1],
            ['last_message_at' => now()]
        );
        return ApiResponse::success([
            'id' => $session->id,
            'status' => $session->status,
        ]);
    }

    public function messageList(Request $request)
    {
        $sessionId = $request->input('sessionId');
        $session = CsSession::where('user_id', $request->user()->id)->find($sessionId);
        if (!$session && $request->user()->isAdmin()) {
            $session = CsSession::find($sessionId);
        }
        if (!$session) {
            return ApiResponse::error('会话不存在', 404);
        }
        $list = CsMessage::with('user')->where('cs_session_id', $session->id)->orderBy('id')->get();
        return ApiResponse::success($list->map->toApiArray());
    }

    public function send(Request $request)
    {
        $sessionId = $request->input('sessionId');
        $content = $request->input('content');
        if (!$content) {
            return ApiResponse::error('消息不能为空', 422);
        }
        $session = CsSession::find($sessionId);
        if (!$session) {
            return ApiResponse::error('会话不存在', 404);
        }
        $isStaff = $request->user()->isAdmin();
        if (!$isStaff && $session->user_id !== $request->user()->id) {
            return ApiResponse::error('无权操作', 403);
        }
        $msg = CsMessage::create([
            'cs_session_id' => $session->id,
            'user_id' => $request->user()->id,
            'type' => $request->input('type', 'text'),
            'content' => $content,
            'is_staff' => $isStaff,
        ]);
        $session->last_message_at = now();
        if ($isStaff) {
            $session->admin_id = $request->user()->id;
        }
        $session->save();
        return ApiResponse::success($msg->load('user')->toApiArray());
    }
}
