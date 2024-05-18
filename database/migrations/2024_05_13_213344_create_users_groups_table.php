<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users_groups', function (Blueprint $table) {
            $table->id();
            $table->integer('portal');
            $table->string('title');
            $table->longText('description')->nullable();
            $table->json('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::table('users_groups', function (Blueprint $table) {

        });
    }
};
