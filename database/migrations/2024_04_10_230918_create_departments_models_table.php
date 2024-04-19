<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bitrix_departments', function (Blueprint $table) {
            $table->id();
            $table->integer('bitrix_id');
            $table->foreignId('portal');
            $table->string('name');
            $table->integer('parent')->nullable();
            $table->json('managers')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('bitrix_departments');
    }
};
