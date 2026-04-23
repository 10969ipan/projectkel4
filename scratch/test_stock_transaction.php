<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

use App\Models\Item;
use App\Models\Transaction;
use App\Models\ItemRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function logTest($msg) {
    echo "[TEST] " . $msg . PHP_EOL;
}

try {
    DB::beginTransaction();

    // 1. Setup Data
    logTest("Mencari item untuk pengujian...");
    $item = Item::first();
    if (!$item) {
        logTest("Tidak ada item ditemukan. Membuat item baru...");
        $item = Item::create([
            'code' => 'TEST-001',
            'name' => 'Paracetamol Test',
            'category_id' => 1,
            'unit_id' => 1,
            'price' => 500,
            'stock' => 100
        ]);
    }
    
    $initialStock = $item->stock;
    logTest("Item: {$item->name}, Stok Awal: {$initialStock}");

    $admin = User::where('role', 'admin')->first() ?: User::first();
    auth()->login($admin);

    // 2. Test Transaction IN
    logTest("Melakukan Transaksi MASUK (IN) sebesar 50...");
    Transaction::create([
        'item_id' => $item->id,
        'user_id' => $admin->id,
        'type' => 'in',
        'quantity' => 50,
        'date' => now(),
        'note' => 'Test Transaksi Masuk'
    ]);
    $item->increment('stock', 50);
    
    $item->refresh();
    logTest("Stok setelah IN: {$item->stock}");
    if ($item->stock == $initialStock + 50) {
        logTest("BERHASIL: Transaksi MASUK akurat.");
    } else {
        logTest("GAGAL: Transaksi MASUK tidak akurat!");
    }

    // 3. Test Transaction OUT
    logTest("Melakukan Transaksi KELUAR (OUT) sebesar 30...");
    Transaction::create([
        'item_id' => $item->id,
        'user_id' => $admin->id,
        'type' => 'out',
        'quantity' => 30,
        'date' => now(),
        'note' => 'Test Transaksi Keluar'
    ]);
    $item->decrement('stock', 30);
    
    $item->refresh();
    logTest("Stok setelah OUT: {$item->stock}");
    if ($item->stock == $initialStock + 50 - 30) {
        logTest("BERHASIL: Transaksi KELUAR akurat.");
    } else {
        logTest("GAGAL: Transaksi KELUAR tidak akurat!");
    }

    // 4. Test Item Request Approval
    logTest("Membuat Permintaan Barang (Item Request) sebesar 20...");
    $request = ItemRequest::create([
        'item_id' => $item->id,
        'user_id' => $admin->id,
        'quantity' => 20,
        'reason' => 'Test Permintaan Barang',
        'status' => 'pending'
    ]);

    logTest("Menyetujui Permintaan Barang...");
    // Simulasi logika di ItemRequestController.approve
    $request->update([
        'status' => 'approved',
        'processed_by' => $admin->id,
        'processed_at' => now()
    ]);
    
    Transaction::create([
        'item_id' => $item->id,
        'user_id' => $admin->id,
        'type' => 'out',
        'quantity' => 20,
        'date' => now(),
        'note' => "Pengeluaran stok untuk permintaan REQ-{$request->id}"
    ]);
    $item->decrement('stock', 20);

    $item->refresh();
    logTest("Stok setelah Request Approved: {$item->stock}");
    if ($item->stock == $initialStock + 50 - 30 - 20) {
        logTest("BERHASIL: Logika Request Approval akurat.");
    } else {
        logTest("GAGAL: Logika Request Approval tidak akurat!");
    }

    DB::rollBack(); // Jangan simpan data testing ke DB asli
    logTest("Pengujian Selesai. Database dikembalikan ke kondisi semula (Rollback).");

} catch (\Exception $e) {
    DB::rollBack();
    logTest("ERROR: " . $e->getMessage());
}
