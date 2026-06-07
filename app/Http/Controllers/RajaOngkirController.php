<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaOngkirController extends Controller
{
    /**
     * Menampilkan halaman UI pengecekan ongkos kirim.
     */
    public function index()
    {
        return view('frontend.ongkir');
    }

    /**
     * Mencari destinasi domestik (Kecamatan/Kota) menggunakan Komerce API V2.
     */
    public function searchDestinations(Request $request)
    {
        $search = $request->input('search');
        if (!$search || strlen($search) < 3) {
            return response()->json([
                'success' => false,
                'message' => 'Pencarian minimal 3 karakter.'
            ], 400);
        }

        $apiKey = env('RAJAONGKIR_API_KEY');
        $baseUrl = env('RAJAONGKIR_BASE_URL');

        try {
            $response = Http::withHeaders([
                'key' => $apiKey
            ])->timeout(10)->get($baseUrl . '/destination/domestic-destination', [
                'search' => $search
            ]);

            $data = $response->json();

            if (isset($data['code']) && $data['code'] !== 200) {
                return response()->json([
                    'success' => false,
                    'message' => $data['message'] ?? 'Gagal mengambil data destinasi.'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $data['data'] ?? []
            ]);
        } catch (\Exception $e) {
            Log::error('RajaOngkir searchDestinations Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat menghubungi Komerce API.'
            ], 500);
        }
    }

    /**
     * Menghitung ongkos kirim menggunakan Komerce API V2.
     */
    public function getCost(Request $request)
    {
        $origin = $request->input('origin', '4816'); // Default origin: BANDUNG, Jawa Barat (sesuaikan dengan toko Anda)
        $destination = $request->input('destination'); // Ini harus destination_id dari pencarian
        $weight = $request->input('weight', 1000); // Default 1kg
        $courier = $request->input('courier', 'jne'); // JNE, SICEPAT, JNT, dll (sesuai API Komerce)

        if (!$destination) {
            return response()->json([
                'success' => false,
                'message' => 'Destinasi belum dipilih dengan benar.'
            ], 400);
        }

        $apiKey = env('RAJAONGKIR_API_KEY');
        $baseUrl = env('RAJAONGKIR_BASE_URL');

        try {
            $response = Http::withHeaders([
                'key' => $apiKey,
                'Content-Type' => 'application/x-www-form-urlencoded'
            ])->asForm()->timeout(15)->post($baseUrl . '/calculate/domestic-cost', [
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => strtolower($courier),
            ]);

            $data = $response->json();

            // Log the request to debug API issues
            Log::info('Komerce Cost Check', ['request' => $request->all(), 'response' => $data]);

            if (!isset($data['meta']) || $data['meta']['code'] != 200) {
                return response()->json([
                    'success' => false,
                    'message' => $data['meta']['message'] ?? 'Layanan ekspedisi tidak tersedia atau terjadi kesalahan.'
                ], 400);
            }

            // Komerce API mengembalikan $data['data'] berupa array services
            // Kita akan membungkusnya dalam struktur yang dimengerti oleh frontend (Mirip struktur lama atau disesuaikan)
            // Di frontend, sebelumnya kita mencari `data.rajaongkir.results[0].costs`
            // Namun karena kita memperbarui frontend juga nanti, kita bisa kembalikan data Komerce langsung.
            
            // Format fallback agar kompatibel (jika masih butuh format lama)
            // Komerce: {"name":"JNE","code":"jne","service":"REG","cost":8000,"etd":"1 day"}
            
            $formattedCosts = [];
            foreach ($data['data'] as $cost) {
                $formattedCosts[] = [
                    'service' => $cost['service'],
                    'description' => $cost['description'] ?? $cost['name'],
                    'cost' => [
                        [
                            'value' => $cost['cost'],
                            'etd' => $cost['etd'],
                            'note' => ''
                        ]
                    ]
                ];
            }

            // Kita kirim respons format Komerce asli, ditambah mock RajaOngkir agar kompatibel jika belum diganti
            return response()->json([
                'success' => true,
                'komerce' => $data['data'],
                'rajaongkir' => [
                    'status' => ['code' => 200],
                    'results' => [
                        [
                            'code' => $courier,
                            'name' => strtoupper($courier),
                            'costs' => $formattedCosts
                        ]
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('RajaOngkir getCost Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghitung ongkos kirim: ' . $e->getMessage()
            ], 500);
        }
    }
}
