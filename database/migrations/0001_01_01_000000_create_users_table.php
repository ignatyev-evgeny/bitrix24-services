<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('auth_id')->nullable();
            $table->string('refresh_id')->nullable();
            $table->string('member_id')->nullable();
            $table->integer('bitrix_id')->unique();
            $table->string('password');
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_administrator')->default(false);
            $table->boolean('is_manager')->default(false);
            $table->boolean('active')->default(false);
            $table->string('name')->nullable();
            $table->string('lastname')->nullable();
            $table->string('photo')->nullable();
            $table->string('email')->unique();
            $table->string('last_login')->nullable();
            $table->string('date_register')->nullable();
            $table->string('is_online')->nullable();
            $table->string('time_zone_offset')->nullable();
            $table->string('timestamp_x')->nullable();
            $table->string('last_activity_date')->nullable();
            $table->string('personal_gender')->nullable();
            $table->string('personal_birthday')->nullable();
            $table->string('user_type')->nullable();
            $table->json('uf_department')->nullable();
            $table->string('lang')->nullable();
            $table->json('auth')->nullable();
            $table->json('member')->nullable();
            $table->foreignId('portal');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
    }
};
