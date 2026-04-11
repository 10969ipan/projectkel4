<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Jadikan kolom role nullable dan hapus default 'staff'
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->nullable()->default(null)->change();
        });

        // 2. Bersihkan data: Semua user yang punya store_role 'customer' harusnya role-nya NULL
        DB::table('users')
            ->where('store_role', 'customer')
            ->update(['role' => null]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->nullable(false)->default('staff')->change();
        });
    }
};
