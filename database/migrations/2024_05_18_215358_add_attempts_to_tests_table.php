<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('tests', function (Blueprint $table) {
            $table->integer('attempts')->nullable()->after('ranging');
        });
    }

    public function down(): void {
        Schema::table('tests', function (Blueprint $table) {
            $table->dropColumn('attempts');
        });
    }
};
