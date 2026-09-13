<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->default('single'); // single|group|product
            $table->string('title')->nullable();
            $table->unsignedBigInteger('game_account_id')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });

        Schema::create('conversation_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('last_read_at')->nullable();
            $table->timestamps();
            $table->unique(['conversation_id', 'user_id']);
        });

        Schema::create('im_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20)->default('text'); // text|image
            $table->text('content');
            $table->timestamps();
            $table->index(['conversation_id', 'id']);
        });

        Schema::create('cs_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->tinyInteger('status')->default(1); // 1进行中 0已关闭
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });

        Schema::create('cs_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cs_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20)->default('text');
            $table->text('content');
            $table->boolean('is_staff')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cs_messages');
        Schema::dropIfExists('cs_sessions');
        Schema::dropIfExists('im_messages');
        Schema::dropIfExists('conversation_participants');
        Schema::dropIfExists('conversations');
    }
};
