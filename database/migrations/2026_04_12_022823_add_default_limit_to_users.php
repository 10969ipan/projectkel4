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
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('paylater_limit', 15, 2)->default(0)->change();
            $table->decimal('wallet_balance', 15, 2)->default(0)->change();
        });

        // ONLY update users who are real customers (role pelanggan / store_role customer)
        \App\Models\User::where('store_role', 'customer')
            ->update(['paylater_limit' => 500000]);

        // Ensure Admin & Staff stay at 0
        \App\Models\User::where('store_role', '!=', 'customer')
            ->orWhereNull('store_role')
            ->update(['paylater_limit' => 0]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('paylater_limit', 15, 2)->default(0)->change();
        });
    }
};
