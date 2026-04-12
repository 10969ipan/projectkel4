<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class MedicalDescriptionSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk memperbarui deskripsi medis obat.
     */
    public function run()
    {
        $descriptions = [
            'Paracetamol 500mg' => 'Obat pereda nyeri (analgesik) dan penurun demam (antipiretik). Efektif meredakan sakit kepala, sakit gigi, dan nyeri ringan hingga sedang lainnya tanpa mengiritasi lambung.',
            'Amoxicillin 500mg' => 'Antibiotik golongan penisilin yang digunakan untuk mengobati berbagai jenis infeksi bakteri (saluran napas, kulit, telinga). Harus dikonsumsi sampai habis sesuai petunjuk dokter untuk mencegah resistensi.',
            'Cetirizine 10mg' => 'Obat antihistamine generasi kedua yang efektif meredakan gejala alergi seperti bersin-bersin, hidung berair, mata gatal, serta gatal-gatal pada kulit (urtikaria) dengan efek kantuk minimal.',
            'Betadine 30ml' => 'Antiseptic cair mengandung Povidone-Iodine 10% untuk mencegah infeksi pada luka bakar ringan, luka gores, atau luka pasca operasi. Efektif membunuh kuman, bakteri, dan virus pada permukaan kulit.',
            'Enervon-C' => 'Suplemen vitamin yang mengombinasikan Vitamin C (500mg) dengan Vitamin B Kompleks. Berfungsi menjaga daya tahan tubuh, meningkatkan metabolisme, serta membantu masa pemulihan setelah sakit.',
            'Ibuprofen 400mg' => 'Obat anti-inflamasi nonsteroid (NSAID) yang bekerja meredakan peradangan, nyeri tulang/otot, sakit gigi, serta nyeri haid. Disarankan dikonsumsi sesudah makan untuk kenyamanan lambung.',
            'Salbutamol Inhaler' => 'Obat bronkodilator untuk melegakan saluran pernapasan pada kondisi asma atau PPOK. Bekerja cepat membuka jalan napas yang menyempit akibat serangan sesak napas akut.',
            'Amlodipine 5mg' => 'Obat golongan kalsium antagonis yang digunakan untuk menurunkan tekanan darah tinggi (hipertensi) dan membantu mengontrol nyeri dada (angina). Membantu pembuluh darah agar lebih rileks.',
            'Sangobion' => 'Suplemen zat besi yang diperkaya dengan Vitamin B12, Asam Folat, dan Vitamin C. Dirancang untuk membantu pembentukan sel darah merah dan mengatasi gejala anemia seperti 5L (Lemah, Letih, Lesu, Lelah, Lalai).',
            'Tolak Angin' => 'Obat herbal standar (OHT) yang diformulasikan dari kapulaga, adas, jahe, dan kayu ules. Terbukti efektif mengatasi gejala masuk angin, perut kembung, mual, serta membantu meningkatkan imunitas tubuh.',
        ];

        foreach ($descriptions as $name => $desc) {
            Item::where('name', $name)->update(['description' => $desc]);
        }
    }
}
