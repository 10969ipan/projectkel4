@extends('layouts.app')

@section('title', 'Tambah Transaksi Baru')

@section('header')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Tambah Transaksi Baru</h1>
        <div>
            <a href="{{ route('transactions.index') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <form action="{{ route('transactions.store') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label for="item_id" class="block text-sm font-medium text-gray-700 mb-1">Barang</label>
                        {{-- UPDATE: Hapus informasi total stok dari opsi, karena stok akan ditampilkan per ukuran --}}
                        <select name="item_id" id="item_id" required
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            <option value="">Pilih Barang</option>
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}
                                    data-unit-symbol="{{ $item->unit->symbol }}">
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('item_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Jenis Transaksi</label>
                        <select name="type" id="type" required
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            <option value="">Pilih Jenis</option>
                            <option value="in" {{ old('type') == 'in' ? 'selected' : '' }}>Masuk (Tambah stok)</option>
                            <option value="out" {{ old('type') == 'out' ? 'selected' : '' }}>Keluar (Kurangi stok)
                            </option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- BARU: Dropdown Varian Ukuran --}}
                    <div id="item-size-section" style="{{ old('item_id') ? '' : 'display: none;' }}">
                        <label for="item_size_id" class="block text-sm font-medium text-gray-700 mb-1">Varian Ukuran</label>
                        {{-- Dropdown ini akan diisi melalui JavaScript setelah item_id dipilih --}}
                        <select name="item_size_id" id="item_size_id" required
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            <option value="">Pilih Ukuran</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500" id="stock-info"></p>
                        @error('item_size_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Quantity Input (sudah ada, tapi akan divalidasi dengan stock ukuran) --}}
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                        <input type="number" name="quantity" id="quantity" required value="{{ old('quantity', 1) }}" min="1"
                            class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md px-3 py-2 border">
                        @error('quantity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        {{-- Pesan error stok dinamis --}}
                        <p class="mt-1 text-sm text-red-600 hidden" id="stock-error-message"></p>
                    </div>

                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                        <input type="date" name="date" id="date" required value="{{ old('date', date('Y-m-d')) }}"
                            class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md px-3 py-2 border">
                        @error('date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="note" class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                        <textarea name="note" id="note" rows="3"
                            class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md px-3 py-2 border">{{ old('note') }}</textarea>
                        @error('note')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" id="submit-button"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <i class="fas fa-save mr-2"></i> Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const itemSelect = document.getElementById('item_id');
            const typeSelect = document.getElementById('type');
            const itemSizeSelect = document.getElementById('item_size_id');
            const quantityInput = document.getElementById('quantity');
            const stockInfo = document.getElementById('stock-info');
            const sizeSection = document.getElementById('item-size-section');
            const stockErrorMessage = document.getElementById('stock-error-message');
            const submitButton = document.getElementById('submit-button');

            let itemSizes = [];
            let currentUnitSymbol = '';

            // --- PERHATIAN: Perlu implementasi AJAX/Fetch untuk mengambil data ItemSize. ---
            // Karena tidak bisa melakukan AJAX di sini, kita akan mencoba mengambil data
            // dari variabel PHP jika sudah di-eager load oleh controller.
            function fetchItemSizes(itemId) {
                // Dalam aplikasi nyata, ini adalah panggilan API (misalnya, /api/items/{id}/sizes)
                // Jika ItemController::create sudah meng-eager load ItemSize:
                const allItems = {!! json_encode($items) !!};
                const selectedItem = allItems.find(item => item.id == itemId);
                return selectedItem && selectedItem.sizes ? selectedItem.sizes : [];
            }
            // --------------------------------------------------------------------------

            // 1. Isi Dropdown Ukuran
            function populateSizeDropdown() {
                const itemId = itemSelect.value;
                itemSizeSelect.innerHTML = '<option value="">Pilih Ukuran</option>';
                stockInfo.innerText = '';
                itemSizes = [];

                if (itemId) {
                    const selectedItemOption = itemSelect.options[itemSelect.selectedIndex];
                    currentUnitSymbol = selectedItemOption ? selectedItemOption.getAttribute('data-unit-symbol') : '';

                    // Ambil data ukuran
                    itemSizes = fetchItemSizes(itemId);

                    if (itemSizes.length > 0) {
                        sizeSection.style.display = 'block';
                        itemSizeSelect.required = true;

                        itemSizes.forEach(sizeData => {
                            const option = document.createElement('option');
                            option.value = sizeData.id;
                            option.innerText = `${sizeData.size} (Stok: ${sizeData.stock} ${currentUnitSymbol})`;
                            option.setAttribute('data-stock', sizeData.stock);

                            // Pemulihan data lama
                            if ('{{ old('item_size_id') }}' == sizeData.id) {
                                option.selected = true;
                            }
                            itemSizeSelect.appendChild(option);
                        });

                    } else {
                        // Jika tidak ada varian ukuran (produk sederhana)
                        sizeSection.style.display = 'none';
                        itemSizeSelect.required = false;
                        stockInfo.innerText = 'Barang ini tidak memiliki varian ukuran.';
                    }
                } else {
                    sizeSection.style.display = 'none';
                    itemSizeSelect.required = false;
                }

                // Panggil validasi setelah dropdown diisi/diperbarui
                validateStock();
            }

            // 2. Perbarui info stok saat ukuran berubah
            function updateStockInfo(stock) {
                const type = typeSelect.value;
                stockInfo.innerText = `Stok saat ini: ${stock} ${currentUnitSymbol}`;

                // Batasi input kuantitas maks untuk transaksi Keluar
                if (type === 'out') {
                    quantityInput.max = stock;
                } else {
                    quantityInput.removeAttribute('max');
                }
            }

            // 3. Validasi stok secara real-time
            function validateStock() {
                const selectedSizeOption = itemSizeSelect.options[itemSizeSelect.selectedIndex];
                const type = typeSelect.value;
                const quantity = parseInt(quantityInput.value);
                let isValid = true;

                stockErrorMessage.classList.add('hidden');
                submitButton.disabled = false;

                if (selectedSizeOption && selectedSizeOption.value !== '') {
                    const currentStock = parseInt(selectedSizeOption.getAttribute('data-stock'));
                    updateStockInfo(currentStock); // Update info stok per ukuran

                    if (type === 'out') {
                        if (isNaN(quantity) || quantity <= 0) {
                            // Biarkan validasi min="1" ditangani oleh browser/server
                        } else if (quantity > currentStock) {
                            stockErrorMessage.innerText = `Stok tidak mencukupi! Stok ${selectedSizeOption.innerText.split('(')[0].trim()} saat ini: ${currentStock} ${currentUnitSymbol}.`;
                            stockErrorMessage.classList.remove('hidden');
                            submitButton.disabled = true;
                            isValid = false;
                        }
                    }
                } else if (itemSelect.value && itemSizes.length > 0) {
                    // Barang dipilih, tapi ukuran belum dipilih (dan ukuran tersedia)
                    stockErrorMessage.innerText = `Harap pilih Varian Ukuran.`;
                    stockErrorMessage.classList.remove('hidden');
                    submitButton.disabled = true;
                    isValid = false;
                } else {
                    stockInfo.innerText = '';
                }

                return isValid;
            }

            // Event Listeners
            itemSelect.addEventListener('change', populateSizeDropdown);
            typeSelect.addEventListener('change', validateStock);
            itemSizeSelect.addEventListener('change', validateStock);
            quantityInput.addEventListener('input', validateStock); // Gunakan 'input' untuk umpan balik instan

            // Pemeriksaan awal saat halaman dimuat
            populateSizeDropdown();
        });
    </script>
@endsection