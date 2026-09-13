<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('account_no', 100)->nullable();
            $table->string('login_type', 50)->nullable(); // QQ/微信/手机
            $table->string('account_pwd', 100)->nullable(); // 卖家填写，交易后可见
            $table->tinyInteger('status')->default(0); // 0待审 1上架 2下架
            $table->integer('hav_coin')->default(0);
            $table->integer('insurance_box')->default(0);
            $table->integer('stamina_level')->default(0);
            $table->integer('weight_level')->default(0);
            $table->integer('fh_level')->default(0);
            $table->decimal('kd_value', 8, 2)->default(0);
            $table->integer('awm_ammo')->default(0);
            $table->integer('helmet_l6')->default(0);
            $table->integer('armor_l6')->default(0);
            $table->integer('slot9_card')->default(0);
            $table->text('melee_skins')->nullable(); // JSON
            $table->text('weapon_skins')->nullable();
            $table->text('operator_skins')->nullable();
            $table->boolean('train_six_grid')->default(false);
            $table->string('trade_start_time', 20)->nullable();
            $table->string('trade_end_time', 20)->nullable();
            $table->boolean('ban_90_days')->default(false);
            $table->string('common_login_area', 100)->nullable();
            $table->string('rank_level', 50)->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('deposit', 12, 2)->default(0);
            $table->integer('rental_duration')->default(1); // 天数
            $table->text('remark')->nullable();
            $table->decimal('price_ratio', 8, 2)->nullable();
            $table->integer('rent_days')->default(1);
            $table->boolean('face_is_self')->default(true);
            $table->decimal('daily_consume', 10, 2)->default(0);
            $table->string('pic')->nullable();
            $table->decimal('liquid_assets', 12, 2)->default(0);
            $table->text('screenshots')->nullable(); // JSON array
            $table->integer('view_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'price']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_accounts');
    }
};
