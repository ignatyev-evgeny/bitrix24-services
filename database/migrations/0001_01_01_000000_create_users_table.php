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
            $table->boolean('active')->default(false);
            $table->string('password');
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_support')->default(false);
            $table->boolean('is_manager')->default(false);
            $table->string('name')->nullable();
            $table->string('photo')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('phone_personal')->nullable()->unique();
            $table->string('phone_work')->nullable()->unique();
            $table->json('departments')->nullable();
            $table->string('position')->nullable();
            $table->string('lang')->default('RU');
            $table->json('auth')->nullable();
            $table->json('user')->nullable();
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
