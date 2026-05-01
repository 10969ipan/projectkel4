<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WellnessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $articles = [
            [
                'title' => 'Optimalkan Penyerapan Vitamin C & B-Complex',
                'slug' => 'optimalkan-penyerapan-vitamin',
                'keyword' => 'Enervon',
                'content' => 'Mengonsumsi multivitamin seperti Enervon-C paling efektif dilakukan di pagi hari setelah sarapan. Vitamin C bersifat asam, sehingga mengonsumsinya saat perut terisi dapat mencegah iritasi lambung. Selain itu, B-complex membantu metabolisme energi, memberikan Anda tenaga tambahan untuk memulai aktivitas harian. Pastikan juga asupan air putih yang cukup sepanjang hari untuk membantu ginjal memproses kelebihan vitamin larut air ini.',
                'image_path' => 'assets/images/wellness/vitamin.webp',
            ],
            [
                'title' => 'Manajemen Demam & Hidrasi Tubuh',
                'slug' => 'tetap-terhidrasi-saat-demam',
                'keyword' => 'Paracetamol',
                'content' => 'Paracetamol adalah solusi efektif untuk menurunkan demam, namun kinerjanya akan jauh lebih optimal jika dibarengi dengan hidrasi yang kuat. Saat demam, tubuh cenderung kehilangan cairan lebih cepat melalui penguapan kulit. Kami menyarankan minum setidaknya 2,5 - 3 liter air mineral atau jus buah segar selama masa pemulihan. Hindari minuman berkafein seperti kopi atau teh pekat sementara waktu, karena dapat memicu dehidrasi lebih lanjut.',
                'image_path' => 'assets/images/wellness/recovery.webp',
            ],
            [
                'title' => 'Pentingnya Penuntasan Terapi Antibiotik',
                'slug' => 'lengkapkan-dosis-antibiotik',
                'keyword' => 'Amoxicillin',
                'content' => 'Kunci utama kesembuhan dari infeksi bakteri adalah kepatuhan. Meskipun Anda merasa sudah bugar setelah 2-3 hari mengonsumsi Amoxicillin, bakteri di dalam tubuh mungkin belum sepenuhnya mati. Menghentikan dosis secara sepihak berisiko tinggi menyebabkan resistensi antibiotik, di mana bakteri menjadi lebih kuat dan sulit diobati di masa depan. Habiskan seluruh obat sesuai instruksi resep tanpa terputus untuk perlindungan maksimal.',
                'image_path' => 'assets/images/wellness/antibiotic.webp',
            ],
            [
                'title' => 'Gaya Hidup untuk Pasien Hipertensi',
                'slug' => 'pola-makan-sehat-hipertensi',
                'keyword' => 'Amlodipine',
                'content' => 'Penggunaan Amlodipine untuk mengontrol tekanan darah harus didukung oleh pola makan DASH (Dietary Approaches to Stop Hypertension). Fokuskan pada konsumsi buah, sayuran, dan protein tanpa lemak. Kurangi asupan garam harian hingga di bawah 1 sendok teh. Selain itu, aktivitas fisik ringan seperti jalan kaki 30 menit sehari dapat memperkuat otot jantung dan membantu efektivitas obat dalam jangka panjang. Monitoring tekanan darah secara rutin di rumah sangat dianjurkan.',
                'image_path' => 'assets/images/wellness/heart.webp',
            ],
            [
                'title' => 'Langkah Tepat Pertolongan Pertama pada Luka',
                'slug' => 'perawatan-luka-benar',
                'keyword' => 'Betadine',
                'content' => 'Saat terjadi luka lecet atau sayat, langkah pertama bukan langsung mengoleskan obat. Bersihkan luka dengan air mengalir untuk membuang kotoran yang menempel. Gunakan kassa steril untuk mengeringkan area sekitar luka secara perlahan. Baru setelah itu arahkan cairan antiseptik seperti Betadine tepat pada area luka. Jika luka cukup dalam, gunakan plester berpori agar luka tetap mendapatkan sirkulasi udara namun terlindungi dari kontaminasi debu luar.',
                'image_path' => 'assets/images/wellness/wound.webp',
            ],
        ];

        foreach ($articles as $art) {
            \App\Models\HealthArticle::updateOrCreate(['slug' => $art['slug']], $art);
        }
    }
}
