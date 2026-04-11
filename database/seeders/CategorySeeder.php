<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::create(['name' => 'Obat Bebas', 'description' => 'Obat yang dapat dibeli tanpa resep dokter (Tanda Bulat Hijau)']);
        Category::create(['name' => 'Obat Bebas Terbatas', 'description' => 'Obat keras yang dapat dibeli tanpa resep dokter dengan peringatan (Tanda Bulat Biru)']);
        Category::create(['name' => 'Obat Keras', 'description' => 'Obat yang hanya dapat dibeli dengan resep dokter (Tanda Bulat Merah K)']);
        Category::create(['name' => 'Vitamin & Suplemen', 'description' => 'Produk untuk menjaga daya tahan tubuh dan nutrisi']);
        Category::create(['name' => 'Alat Kesehatan', 'description' => 'Perangkat atau peralatan untuk keperluan medis']);
        Category::create(['name' => 'Produk Bayi & Anak', 'description' => 'Obat-obatan dan perawatan khusus balita']);
        Category::create(['name' => 'Perawatan Tubuh', 'description' => 'Produk perawatan kulit, antiseptik, dan higienis']);
        Category::create(['name' => 'Lain-lain', 'description' => 'Kategori umum lainnya']);
    }
}