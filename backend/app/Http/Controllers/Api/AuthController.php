<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $v = Validator::make($request->all(), [
            'phone' => 'required|string',
            'password' => 'required|string',
        ]);
        if ($v->fails()) {
            return ApiResponse::error($v->errors()->first(), 422);
        }

        $user = User::where('phone', $request->phone)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return ApiResponse::error('手机号或密码错误', 401);
        }
        if ($user->status != 1) {
            return ApiResponse::error('账号已被禁用', 403);
        }

        $token = $user->createToken('api')->plainTextToken;
        return ApiResponse::success([
            'token' => $token,
            'user' => $this->userData($user),
        ], '登录成功');
    }

    public function register(Request $request)
    {
        $v = Validator::make($request->all(), [
            'phone' => 'required|string|regex:/^1\d{10}$/|unique:users,phone',
            'password' => 'required|string|min:6',
            'nickname' => 'nullable|string|max:50',
        ], [
            'phone.unique' => '该手机号已注册',
            'phone.regex' => '手机号格式不正确',
        ]);
        if ($v->fails()) {
            return ApiResponse::error($v->errors()->first(), 422);
        }

        $user = User::create([
            'name' => $request->nickname ?: ('用户' . substr($request->phone, -4)),
            'nickname' => $request->nickname ?: ('用户' . substr($request->phone, -4)),
            'phone' => $request->phone,
            'email' => $request->phone . '@sssh.local',
            'password' => $request->password,
            'role' => 'user',
            'balance' => 0,
        ]);

        $token = $user->createToken('api')->plainTextToken;
        return ApiResponse::success([
            'token' => $token,
            'user' => $this->userData($user),
        ], '注册成功');
    }

    public function me(Request $request)
    {
        return ApiResponse::success($this->userData($request->user()));
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return ApiResponse::success(null, '已退出');
    }

    private function userData(User $user): array
    {
        return [
            'id' => $user->id,
            'phone' => $user->phone,
            'nickname' => $user->nickname,
            'name' => $user->name,
            'avatar' => $user->avatar,
            'balance' => $user->balance,
            'role' => $user->role,
            'realName' => $user->real_name,
            'createdAt' => $user->created_at?->toDateTimeString(),
        ];
    }
}
