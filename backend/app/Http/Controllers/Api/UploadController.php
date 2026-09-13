<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function image(Request $request)
    {
        if (!$request->hasFile('file')) {
            return ApiResponse::error('请选择文件', 422);
        }
        $file = $request->file('file');
        if (!$file->isValid()) {
            return ApiResponse::error('文件无效', 422);
        }
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, $allowed)) {
            return ApiResponse::error('仅支持图片格式', 422);
        }
        $path = $file->store('uploads/' . date('Ym'), 'public');
        $url = '/storage/' . $path;
        return ApiResponse::success(['url' => $url], '上传成功');
    }
}
