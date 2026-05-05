<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
     * Mendapatkan daftar provinsi dari API RajaOngkir (Mocked).
     */
    public function getProvinces()
    {
        $apiKey = env('RAJAONGKIR_API_KEY');
        $baseUrl = env('RAJAONGKIR_BASE_URL');

        if (!$apiKey || !$baseUrl) {
            return $this->mockProvinces();
        }

        $response = Http::withHeaders([
            'key' => $apiKey
        ])->get($baseUrl . '/province');

        $data = $response->json();
        
        // MOCK DATA IF API IS DEAD (410 GONE)
        if (isset($data['code']) && $data['code'] == 410 || !isset($data['rajaongkir'])) {
            return $this->mockProvinces();
        }

        return response()->json($data);
    }

    private function mockProvinces()
    {
        return response()->json([
            'rajaongkir' => [
                'status' => ['code' => 200, 'description' => 'OK (Mocked)'],
                'results' => [
                    ['province_id' => "1", 'province' => "Bali"],
                    ['province_id' => "2", 'province' => "Bangka Belitung"],
                    ['province_id' => "3", 'province' => "Banten"],
                    ['province_id' => "5", 'province' => "DI Yogyakarta"],
                    ['province_id' => "6", 'province' => "DKI Jakarta"],
                    ['province_id' => "9", 'province' => "Jawa Barat"],
                    ['province_id' => "10", 'province' => "Jawa Tengah"],
                    ['province_id' => "11", 'province' => "Jawa Timur"],
                ]
            ]
        ]);
    }

    /**
     * Mendapatkan daftar kota berdasarkan province_id (Mocked).
     */
    public function getCities(Request $request)
    {
        $provinceId = $request->input('province_id');
        $apiKey = env('RAJAONGKIR_API_KEY');
        $baseUrl = env('RAJAONGKIR_BASE_URL');

        if (!$apiKey || !$baseUrl) {
            return $this->mockCities($provinceId);
        }

        $response = Http::withHeaders([
            'key' => $apiKey
        ])->get($baseUrl . '/city', [
            'province' => $provinceId
        ]);

        $data = $response->json();

        // MOCK DATA IF API IS DEAD
        if (isset($data['code']) && $data['code'] == 410 || !isset($data['rajaongkir'])) {
            return $this->mockCities($provinceId);
        }

        return response()->json($data);
    }

    private function mockCities($provinceId)
    {
        $allCities = [
            "1" => [['city_id' => "17", 'city_name' => "Denpasar"]],
            "2" => [['city_id' => "18", 'city_name' => "Pangkal Pinang"]],
            "3" => [
                ['city_id' => "10", 'city_name' => "Kota Tangerang"],
                ['city_id' => "11", 'city_name' => "Tangerang Selatan"],
            ],
            "5" => [
                ['city_id' => "12", 'city_name' => "Yogyakarta"],
                ['city_id' => "13", 'city_name' => "Sleman"],
                ['city_id' => "14", 'city_name' => "Bantul"],
            ],
            "6" => [
                ['city_id' => "5", 'city_name' => "Jakarta Selatan"],
                ['city_id' => "6", 'city_name' => "Jakarta Pusat"],
                ['city_id' => "7", 'city_name' => "Jakarta Barat"],
                ['city_id' => "8", 'city_name' => "Jakarta Timur"],
                ['city_id' => "9", 'city_name' => "Jakarta Utara"],
            ],
            "9" => [
                ['city_id' => "1", 'city_name' => "Kota Bandung"],
                ['city_id' => "2", 'city_name' => "Kota Bekasi"],
                ['city_id' => "3", 'city_name' => "Kota Bogor"],
                ['city_id' => "4", 'city_name' => "Kota Depok"],
            ],
            "10" => [
                ['city_id' => "19", 'city_name' => "Semarang"],
                ['city_id' => "20", 'city_name' => "Surakarta"],
            ],
            "11" => [
                ['city_id' => "15", 'city_name' => "Surabaya"],
                ['city_id' => "16", 'city_name' => "Malang"],
            ]
        ];

        $results = isset($allCities[$provinceId]) ? $allCities[$provinceId] : [];

        return response()->json([
            'rajaongkir' => [
                'status' => ['code' => 200, 'description' => 'OK (Mocked)'],
                'results' => $results
            ]
        ]);
    }

    /**
     * Menghitung ongkos kirim (Mocked).
     */
    public function getCost(Request $request)
    {
        $origin = $request->input('origin');
        $destination = $request->input('destination');
        $weight = $request->input('weight');
        $courier = $request->input('courier');

        $apiKey = env('RAJAONGKIR_API_KEY');
        $baseUrl = env('RAJAONGKIR_BASE_URL');

        if (!$apiKey || !$baseUrl) {
            return $this->mockCost($origin, $destination, $weight, $courier);
        }

        $response = Http::withHeaders([
            'key' => $apiKey
        ])->post($baseUrl . '/cost', [
            'origin' => $origin,
            'destination' => $destination,
            'weight' => $weight,
            'courier' => $courier,
        ]);

        $data = $response->json();

        // MOCK DATA IF API IS DEAD
        if (isset($data['code']) && $data['code'] == 410 || !isset($data['rajaongkir']) || isset($data['rajaongkir']['status']['code']) && $data['rajaongkir']['status']['code'] != 200) {
            return $this->mockCost($origin, $destination, $weight, $courier);
        }

        return response()->json($data);
    }

    private function mockCost($origin, $destination, $weight, $courier)
    {
        // Generate a fake but deterministic base price based on origin and destination
        $originId = intval($origin) ?: 1;
        $destId = intval($destination) ?: 1;
        $distanceFactor = abs($destId - $originId) * 1500;
        $baseCost = 10000 + $distanceFactor;

        if ($courier === 'jne') {
            $costs = [
                ['service' => 'OKE', 'description' => 'Ongkos Kirim Ekonomis', 'cost' => [['value' => $baseCost, 'etd' => '3-4']]],
                ['service' => 'REG', 'description' => 'Layanan Reguler', 'cost' => [['value' => $baseCost + 5000, 'etd' => '2-3']]],
                ['service' => 'YES', 'description' => 'Yakin Esok Sampai', 'cost' => [['value' => $baseCost + 15000, 'etd' => '1-1']]],
            ];
        } else if ($courier === 'tiki') {
            $costs = [
                ['service' => 'ECO', 'description' => 'Economy Service', 'cost' => [['value' => $baseCost - 1000, 'etd' => '4-5']]],
                ['service' => 'REG', 'description' => 'Regular Service', 'cost' => [['value' => $baseCost + 4000, 'etd' => '2-3']]],
                ['service' => 'ONS', 'description' => 'Over Night Service', 'cost' => [['value' => $baseCost + 14000, 'etd' => '1-1']]],
            ];
        } else {
            $costs = [
                ['service' => 'Paket Kilat Khusus', 'description' => 'Paket Kilat Khusus', 'cost' => [['value' => $baseCost + 2000, 'etd' => '2-4']]],
                ['service' => 'Express', 'description' => 'Express', 'cost' => [['value' => $baseCost + 12000, 'etd' => '1-2']]],
            ];
        }

        return response()->json([
            'rajaongkir' => [
                'status' => ['code' => 200, 'description' => 'OK (Mocked API)'],
                'results' => [
                    [
                        'code' => $courier,
                        'name' => strtoupper($courier),
                        'costs' => $costs
                    ]
                ]
            ]
        ]);
    }
}
