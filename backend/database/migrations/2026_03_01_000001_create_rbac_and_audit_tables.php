<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. PERMISSIONS
        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('group')->default('umum');
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }

        // 2. ROLE_PERMISSIONS
        if (!Schema::hasTable('role_permissions')) {
            Schema::create('role_permissions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('role_id');
                $table->unsignedBigInteger('permission_id');
                $table->timestamps();

                $table->foreign('role_id')->references('id')->on('role')->onDelete('cascade');
                $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
                $table->unique(['role_id', 'permission_id']);
            });
        }

        // 3. SYSTEM AUDIT LOGS
        if (!Schema::hasTable('system_audit_logs')) {
            Schema::create('system_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('action'); // LOGIN, LOGOUT, CREATE, UPDATE, DELETE
                $table->string('module')->nullable();
                $table->string('model_type')->nullable();
                $table->unsignedBigInteger('model_id')->nullable();
                $table->jsonb('old_values')->nullable();
                $table->jsonb('new_values')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'action']);
                $table->index(['model_type', 'model_id']);
            });
        }

        // 4. EMR AUDIT LOGS (Permenkes 24/2022 & UU PDP 27/2022)
        if (!Schema::hasTable('emr_audit_logs')) {
            Schema::create('emr_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('profile_anak_id');
                $table->unsignedBigInteger('emr_record_id')->nullable();
                $table->string('action'); // VIEW, CREATE, UPDATE, EXPORT_PDF, ADDENDUM, SIGN
                $table->text('alasan_akses')->nullable();
                $table->jsonb('details')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->index(['profile_anak_id', 'created_at']);
                $table->index(['user_id', 'action']);
            });
        }

        // 5. CLINIC SETTINGS
        if (!Schema::hasTable('clinic_settings')) {
            Schema::create('clinic_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->string('group')->default('general');
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('clinic_settings');
        Schema::dropIfExists('emr_audit_logs');
        Schema::dropIfExists('system_audit_logs');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
    }
};
