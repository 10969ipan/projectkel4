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
        // 1. Tambah Saldo ke Tabel Users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'wallet_balance')) {
                $table->decimal('wallet_balance', 15, 2)->default(0)->after('is_prescription_approved');
            }
        });

        // 2. Tabel Wallet Transactions (Riwayat Saldo)
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->string('type'); // topup, payment, refund
            $table->string('description')->nullable();
            $table->string('reference_id')->nullable(); // Order number or Topup ID
            $table->timestamps();
        });

        // 3. Tambahan kolom ke tabel Subscriptions jika diperlukan (opsional karena interval_days sudah ada)
        // Kita tambahkan kolom discount agar record diskon saat langganan dibuat tersimpan permanen
        Schema::table('subscriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('subscriptions', 'discount_percentage')) {
                $table->decimal('discount_percentage', 5, 2)->default(10.00)->after('interval_days');
            }
            if (!Schema::hasColumn('subscriptions', 'quantity')) {
                $table->integer('quantity')->default(1)->after('item_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['discount_percentage', 'quantity']);
        });
        Schema::dropIfExists('wallet_transactions');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('wallet_balance');
        });
    }
};
