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
        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'generic_name')) {
                $table->string('generic_name')->after('name')->nullable();
            }
            if (!Schema::hasColumn('items', 'manufacturer')) {
                $table->string('manufacturer')->after('generic_name')->nullable();
            }
        });

        if (Schema::hasColumn('item_sizes', 'size')) {
            Schema::table('item_sizes', function (Blueprint $table) {
                $table->renameColumn('size', 'batch_number');
            });
        }

        if (!Schema::hasColumn('item_sizes', 'expiry_date')) {
            Schema::table('item_sizes', function (Blueprint $table) {
                $table->date('expiry_date')->after('batch_number')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['generic_name', 'manufacturer']);
        });

        Schema::table('item_sizes', function (Blueprint $table) {
            $table->renameColumn('batch_number', 'size');
            $table->dropColumn('expiry_date');
        });
    }
};
