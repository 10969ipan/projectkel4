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
        // Drop foreign keys and columns from transactions
        if (Schema::hasColumn('transactions', 'item_size_id')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropForeign(['item_size_id']);
                $table->dropColumn('item_size_id');
            });
        }

        // Drop foreign keys and columns from item_requests
        Schema::table('item_requests', function (Blueprint $table) {
            if (Schema::hasColumn('item_requests', 'item_size_id')) {
                $table->dropForeign(['item_size_id']);
                $table->dropColumn('item_size_id');
            }
            
            if (Schema::hasColumn('item_requests', 'size')) {
                $table->dropColumn('size');
            }
        });

        // Drop the item_sizes table
        Schema::dropIfExists('item_sizes');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-create the item_sizes table
        Schema::create('item_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->string('size')->nullable(); // Or 'batch_number' depending on which state we roll back to
            $table->integer('stock')->default(0);
            $table->timestamps();
        });

        // Re-add columns to transactions
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('item_size_id')
                  ->nullable()
                  ->after('item_id')
                  ->constrained('item_sizes')
                  ->nullOnDelete();
        });

        // Re-add columns to item_requests
        Schema::table('item_requests', function (Blueprint $table) {
            $table->foreignId('item_size_id')
                  ->nullable()
                  ->after('item_id')
                  ->constrained('item_sizes')
                  ->nullOnDelete();
            $table->string('size')->nullable()->after('item_size_id');
        });
    }
};
