<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        // Barang masuk
        Transaction::create([
            'item_id' => 1, // Paracetamol
            'user_id' => 1,
            'type' => 'in',
            'quantity' => 50,
            'date' => now()->subDays(10),
            'note' => 'Stok awal dari supplier A'
        ]);

        Transaction::create([
            'item_id' => 2, // Amoxicillin
            'user_id' => 1,
            'type' => 'in',
            'quantity' => 30,
            'date' => now()->subDays(8),
            'note' => 'Stok awal dari supplier B'
        ]);

        // Barang keluar
        Transaction::create([
            'item_id' => 1,
            'user_id' => 2,
            'type' => 'out',
            'quantity' => 5,
            'date' => now()->subDays(5),
            'note' => 'Permintaan IGD'
        ]);
    }
}