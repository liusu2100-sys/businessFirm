<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ComplaintController;
use App\Http\Controllers\Api\CsController;
use App\Http\Controllers\Api\GameAccountController;
use App\Http\Controllers\Api\ImController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\PaymentAccountController;
use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

Route::get('game-account/list', [GameAccountController::class, 'list']);
Route::get('game-account/{id}', [GameAccountController::class, 'show'])->whereNumber('id');

Route::get('announcements', [PublicController::class, 'announcements']);
Route::get('announcement/{id}', [PublicController::class, 'announcementShow']);
Route::get('banners', [PublicController::class, 'banners']);
Route::get('public/config', [PublicController::class, 'config']);
Route::get('public/recharge-config', [PublicController::class, 'rechargeConfig']);
Route::get('public/stats', [PublicController::class, 'stats']);
Route::get('public/help', [PublicController::class, 'help']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('auth/me', [AuthController::class, 'me']);

    Route::get('user/profile', [UserController::class, 'profile']);
    Route::put('user/profile', [UserController::class, 'updateProfile']);
    Route::put('user/password', [UserController::class, 'updatePassword']);
    Route::get('user/balance-log', [UserController::class, 'balanceLog']);
    Route::post('user/withdrawal', [UserController::class, 'withdrawal']);
    Route::get('user/withdrawal-list', [UserController::class, 'withdrawalList']);

    Route::get('game-account/my/list', [GameAccountController::class, 'myList']);
    Route::post('game-account', [GameAccountController::class, 'store']);
    Route::put('game-account/{id}', [GameAccountController::class, 'update']);
    Route::put('game-account/{id}/off-shelf', [GameAccountController::class, 'offShelf']);
    Route::delete('game-account/batch', [GameAccountController::class, 'batchDelete']);

    Route::get('order/list', [OrderController::class, 'list']);
    Route::post('order', [OrderController::class, 'store']);
    Route::put('order/{id}/pay', [OrderController::class, 'pay']);
    Route::put('order/{id}/complete', [OrderController::class, 'complete']);
    Route::put('order/{id}/early-settle', [OrderController::class, 'earlySettle']);
    Route::put('order/{id}/cancel', [OrderController::class, 'cancel']);

    Route::get('payment-account', [PaymentAccountController::class, 'list']);
    Route::post('payment-account', [PaymentAccountController::class, 'store']);
    Route::put('payment-account/{id}', [PaymentAccountController::class, 'update']);
    Route::delete('payment-account/{id}', [PaymentAccountController::class, 'destroy']);
    Route::put('payment-account/{id}/set-default', [PaymentAccountController::class, 'setDefault']);

    Route::get('complaint', [ComplaintController::class, 'list']);
    Route::post('complaint', [ComplaintController::class, 'store']);
    Route::delete('complaint/{id}', [ComplaintController::class, 'destroy']);

    Route::get('im/conversations', [ImController::class, 'conversations']);
    Route::get('im/messages', [ImController::class, 'messages']);
    Route::post('im/message', [ImController::class, 'send']);
    Route::post('im/single', [ImController::class, 'single']);
    Route::post('im/product-consultations', [ImController::class, 'productConsult']);
    Route::post('product-consultations', [ImController::class, 'productConsult']);

    Route::post('cs/session/start', [CsController::class, 'start']);
    Route::get('cs/message/list', [CsController::class, 'messageList']);
    Route::post('cs/message/send', [CsController::class, 'send']);

    Route::post('upload/image', [UploadController::class, 'image']);

    Route::prefix('admin')->middleware(AdminMiddleware::class)->group(function () {
        Route::get('dashboard', [AdminController::class, 'dashboard']);
        Route::get('users', [AdminController::class, 'users']);
        Route::post('users/credit', [AdminController::class, 'credit']);
        Route::get('accounts', [AdminController::class, 'accounts']);
        Route::put('accounts/{id}/audit', [AdminController::class, 'auditAccount']);
        Route::get('orders', [AdminController::class, 'orders']);
        Route::get('withdrawals', [AdminController::class, 'withdrawals']);
        Route::put('withdrawals/{id}', [AdminController::class, 'processWithdrawal']);
        Route::get('complaints', [AdminController::class, 'complaints']);
        Route::put('complaints/{id}', [AdminController::class, 'replyComplaint']);
        Route::get('announcements', [AdminController::class, 'announcements']);
        Route::post('announcements', [AdminController::class, 'saveAnnouncement']);
        Route::put('announcements/{id}', [AdminController::class, 'updateAnnouncement']);
        Route::delete('announcements/{id}', [AdminController::class, 'deleteAnnouncement']);
        Route::get('banners', [AdminController::class, 'banners']);
        Route::post('banners', [AdminController::class, 'saveBanner']);
        Route::put('banners/{id}', [AdminController::class, 'updateBanner']);
        Route::delete('banners/{id}', [AdminController::class, 'deleteBanner']);
        Route::get('configs', [AdminController::class, 'configs']);
        Route::post('configs', [AdminController::class, 'saveConfigs']);
        Route::get('cs/sessions', [AdminController::class, 'csSessions']);
    });
});
