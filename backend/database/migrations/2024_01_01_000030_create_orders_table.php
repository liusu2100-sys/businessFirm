<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no', 40)->unique();
            $table->foreignId('game_account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('price', 12, 2);
            $table->decimal('deposit', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2); // price + deposit
            $table->decimal('commission', 12, 2)->default(0);
            $table->integer('rent_days')->default(1);
            $table->tinyInteger('status')->default(0); // 0待支付 1交易中 3已取消 4已退款 5已完成
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('expire_at')->nullable(); // 待支付超时
            $table->text('remark')->nullable();
            $table->timestamps();
            $table->index(['buyer_id', 'status']);
            $table->index(['seller_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
