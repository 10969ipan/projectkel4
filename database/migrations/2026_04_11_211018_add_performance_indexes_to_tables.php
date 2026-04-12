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
        // Index untuk tabel items (Pecah per field agar tahan error)
        try { Schema::table('items', function (Blueprint $table) { $table->index('price'); }); } catch (\Exception $e) {}
        try { Schema::table('items', function (Blueprint $table) { $table->index('stock'); }); } catch (\Exception $e) {}
        try { Schema::table('items', function (Blueprint $table) { $table->index('requires_prescription'); }); } catch (\Exception $e) {}

        // Index untuk tabel item_sizes
        try { Schema::table('item_sizes', function (Blueprint $table) { $table->index('expiry_date'); }); } catch (\Exception $e) {}
        try { Schema::table('item_sizes', function (Blueprint $table) { $table->index(['item_id', 'expiry_date']); }); } catch (\Exception $e) {}

        // Index untuk tabel store_orders
        try { Schema::table('store_orders', function (Blueprint $table) { $table->index('payment_status'); }); } catch (\Exception $e) {}
        try { Schema::table('store_orders', function (Blueprint $table) { $table->index('order_status'); }); } catch (\Exception $e) {}
        try { Schema::table('store_orders', function (Blueprint $table) { $table->index('created_at'); }); } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try { Schema::table('store_orders', function (Blueprint $table) { $table->dropIndex(['payment_status']); }); } catch (\Exception $e) {}
        try { Schema::table('store_orders', function (Blueprint $table) { $table->dropIndex(['order_status']); }); } catch (\Exception $e) {}
        try { Schema::table('store_orders', function (Blueprint $table) { $table->dropIndex(['created_at']); }); } catch (\Exception $e) {}

        try { Schema::table('item_sizes', function (Blueprint $table) { $table->dropIndex(['expiry_date']); }); } catch (\Exception $e) {}
        try { Schema::table('item_sizes', function (Blueprint $table) { $table->dropIndex(['item_id', 'expiry_date']); }); } catch (\Exception $e) {}

        try { Schema::table('items', function (Blueprint $table) { $table->dropIndex(['price']); }); } catch (\Exception $e) {}
        try { Schema::table('items', function (Blueprint $table) { $table->dropIndex(['stock']); }); } catch (\Exception $e) {}
        try { Schema::table('items', function (Blueprint $table) { $table->dropIndex(['requires_prescription']); }); } catch (\Exception $e) {}
    }
};
