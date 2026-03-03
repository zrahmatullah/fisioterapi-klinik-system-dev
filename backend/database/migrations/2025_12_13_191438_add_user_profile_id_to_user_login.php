<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_login', function (Blueprint $table) {
            $table->foreignId('user_profile_id')
                ->nullable()
                ->after('id')
                ->constrained('user_profile')
                ->cascadeOnUpdate()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('user_login', function (Blueprint $table) {
            $table->dropForeign(['user_profile_id']);
            $table->dropColumn('user_profile_id');
        });
    }
};
