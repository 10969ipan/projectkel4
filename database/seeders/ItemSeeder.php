<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\ItemSize;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus data lama untuk menghindari duplikasi saat seeding ulang
        DB::table('item_sizes')->delete();
        DB::table('items')->delete();

        $itemsData = [
            [
                'code' => 'BA-001', 'name' => 'Setelan Piyama Anak Dino', 'category_id' => 1, 'unit_id' => 1, 'price' => 45000,
                'description' => 'Piyama bahan katun motif dinosaurus untuk usia 3-5 tahun',
                'variants' => ['S', 'M', 'L']
            ],
            [
                'code' => 'KM-001', 'name' => 'Kemeja Flannel Kotak-kotak', 'category_id' => 2, 'unit_id' => 1, 'price' => 120000,
                'description' => 'Kemeja lengan panjang bahan flannel premium',
                'variants' => ['M', 'L', 'XL', 'XXL']
            ],
            [
                'code' => 'KM-002', 'name' => 'Kemeja Polos Putih Slimfit', 'category_id' => 2, 'unit_id' => 1, 'price' => 150000,
                'description' => 'Kemeja formal bahan katun oxford',
                'variants' => ['S', 'M', 'L', 'XL']
            ],
            [
                'code' => 'CL-001', 'name' => 'Celana Chino Panjang', 'category_id' => 3, 'unit_id' => 1, 'price' => 175000,
                'description' => 'Celana chino warna khaki',
                'variants' => ['28', '30', '32', '34', '36']
            ],
            [
                'code' => 'CL-002', 'name' => 'Jeans Denim Regular', 'category_id' => 3, 'unit_id' => 1, 'price' => 250000,
                'description' => 'Celana jeans bahan tebal warna biru dongker',
                'variants' => ['29', '30', '31', '32', '33', '34']
            ],
            [
                'code' => 'SP-001', 'name' => 'Sepatu Sneaker Classic', 'category_id' => 4, 'unit_id' => 1, 'price' => 300000,
                'description' => 'Sepatu olahraga bahan sintetis',
                'variants' => ['39', '40', '41', '42', '43', '44']
            ],
            [
                'code' => 'JK-001', 'name' => 'Jaket Hoodie Fleece', 'category_id' => 5, 'unit_id' => 1, 'price' => 200000,
                'description' => 'Jaket hoodie bahan fleece',
                'variants' => ['M', 'L', 'XL', 'XXL']
            ],
            [
                'code' => 'TS-001', 'name' => 'Tas Ransel Laptop', 'category_id' => 6, 'unit_id' => 1, 'price' => 400000,
                'description' => 'Tas ransel dengan kompartemen khusus laptop hingga 15 inci',
                'variants' => ['All Size']
            ],
            [
                'code' => 'AK-001', 'name' => 'Topi Baseball Casual', 'category_id' => 7, 'unit_id' => 1, 'price' => 80000,
                'description' => 'Topi baseball bahan katun dengan desain casual',
                'variants' => ['All Size']
            ],
            [
                'code' => 'AK-002', 'name' => 'Gelang Kulit Fashion', 'category_id' => 7, 'unit_id' => 1, 'price' => 60000,
                'description' => 'Gelang bahan kulit asli dengan desain trendy',
                'variants' => ['All Size']
            ],
            [
                'code' => 'SP-002', 'name' => 'Sepatu Sneaker Trendy', 'category_id' => 4, 'unit_id' => 1, 'price' => 250000,
                'description' => 'Sepatu olahraga bahan sintetis dengan desain trendy',
                'variants' => ['38', '39', '40', '41', '42']
            ],
        ];

        foreach ($itemsData as $data) {
            $variants = $data['variants'];
            unset($data['variants']);

            $totalStock = 0;
            $itemSizes = [];

            // Buat varian ukuran dan hitung total stok
            foreach ($variants as $size) {
                $stock = rand(5, 50);
                $totalStock += $stock;
                $itemSizes[] = ['size' => $size, 'stock' => $stock];
            }

            // Update data item utama
            $data['stock'] = $totalStock;
            $data['size'] = implode(', ', $variants); // Ringkasan ukuran

            // Buat item utama
            $item = Item::create($data);

            // Buat dan lampirkan varian ukuran
            foreach ($itemSizes as &$sizeData) {
                $sizeData['item_id'] = $item->id;
            }
            ItemSize::insert($itemSizes);
        }
    }
}
