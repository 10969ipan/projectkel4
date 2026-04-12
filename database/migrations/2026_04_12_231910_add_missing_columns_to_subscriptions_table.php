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
        Schema::table('subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('subscriptions', 'quantity')) {
                $table->integer('quantity')->default(1)->after('item_id');
            }
            if (!Schema::hasColumn('subscriptions', 'discount_percentage')) {
                $table->decimal('discount_percentage', 5, 2)->default(10.00)->after('quantity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'discount_percentage']);
        });
    }
};
