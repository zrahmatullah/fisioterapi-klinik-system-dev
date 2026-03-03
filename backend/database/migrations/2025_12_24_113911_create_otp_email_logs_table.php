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
        Schema::create('otp_email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('otp_email_id')
                ->constrained('otp_email')
                ->cascadeOnDelete();
            $table->string('email');
            $table->enum('status', ['success', 'failed']);
            $table->text('response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_email_logs');
    }
};
