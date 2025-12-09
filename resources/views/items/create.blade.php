@extends('layouts.app')

@section('title', 'Tambah Barang Baru')

@section('header')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Tambah Barang Baru</h1>
        <div>
            <a href="{{ route('items.index') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <form action="{{ route('items.store') }}" method="POST" id="itemForm">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Kode Barang --}}
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700 mb-1">Kode Barang (SKU)</label>
                        <input type="text" name="code" id="code" required value="{{ old('code') }}"
                            placeholder="Misal: KMJ-001"
                            class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md px-3 py-2 border">
                        @error('code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                        <select name="category_id" id="category_id" required onchange="checkCategory()"
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" data-name="{{ Str::lower($category->name) }}"
                                    {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama Barang --}}
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Barang</label>
                        <input type="text" name="name" id="name" required value="{{ old('name') }}"
                            placeholder="Contoh: Kemeja Flannel Kotak"
                            class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md px-3 py-2 border">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- AREA VARIAN UKURAN DAN STOK (DINAMIS) --}}
                    <div class="md:col-span-2 bg-gray-50 p-5 rounded-md border border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Varian Ukuran & Stok</h3>
                                <p class="text-sm text-gray-500">Tambahkan detail stok untuk setiap ukuran.</p>
                            </div>
                            <div class="text-right">
                                <span class="block text-xs text-gray-500">Total Stok</span>
                                <span class="text-2xl font-bold text-primary-600" id="displayTotalStock">0</span>
                            </div>
                        </div>

                        <table class="min-w-full divide-y divide-gray-200" id="variantTable">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/2">Ukuran</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/3">Stok</th>
                                    <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="variantContainer">
                                {{-- Baris akan ditambahkan via JS --}}
                            </tbody>
                        </table>

                        <button type="button" onclick="addVariantRow()"
                            class="mt-3 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <i class="fas fa-plus mr-2"></i> Tambah Ukuran
                        </button>
                    </div>

                    {{-- Satuan & Harga --}}
                    <div>
                        <label for="unit_id" class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                        <select name="unit_id" id="unit_id" required
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            <option value="">Pilih Satuan</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }} ({{ $unit->symbol }})
                                </option>
                            @endforeach
                        </select>
                        @error('unit_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                        <input type="number" name="price" id="price" required value="{{ old('price', 0) }}"
                            min="0" step="0.01"
                            class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md px-3 py-2 border">
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="description" id="description" rows="3"
                            class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md px-3 py-2 border">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2 flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <i class="fas fa-save mr-2"></i> Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPT PENGELOLA VARIAN --}}
    <script>
        let variantCount = 0;
        let currentType = 'text'; // 'text', 'clothing', 'shoes'

        function checkCategory() {
            const categorySelect = document.getElementById('category_id');
            const selectedOption = categorySelect.options[categorySelect.selectedIndex];
            const categoryName = selectedOption ? selectedOption.getAttribute('data-name') : '';
            
            let newType = 'text';
            if (['baju', 'kemeja', 'jaket', 'kaos', 'hoodie', 'jersey', 'celana'].some(el => categoryName.includes(el))) {
                newType = 'clothing';
            } else if (['sepatu', 'sandal', 'sneakers', 'boots'].some(el => categoryName.includes(el))) {
                newType = 'shoes';
            }

            // Jika tipe berubah, kita perlu mereset inputan atau membiarkannya tapi user harus mengganti manual
            // Untuk kenyamanan, kita simpan tipe global untuk row baru
            currentType = newType;
            
            // Opsional: Update row yang sudah ada (tapi hati-hati menghapus data user)
            // Disini kita hanya set untuk row baru agar data lama tidak hilang tiba-tiba
        }

        function addVariantRow(sizeValue = '', stockValue = 0) {
            const container = document.getElementById('variantContainer');
            const index = variantCount++;
            
            let sizeInputHtml = '';

            if (currentType === 'clothing') {
                const sizes = ['S', 'M', 'L', 'XL', 'XXL', '3XL'];
                let options = `<option value="">Pilih Ukuran</option>`;
                sizes.forEach(s => {
                    const selected = sizeValue === s ? 'selected' : '';
                    options += `<option value="${s}" ${selected}>${s}</option>`;
                });
                sizeInputHtml = `<select name="sizes[]" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm" required>${options}</select>`;
            } else if (currentType === 'shoes') {
                let options = `<option value="">Pilih Ukuran</option>`;
                for(let i=36; i<=46; i++) {
                    const selected = String(sizeValue) === String(i) ? 'selected' : '';
                    options += `<option value="${i}" ${selected}>${i}</option>`;
                }
                sizeInputHtml = `<select name="sizes[]" class="block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm" required>${options}</select>`;
            } else {
                sizeInputHtml = `<input type="text" name="sizes[]" value="${sizeValue}" placeholder="Contoh: All Size" class="focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md px-3 py-2 border" required>`;
            }

            const row = document.createElement('tr');
            row.id = `row-${index}`;
            row.innerHTML = `
                <td class="px-4 py-2">
                    ${sizeInputHtml}
                </td>
                <td class="px-4 py-2">
                    <input type="number" name="stocks[]" value="${stockValue}" min="0" oninput="calculateTotal()" class="stock-input focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md px-3 py-2 border" required>
                </td>
                <td class="px-4 py-2 text-right">
                    <button type="button" onclick="removeRow(${index})" class="text-red-600 hover:text-red-900">
                        <i class="fas fa-trash"></i>
                    </button>
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
            inputs.forEach(input => {
                total += parseInt(input.value) || 0;
            });
            document.getElementById('displayTotalStock').innerText = total;
        }

        document.addEventListener('DOMContentLoaded', function() {
            checkCategory();
            // Tambahkan satu row kosong default jika tidak ada old data
            @if(old('sizes'))
                @foreach(old('sizes') as $i => $size)
                    addVariantRow('{{ $size }}', '{{ old('stocks')[$i] }}');
                @endforeach
            @else
                addVariantRow(); 
            @endif
        });
    </script>
@endsection