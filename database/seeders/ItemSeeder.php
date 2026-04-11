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
                'code' => 'OBB-001', 'name' => 'Paracetamol 500mg', 'category_id' => 1, 'unit_id' => 2, 'price' => 5000,
                'requires_prescription' => false, 'description' => 'Meredakan demam dan nyeri ringan hingga sedang.',
                'stock' => 100, 'size' => '10 Tablet/Strip'
            ],
            [
                'code' => 'OBK-001', 'name' => 'Amoxicillin 500mg', 'category_id' => 3, 'unit_id' => 2, 'price' => 12000,
                'requires_prescription' => true, 'description' => 'Antibiotik spektrum luas untuk pengobatan infeksi bakteri.',
                'stock' => 50, 'size' => '10 Kaplet/Strip'
            ],
            [
                'code' => 'VIT-001', 'name' => 'Vitamin C 500mg', 'category_id' => 4, 'unit_id' => 2, 'price' => 8500,
                'requires_prescription' => false, 'description' => 'Suplemen untuk menjaga daya tahan tubuh.',
                'stock' => 200, 'size' => '10 Tablet/Strip'
            ],
            [
                'code' => 'ALKES-001', 'name' => 'Masker Medis 3-Ply', 'category_id' => 5, 'unit_id' => 6, 'price' => 35000,
                'requires_prescription' => false, 'description' => 'Masker bedah penyaring bakteri dan partikel debu.',
                'stock' => 500, 'size' => '50 Pcs/Box'
            ],
            [
                'code' => 'OBT-001', 'name' => 'Sanmol Drop 15ml', 'category_id' => 6, 'unit_id' => 3, 'price' => 25000,
                'requires_prescription' => false, 'description' => 'Penurun demam khusus bayi dengan pipet takar.',
                'stock' => 30, 'size' => '15 ml'
            ],
            [
                'code' => 'PWT-001', 'name' => 'Betadine Antiseptic 15ml', 'category_id' => 7, 'unit_id' => 3, 'price' => 18000,
                'requires_prescription' => false, 'description' => 'Cairan antiseptik untuk mencegah infeksi pada luka.',
                'stock' => 45, 'size' => '15 ml'
            ],
            [
                'code' => 'OBB-002', 'name' => 'Promag Tablet', 'category_id' => 2, 'unit_id' => 2, 'price' => 9000,
                'requires_prescription' => false, 'description' => 'Obat sakit maag dan kembung.',
                'stock' => 150, 'size' => '12 Tablet/Strip'
            ],
            [
                'code' => 'VIT-002', 'name' => 'Antangin Sachet', 'category_id' => 4, 'unit_id' => 5, 'price' => 3500,
                'requires_prescription' => false, 'description' => 'Sirup herbal untuk meredakan masuk angin.',
                'stock' => 300, 'size' => '15 ml/Sachet'
            ],
            [
                'code' => 'PWT-002', 'name' => 'Salep Kulit 88', 'category_id' => 7, 'unit_id' => 4, 'price' => 15000,
                'requires_prescription' => false, 'description' => 'Salep untuk mengatasi gatal-gatal kurap dan panu.',
                'stock' => 60, 'size' => '6 gr/Tube'
            ],
            [
                'code' => 'ALKES-002', 'name' => 'Termometer Digital', 'category_id' => 5, 'unit_id' => 7, 'price' => 45000,
                'requires_prescription' => false, 'description' => 'Alat pengukur suhu tubuh digital akurat.',
                'stock' => 25, 'size' => '1 Unit'
            ],
        ];

        foreach ($itemsData as $data) {
            Item::create($data);
        }
    }
}
