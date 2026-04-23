@extends('layouts.frontend')

@section('title', 'Cek Ongkos Kirim')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl" x-data="ongkirApp()">
    
    <!-- Header -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
            <i class="fas fa-truck-fast text-primary-600 mr-2"></i>Cek Ongkos Kirim
        </h1>
        <p class="text-gray-500 mt-2">Dapatkan estimasi biaya pengiriman dari berbagai ekspedisi secara real-time.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <!-- Form Cek Ongkir -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-6 border-b border-gray-100 pb-3">Rincian Pengiriman</h2>
            
            <form @submit.prevent="checkOngkir" id="ongkirForm" class="space-y-5">
                
                <!-- Provinsi Asal (Fixed to DKI Jakarta for Store typically, but let's just make it hidden or fixed. Wait, the module asks for Origin = 501. 501 is DI Yogyakarta in RajaOngkir. Let's keep it fixed in JS like the module) -->

                <!-- Provinsi Tujuan -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Provinsi Tujuan</label>
                    <select x-model="selectedProvince" @change="fetchCities" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" required>
                        <option value="">-- Pilih Provinsi --</option>
                        <template x-for="prov in provinces" :key="prov.province_id">
                            <option :value="prov.province_id" x-text="prov.province"></option>
                        </template>
                    </select>
                </div>

                <!-- Kota Tujuan -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Kota / Kabupaten Tujuan</label>
                    <select x-model="selectedCity" id="city" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" :disabled="cities.length === 0" required>
                        <option value="">-- Pilih Kota --</option>
                        <template x-for="city in cities" :key="city.city_id">
                            <option :value="city.city_id" x-text="city.city_name"></option>
                        </template>
                    </select>
                </div>

                <!-- Berat Barang -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Berat Barang (Gram)</label>
                    <div class="relative rounded-md shadow-sm">
                        <input type="number" x-model="weight" min="1" class="w-full rounded-xl border-gray-300 pl-4 pr-12 focus:border-primary-500 focus:ring-primary-500" placeholder="1000" required>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm font-medium">gr</span>
                        </div>
                    </div>
                </div>

                <!-- Kurir -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Pilih Ekspedisi</label>
                    <select x-model="courier" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" required>
                        <option value="">-- Pilih Kurir --</option>
                        <option value="jne">JNE (Jalur Nugraha Ekakurir)</option>
                        <option value="pos">POS Indonesia</option>
                        <option value="tiki">TIKI (Citra Van Titipan Kilat)</option>
                    </select>
                </div>

                <!-- Tombol Submit -->
                <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200" :disabled="loading">
                    <span x-show="!loading"><i class="fas fa-search mr-2"></i> Cek Harga Pengiriman</span>
                    <span x-show="loading"><i class="fas fa-spinner fa-spin mr-2"></i> Menghitung...</span>
                </button>
            </form>
        </div>

        <!-- Hasil Ongkir -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col h-full">
            <h2 class="text-xl font-bold text-gray-800 mb-6 border-b border-gray-100 pb-3">Hasil Pencarian</h2>
            
            <div x-show="results.length === 0 && !loading && !error" class="flex-grow flex flex-col items-center justify-center text-center text-gray-400 py-10">
                <i class="fas fa-box-open text-6xl mb-4 text-gray-200"></i>
                <p>Silakan lengkapi form di samping untuk melihat tarif pengiriman.</p>
            </div>

            <div x-show="error" class="p-4 bg-red-50 text-red-600 rounded-xl mb-4 text-sm font-medium border border-red-100">
                <i class="fas fa-exclamation-circle mr-2"></i> <span x-text="error"></span>
            </div>

            <div x-show="results.length > 0" class="space-y-4 overflow-y-auto max-h-[400px] pr-2 custom-scrollbar">
                <template x-for="(cost, index) in results" :key="index">
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 hover:border-primary-300 transition-colors duration-200">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h3 class="font-bold text-gray-900 uppercase" x-text="cost.service"></h3>
                                <p class="text-xs text-gray-500" x-text="cost.description"></p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                Tersedia
                            </span>
                        </div>
                        <div class="mt-3 flex justify-between items-end">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Estimasi Tiba</p>
                                <p class="font-semibold text-gray-700"><i class="far fa-clock mr-1 text-primary-500"></i> <span x-text="cost.cost[0].etd + ' Hari'"></span></p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 mb-1">Tarif</p>
                                <p class="text-lg font-black text-primary-600">Rp <span x-text="new Intl.NumberFormat('id-ID').format(cost.cost[0].value)"></span></p>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9; 
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1; 
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8; 
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('ongkirApp', () => ({
            provinces: [],
            cities: [],
            selectedProvince: '',
            selectedCity: '',
            weight: 1000,
            courier: '',
            results: [],
            loading: false,
            error: null,

            init() {
                this.fetchProvinces();
            },

            async fetchProvinces() {
                try {
                    const response = await fetch('/provinces');
                    const data = await response.json();
                    if (data.rajaongkir.status.code === 200) {
                        this.provinces = data.rajaongkir.results;
                    } else {
                        console.error('Failed to fetch provinces', data.rajaongkir.status.description);
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            },

            async fetchCities() {
                this.selectedCity = '';
                this.cities = [];
                
                if (!this.selectedProvince) return;

                try {
                    const response = await fetch(`/cities?province_id=${this.selectedProvince}`);
                    const data = await response.json();
                    if (data.rajaongkir.status.code === 200) {
                        this.cities = data.rajaongkir.results;
                    } else {
                        console.error('Failed to fetch cities', data.rajaongkir.status.description);
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            },

            async checkOngkir() {
                if (!this.selectedCity || !this.weight || !this.courier) {
                    this.error = "Mohon lengkapi semua data form.";
                    return;
                }

                this.loading = true;
                this.error = null;
                this.results = [];

                try {
                    const response = await fetch('/cost', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            origin: 501, // Module default (DI Yogyakarta)
                            destination: this.selectedCity,
                            weight: this.weight,
                            courier: this.courier
                        })
                    });

                    const data = await response.json();
                    
                    if (data.rajaongkir.status.code === 200) {
                        this.results = data.rajaongkir.results[0].costs;
                        if(this.results.length === 0) {
                             this.error = "Kurir tidak tersedia untuk rute ini.";
                        }
                    } else {
                        this.error = "Gagal memuat tarif: " + data.rajaongkir.status.description;
                    }
                } catch (error) {
                    this.error = "Terjadi kesalahan jaringan. Coba beberapa saat lagi.";
                    console.error('Error:', error);
                } finally {
                    this.loading = false;
                }
            }
        }));
    });
</script>
@endsection
