<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('otp_email', function (Blueprint $table) {
            $table->string('purpose', 30)->nullable()->after('email');
            $table->string('reset_token')->nullable()->after('purpose');
        });
    }

    public function down(): void
    {
        Schema::table('otp_email', function (Blueprint $table) {
            $table->dropColumn(['purpose', 'reset_token']);
        });
    }
};

