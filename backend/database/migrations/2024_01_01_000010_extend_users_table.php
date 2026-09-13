<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->unique()->after('id');
            $table->string('nickname', 50)->nullable()->after('name');
            $table->string('avatar')->nullable()->after('nickname');
            $table->decimal('balance', 12, 2)->default(0)->after('password');
            $table->string('role', 20)->default('user')->after('balance');
            $table->tinyInteger('status')->default(1)->after('role');
            $table->string('real_name', 50)->nullable()->after('status');
            $table->string('id_card', 30)->nullable()->after('real_name');
        });
        // Make email nullable for phone-based auth (sqlite-compatible recreate not needed if already nullable in some installs)
        try {
            if (Schema::getConnection()->getDriverName() === 'sqlite') {
                // sqlite: emails can stay as-is; seeders will set unique emails
            }
        } catch (\Throwable $e) {}
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'nickname', 'avatar', 'balance', 'role', 'status', 'real_name', 'id_card']);
        });
    }
};
