<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends Controller
{
    public function list(Request $request)
    {
        $page = $request->user()->complaints()->orderByDesc('id')
            ->paginate($request->input('pageSize', 20));
        // add relation
        return ApiResponse::success([
            'list' => collect($page->items())->map(function ($c) {
                return method_exists($c, 'toApiArray') ? $c->toApiArray() : $c;
            }),
            'total' => $page->total(),
        ]);
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'title' => 'required|string|max:100',
            'content' => 'required|string',
        ]);
        if ($v->fails()) {
            return ApiResponse::error($v->errors()->first(), 422);
        }
        $c = Complaint::create([
            'user_id' => $request->user()->id,
            'order_id' => $request->orderId,
            'title' => $request->title,
            'content' => $request->content,
            'images' => $request->images,
            'status' => 0,
        ]);
        return ApiResponse::success($c->toApiArray(), '投诉已提交');
    }

    public function destroy(Request $request, $id)
    {
        Complaint::where('user_id', $request->user()->id)->where('id', $id)->delete();
        return ApiResponse::success(null, '已删除');
    }
}
