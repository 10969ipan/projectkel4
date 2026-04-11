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
        // 1. Modifikasi tabel Users untuk Telemedisin & Paylater
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'paylater_limit')) {
                $table->decimal('paylater_limit', 15, 2)->default(0)->after('password');
            }
            if (!Schema::hasColumn('users', 'is_prescription_approved')) {
                $table->boolean('is_prescription_approved')->default(false)->after('paylater_limit');
            }
            if (!Schema::hasColumn('users', 'store_role')) {
                $table->string('store_role')->nullable()->after('role')->comment('customer, doctor');
            }
        });

        // 2. Modifikasi tabel Items untuk data harga dan resep wajib
        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'price')) {
                $table->decimal('price', 15, 2)->default(0)->after('manufacturer');
            }
            if (!Schema::hasColumn('items', 'requires_prescription')) {
                $table->boolean('requires_prescription')->default(false)->after('price');
            }
            if (!Schema::hasColumn('items', 'image_path')) {
                $table->string('image_path')->nullable()->after('requires_prescription');
            }
            if (!Schema::hasColumn('items', 'description')) {
                $table->text('description')->nullable()->after('image_path');
            }
        });

        // 3. Tabel Addresses (Untuk Instant Delivery)
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label')->default('Rumah');
            $table->text('full_address');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        // 4. Tabel Store Orders (Pesanan Farmasi)
        Schema::create('store_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('order_number')->unique();
            $table->foreignId('address_id')->nullable()->constrained('addresses')->nullOnDelete();
            $table->string('shipping_method')->default('regular');
            $table->string('payment_method')->default('qris');
            $table->string('payment_status')->default('pending'); // pending, paid, failed
            $table->string('order_status')->default('waiting_shipping_charge'); // waiting_shipping_charge, ordered, paid, shipped, delivered, closed
            $table->decimal('sub_total', 15, 2)->default(0);
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->decimal('unique_code', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->string('prescription_path')->nullable(); // Upload resep fisik jika ada
            $table->timestamps();
        });

        // 5. Tabel Store Order Items
        Schema::create('store_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_order_id')->constrained('store_orders')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('sub_total', 15, 2)->default(0);
            $table->timestamps();
        });

        // 6. Tabel Subscriptions (Langganan Penyakit Kronis)
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->integer('interval_days')->default(30);
            $table->date('next_delivery_date');
            $table->string('status')->default('active'); // active, paused, cancelled
            $table->timestamps();
        });

        // 7. Tabel Telemedicine Chats (Konsultasi Dokter)
        Schema::create('telemedicine_chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Pasien
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete(); // Dokter
            $table->text('message');
            $table->boolean('is_from_doctor')->default(false);
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telemedicine_chats');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('store_order_items');
        Schema::dropIfExists('store_orders');
        Schema::dropIfExists('addresses');

        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['price', 'requires_prescription', 'image_path', 'description']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['paylater_limit', 'is_prescription_approved', 'store_role']);
        });
    }
};
