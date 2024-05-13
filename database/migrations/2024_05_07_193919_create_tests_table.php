<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('portal');
            $table->string('title');
            $table->longText('description')->nullable();
            $table->integer('maximum_time');
            $table->integer('maximum_score');
            $table->integer('passing_score');
            $table->boolean('skipping');
            $table->boolean('ranging');
            $table->json('questions');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('tests');
    }
};
