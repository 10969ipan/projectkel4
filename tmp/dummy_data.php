<?php

use App\Models\Item;
use App\Models\ItemSize;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Transaction;
use App\Models\ItemRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// Clear existing items and sizes to make it clean
DB::statement('SET FOREIGN_KEY_CHECKS=0;');
ItemSize::truncate();
Item::truncate();
Transaction::truncate();
ItemRequest::truncate();
DB::statement('SET FOREIGN_KEY_CHECKS=1;');

$medicines = [
    ['code' => 'OB-001', 'name' => 'Paracetamol 500mg', 'generic_name' => 'Paracetamol', 'manufacturer' => 'PT Kimia Farma', 'category_id' => 1, 'unit_id' => 1, 'price' => 5000],
    ['code' => 'OK-001', 'name' => 'Amoxicillin 500mg', 'generic_name' => 'Amoxicillin', 'manufacturer' => 'PT Sanbe Farma', 'category_id' => 3, 'unit_id' => 3, 'price' => 15000],
    ['code' => 'OBT-001', 'name' => 'Cetirizine 10mg', 'generic_name' => 'Cetirizine HCl', 'manufacturer' => 'PT Kalbe Farma', 'category_id' => 2, 'unit_id' => 1, 'price' => 12000],
    ['code' => 'AL-001', 'name' => 'Betadine 30ml', 'generic_name' => 'Povidone Iodine', 'manufacturer' => 'PT Mundipharma', 'category_id' => 5, 'unit_id' => 4, 'price' => 25000],
    ['code' => 'VIT-001', 'name' => 'Enervon-C', 'generic_name' => 'Multivitamin', 'manufacturer' => 'PT Darya Varia', 'category_id' => 6, 'unit_id' => 3, 'price' => 8000],
    ['code' => 'OBT-002', 'name' => 'Ibuprofen 400mg', 'generic_name' => 'Ibuprofen', 'manufacturer' => 'PT Indofarma', 'category_id' => 2, 'unit_id' => 1, 'price' => 10000],
    ['code' => 'OK-002', 'name' => 'Salbutamol Inhaler', 'generic_name' => 'Salbutamol', 'manufacturer' => 'PT GlaxoSmithKline', 'category_id' => 3, 'unit_id' => 5, 'price' => 150000],
    ['code' => 'OK-003', 'name' => 'Amlodipine 5mg', 'generic_name' => 'Amlodipine Besylate', 'manufacturer' => 'PT Dexa Medica', 'category_id' => 3, 'unit_id' => 3, 'price' => 7000],
    ['code' => 'VIT-002', 'name' => 'Sangobion', 'generic_name' => 'Iron + Multivitamins', 'manufacturer' => 'PT Merck', 'category_id' => 6, 'unit_id' => 2, 'price' => 20000],
    ['code' => 'HRB-001', 'name' => 'Tolak Angin', 'generic_name' => 'Herbal Extract', 'manufacturer' => 'PT Sido Muncul', 'category_id' => 7, 'unit_id' => 8, 'price' => 4000],
];

$adminId = User::where('role', 'admin')->first()->id ?? 1;
$staffIds = User::where('role', 'staff')->pluck('id')->toArray();
if (empty($staffIds)) $staffIds = [$adminId];

foreach ($medicines as $medData) {
    $item = Item::create(array_merge($medData, ['stock' => 0]));
    
    // Create 1-2 batches
    $numBatches = rand(1, 2);
    $totalStock = 0;
    
    for ($i = 0; $i < $numBatches; $i++) {
        $batchNum = 'B' . Carbon::now()->year . '-' . strtoupper(Str::random(5));
        
        // Varying expiry dates: Expired, Near (30 days), and Future
        $expiryType = rand(1, 3);
        if ($expiryType == 1) { // Expired
            $expiry = Carbon::now()->subMonths(rand(1, 6));
        } elseif ($expiryType == 2) { // Near Expiry
            $expiry = Carbon::now()->addDays(rand(5, 25));
        } else { // Far Future
            $expiry = Carbon::now()->addMonths(rand(6, 24));
        }
        
        $stock = rand(20, 200);
        $totalStock += $stock;
        
        $batch = ItemSize::create([
            'item_id' => $item->id,
            'batch_number' => $batchNum,
            'expiry_date' => $expiry->toDateString(),
            'stock' => $stock
        ]);
        
        // Add "In" transaction for this batch
        Transaction::create([
            'item_id' => $item->id,
            'item_size_id' => $batch->id,
            'user_id' => $adminId,
            'type' => 'in',
            'quantity' => $stock,
            'date' => Carbon::now()->subDays(rand(10, 60)),
            'note' => 'Stok awal batch ' . $batchNum
        ]);
        
        // Add "Out" transaction for some batches
        if (rand(0, 1)) {
            $outQty = rand(5, 15);
            if ($outQty < $stock) {
                Transaction::create([
                    'item_id' => $item->id,
                    'item_size_id' => $batch->id,
                    'user_id' => $staffIds[array_rand($staffIds)],
                    'type' => 'out',
                    'quantity' => $outQty,
                    'date' => Carbon::now()->subDays(rand(1, 5)),
                    'note' => 'Penjualan resep'
                ]);
                $batch->decrement('stock', $outQty);
                $totalStock -= $outQty;
            }
        }
    }
    
    $item->update(['stock' => $totalStock]);
    
    // Random Request for some items
    if (rand(0, 1)) {
        $reqQty = rand(10, 50);
        $status = ['pending', 'approved', 'rejected'][rand(0, 2)];
        
        $req = [
            'item_id' => $item->id,
            'user_id' => $staffIds[array_rand($staffIds)],
            'quantity' => $reqQty,
            'reason' => 'Stok mulai menipis di rak depan',
            'status' => $status
        ];
        
        if ($status != 'pending') {
            $req['processed_by'] = $adminId;
            $req['processed_at'] = Carbon::now()->subDays(1);
            if ($status == 'rejected') {
                $req['rejection_reason'] = 'Tunggu pengiriman distributor minggu depan';
            }
        }
        
        ItemRequest::create($req);
    }
}

echo "Dummy data generated successfully!";
