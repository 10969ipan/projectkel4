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
        let currentType = 'text'; 

        function checkCategory() {
            const categorySelect = document.getElementById('category_id');
            const selectedOption = categorySelect.options[categorySelect.selectedIndex];
            const categoryName = selectedOption ? selectedOption.getAttribute('data-name') : '';
            
            if (['baju', 'kemeja', 'jaket', 'kaos', 'hoodie', 'jersey', 'celana'].some(el => categoryName.includes(el))) {
                currentType = 'clothing';
            } else if (['sepatu', 'sandal', 'sneakers', 'boots'].some(el => categoryName.includes(el))) {
                currentType = 'shoes';
            } else {
                currentType = 'text';
            }
        }

        function addVariantRow(sizeValue = '', stockValue = 0) {
            const container = document.getElementById('variantContainer');
            const index = variantCount++;
            let sizeInputHtml = '';

            // Generate dropdown/input berdasarkan tipe saat ini (atau force text jika edit data lama yg tidak sesuai)
            // Di sini kita pakai logika sederhana: jika sizeValue cocok dengan S/M/L kita kasih dropdown clothing, dst.
            // Atau kita ikuti kategori yang dipilih.
            
            if (currentType === 'clothing') {
                const sizes = ['S', 'M', 'L', 'XL', 'XXL', '3XL'];
                let options = `<option value="">Pilih</option>`;
                sizes.forEach(s => {
                    const selected = sizeValue == s ? 'selected' : '';
                    options += `<option value="${s}" ${selected}>${s}</option>`;
                });
                sizeInputHtml = `<select name="sizes[]" class="block w-full p-2 border border-gray-300 rounded-md sm:text-sm" required>${options}</select>`;
            } else if (currentType === 'shoes') {
                let options = `<option value="">Pilih</option>`;
                for(let i=36; i<=46; i++) {
                    const selected = sizeValue == i ? 'selected' : '';
                    options += `<option value="${i}" ${selected}>${i}</option>`;
                }
                sizeInputHtml = `<select name="sizes[]" class="block w-full p-2 border border-gray-300 rounded-md sm:text-sm" required>${options}</select>`;
            } else {
                sizeInputHtml = `<input type="text" name="sizes[]" value="${sizeValue}" class="block w-full p-2 border border-gray-300 rounded-md sm:text-sm" required>`;
            }

            const row = document.createElement('tr');
            row.id = `row-${index}`;
            row.innerHTML = `
                <td class="px-4 py-2">${sizeInputHtml}</td>
                <td class="px-4 py-2">
                    <input type="number" name="stocks[]" value="${stockValue}" min="0" oninput="calculateTotal()" class="stock-input block w-full p-2 border border-gray-300 rounded-md sm:text-sm" required>
                </td>
                <td class="px-4 py-2 text-right">
                    <button type="button" onclick="removeRow(${index})" class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button>
                </td>
            `;
            container.appendChild(row);
            calculateTotal();
        }

        function removeRow(index) {
            const row = document.getElementById(`row-${index}`);
            if (row) row.remove();
            calculateTotal();
        }

        function calculateTotal() {
            const inputs = document.querySelectorAll('.stock-input');
            let total = 0;
            inputs.forEach(input => total += parseInt(input.value) || 0);
            document.getElementById('displayTotalStock').innerText = total;
        }

        document.addEventListener('DOMContentLoaded', function() {
            checkCategory(); // Set initial type
            
            // Load existing data
            const existingSizes = @json($item->sizes);
            
            if (existingSizes.length > 0) {
                existingSizes.forEach(item => {
                    addVariantRow(item.size, item.stock);
                });
            } else {
                // Fallback jika belum ada detail (misal data lama)
                addVariantRow('{{ $item->size }}', {{ $item->stock }});
            }
        });
    </script>
@endsection