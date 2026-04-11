<?php

namespace Database\Seeders;

use App\Models\ItemRequest;
use Illuminate\Database\Seeder;

class ItemRequestSeeder extends Seeder
{
    public function run(): void
    {
        ItemRequest::create([
            'item_id' => 1, // Paracetamol
            'user_id' => 2,
            'quantity' => 10,
            'reason' => 'Stok di unit gawat darurat mulai menipis',
            'status' => 'pending'
        ]);

        ItemRequest::create([
            'item_id' => 3, // Vitamin C
            'user_id' => 3,
            'quantity' => 50,
            'reason' => 'Untuk paket bantuan kesehatan warga',
            'status' => 'approved',
            'processed_by' => 1,
            'processed_at' => now()->subDays(2)
        ]);

        ItemRequest::create([
            'item_id' => 2, // Amoxicillin
            'user_id' => 2,
            'quantity' => 20,
            'reason' => 'Permintaan tambahan poliklinik rawat jalan',
            'status' => 'rejected',
            'rejection_reason' => 'Dosis 500mg sedang kosong di pusat',
            'processed_by' => 1,
            'processed_at' => now()->subDays(1)
        ]);
    }
}