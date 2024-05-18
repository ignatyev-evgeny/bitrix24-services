<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('users_groups', function (Blueprint $table) {
            $table->integer('manager')->after('portal');
        });
    }

    public function down(): void {
        Schema::table('users_groups', function (Blueprint $table) {
            //
        });
    }
};
