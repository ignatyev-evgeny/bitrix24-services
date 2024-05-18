<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('knowledge', function (Blueprint $table) {
            $table->id();
            $table->integer('portal');
            $table->string('title');
            $table->longText('description')->nullable();
            $table->longText('tags')->nullable();
            $table->json('questions')->nullable();
            $table->json('tests')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('knowledge');
    }
};
