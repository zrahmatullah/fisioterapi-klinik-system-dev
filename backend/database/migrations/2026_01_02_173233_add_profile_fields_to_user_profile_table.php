<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('user_profile', function (Blueprint $table) {
            $table->string('no_telepon', 20)->nullable()->after('email');
            $table->foreignId('jenis_kelamin_id')
                ->nullable()
                ->after('no_telepon')
                ->constrained('jenis_kelamin');
            $table->string('spesialisasi')->nullable()->after('jenis_kelamin_id');
        });
    }

    public function down(): void
    {
        Schema::table('user_profile', function (Blueprint $table) {
            $table->dropForeign(['jenis_kelamin_id']);
            $table->dropColumn([
                'no_telepon',
                'jenis_kelamin_id',
                'spesialisasi'
            ]);
        });
    }
};
