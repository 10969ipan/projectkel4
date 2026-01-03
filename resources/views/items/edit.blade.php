@extends('layouts.app')

@section('title', 'Edit Barang')

@section('header')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Edit Barang</h1>
        <div>
            <a href="{{ route('items.index') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="card bg-white rounded-lg overflow-hidden">
        <div class="p-6">
            <form action="{{ route('items.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700">Kode Barang</label>
                        <input type="text" name="code" id="code" required value="{{ old('code', $item->code) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm border p-2">
                        @error('code')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori</label>
                        <select name="category_id" id="category_id" required onchange="checkCategory()"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm border p-2">
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" data-name="{{ Str::lower($category->name) }}"
                                    {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500" id="categoryHelper">Pilih kategori untuk penyesuaian ukuran otomatis.</p>
                        @error('category_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama Barang</label>
                        <input type="text" name="name" id="name" required value="{{ old('name', $item->name) }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm border p-2">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- AREA VARIAN UKURAN DAN STOK (DINAMIS) --}}
                    <div class="md:col-span-2 bg-gray-50 p-5 rounded-md border border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Varian Ukuran & Stok</h3>
                                <p class="text-sm text-gray-500">Edit detail stok untuk setiap ukuran.</p>
                            </div>
                            <div class="text-right">
                                <span class="block text-xs text-gray-500">Total Stok</span>
                                <span class="text-2xl font-bold text-primary-600" id="displayTotalStock">{{ $item->stock }}</span>
                            </div>
                        </div>

                        <table class="min-w-full divide-y divide-gray-200" id="variantTable">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 w-1/2">Ukuran</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 w-1/3">Stok</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="variantContainer">
                                {{-- Loaded via JS --}}
                            </tbody>
                        </table>

                        <button type="button" onclick="addVariantRow()"
                            class="mt-3 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-plus mr-2"></i> Tambah Ukuran
                        </button>
                    </div>

                    <div>
                        <label for="unit_id" class="block text-sm font-medium text-gray-700">Satuan</label>
                        <select name="unit_id" id="unit_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm border p-2">
                            <option value="">Pilih Satuan</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}"
                                    {{ old('unit_id', $item->unit_id) == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }} ({{ $unit->symbol }})</option>
                            @endforeach
                        </select>
                        @error('unit_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700">Harga</label>
                        <input type="number" name="price" id="price" required
                            value="{{ old('price', $item->price) }}" min="0" step="0.01"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm border p-2">
                        @error('price')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                        <textarea name="description" id="description" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm border p-2">{{ old('description', $item->description) }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2 flex justify-end">
                        <button type="submit"
                            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <i class="fas fa-save mr-2"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        let variantCount = 0;
        let currentType = 'text'; // 'text', 'tops', 'bottoms', 'shoes'
        let storedStocks = {}; // Persistent storage for stock values

        /**
         * Determines the category type, updates the helper text, and regenerates the variant rows.
         */
        function checkCategoryAndGenerate() {
            const categorySelect = document.getElementById('category_id');
            const selectedOption = categorySelect.options[categorySelect.selectedIndex];
            const categoryName = selectedOption ? selectedOption.getAttribute('data-name') : '';
            const helper = document.getElementById('categoryHelper');
            
            // 1. Update storedStocks with current values before clearing
            document.querySelectorAll('#variantContainer tr').forEach(row => {
                const sizeInput = row.querySelector('[name="sizes[]"]');
                const stockInput = row.querySelector('[name="stocks[]"]');
                if (sizeInput && stockInput && sizeInput.value) {
                    storedStocks[sizeInput.value] = stockInput.value;
                }
            });

            // 2. Determine Type and Message
            let newType = 'text';
            let message = 'Input ukuran manual. Tambahkan baris sesuai kebutuhan.';
            if (categoryName) {
                if (['baju', 'kemeja', 'jaket', 'kaos', 'hoodie', 'jersey', 'rompi', 'blazer'].some(el => categoryName.includes(el))) {
                    newType = 'tops';
                    message = 'Mode Atasan: Ukuran standar (XS-XXL) akan ditambahkan secara otomatis jika Anda mengganti kategori.';
                } else if (['celana', 'rok', 'jeans', 'chino', 'shorts', 'trousers'].some(el => categoryName.includes(el))) {
                    newType = 'bottoms';
                    message = 'Mode Bawahan: Ukuran standar (27-38) akan ditambahkan secara otomatis jika Anda mengganti kategori.';
                } else if (['sepatu', 'sandal', 'sneakers', 'boots', 'flat'].some(el => categoryName.includes(el))) {
                    newType = 'shoes';
                    message = 'Mode Sepatu: Ukuran standar (36-46) akan ditambahkan secara otomatis jika Anda mengganti kategori.';
                }
            }

            // Only update type if it's different to avoid re-rendering on page load
            if (newType !== currentType) {
                currentType = newType;
                
                // Clear existing rows and reset counter
                const container = document.getElementById('variantContainer');
                container.innerHTML = '';
                variantCount = 0;

                // Generate new rows, restoring from storedStocks
                const presets = {
                    tops: ['XS', 'S', 'M', 'L', 'XL', 'XXL'],
                    bottoms: Array.from({ length: 12 }, (_, i) => String(27 + i)), // 27 to 38
                    shoes: Array.from({ length: 11 }, (_, i) => String(36 + i))   // 36 to 46
                };
                const sizesToGenerate = presets[currentType];
                if (sizesToGenerate) {
                    sizesToGenerate.forEach(size => {
                        const stock = storedStocks[size] || 0;
                        addVariantRow(size, stock);
                    });
                } else {
                    addVariantRow('', storedStocks[''] || 0);
                }
            }
             helper.innerText = message;
        }

        /**
         * Adds a new variant row to the table. The input type depends on the global `currentType`.
         */
        function addVariantRow(sizeValue = '', stockValue = 0) {
            const container = document.getElementById('variantContainer');
            const index = variantCount++;
            let sizeInputHtml = '';

            if (currentType === 'tops') {
                const sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL'];
                let options = sizes.map(s => `<option value="${s}" ${sizeValue === s ? 'selected' : ''}>${s}</option>`).join('');
                sizeInputHtml = `<select name="sizes[]" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm" required><option value="">Pilih Ukuran</option>${options}</select>`;
            } else if (currentType === 'bottoms') {
                let options = '';
                for(let i=27; i<=40; i++) {
                    options += `<option value="${i}" ${String(sizeValue) === String(i) ? 'selected' : ''}>${i}</option>`;
                }
                sizeInputHtml = `<select name="sizes[]" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm" required><option value="">Pilih Ukuran</option>${options}</select>`;
            } else if (currentType === 'shoes') {
                let options = '';
                for(let i=36; i<=46; i++) {
                    options += `<option value="${i}" ${String(sizeValue) === String(i) ? 'selected' : ''}>${i}</option>`;
                }
                sizeInputHtml = `<select name="sizes[]" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm" required><option value="">Pilih Ukuran</option>${options}</select>`;
            } else {
                sizeInputHtml = `<input type="text" name="sizes[]" value="${sizeValue}" placeholder="Contoh: All Size, 500gr" class="focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md px-3 py-2 border" required>`;
            }

            const row = document.createElement('tr');
            row.id = `row-${index}`;
            row.innerHTML = `
                <td class="px-4 py-2">${sizeInputHtml}</td>
                <td class="px-4 py-2">
                    <input type="number" name="stocks[]" value="${stockValue}" min="0" oninput="calculateTotal()" class="stock-input focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md px-3 py-2 border" required>
                </td>
                <td class="px-4 py-2 text-right">
                    <button type="button" onclick="removeRow(${index})" class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button>
                </td>
            `;
            container.appendChild(row);
            calculateTotal();
        }

        /**
         * Removes a specific variant row.
         */
        function removeRow(index) {
            document.getElementById(`row-${index}`)?.remove();
            calculateTotal();
        }

        /**
         * Recalculates and displays the total stock from all variant rows.
         */
        function calculateTotal() {
            const total = Array.from(document.querySelectorAll('.stock-input'))
                               .reduce((sum, input) => sum + (parseInt(input.value) || 0), 0);
            document.getElementById('displayTotalStock').innerText = total;
        }

        /**
         * Initializes the form on page load.
         */
        document.addEventListener('DOMContentLoaded', function() {
            // Set initial category type
            const categorySelect = document.getElementById('category_id');
            const selectedOption = categorySelect.options[categorySelect.selectedIndex];
            const categoryName = selectedOption ? selectedOption.getAttribute('data-name') : '';
             if (categoryName) {
                 if (['baju', 'kemeja', 'jaket', 'kaos'].some(el => categoryName.includes(el))) { currentType = 'tops'; }
                else if (['celana', 'rok', 'jeans'].some(el => categoryName.includes(el))) { currentType = 'bottoms'; }
                else if (['sepatu', 'sandal'].some(el => categoryName.includes(el))) { currentType = 'shoes'; }
            }
            
            // Load existing item variants from the database
            const existingSizes = @json($item->sizes->toArray());
            if (existingSizes && existingSizes.length > 0) {
                existingSizes.forEach(variant => {
                    addVariantRow(variant.size, variant.stock);
                });
            } else {
                 addVariantRow(); // Add a default empty row if no variants exist
            }

            // Attach the event listener for subsequent changes.
            categorySelect.addEventListener('change', checkCategoryAndGenerate);
        });
    </script>
@endsection
