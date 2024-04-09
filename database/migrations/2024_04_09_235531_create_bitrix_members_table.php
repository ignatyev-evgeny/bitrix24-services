<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bitrix_members', function (Blueprint $table) {
            $table->id();
            $table->string('auth_id');
            $table->string('refresh_id');
            $table->string('member_id');
            $table->integer('bitrix_id');
            $table->boolean('active');
            $table->string('name');
            $table->string('lastname');
            $table->string('photo')->nullable();
            $table->string('email');
            $table->string('last_login');
            $table->string('date_register');
            $table->string('is_online');
            $table->string('time_zone_offset');
            $table->string('timestamp_x');
            $table->string('last_activity_date');
            $table->string('personal_gender')->nullable();
            $table->string('personal_birthday')->nullable();
            $table->json('uf_department');
            $table->json('current_user');
            $table->string('lang');
            $table->foreignId('portal')->constrained('bitrix_portals');
            $table->json('response');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('bitrix_members');
    }
};
