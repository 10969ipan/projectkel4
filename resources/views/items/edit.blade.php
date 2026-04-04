@extends('layouts.app')

@section('title', 'Edit Data Obat')

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Data Obat</h1>
            <p class="text-sm text-gray-500 mt-1">Perbarui informasi obat, nomor batch, atau stok persediaan.</p>
        </div>
        <div>
            <a href="{{ route('items.index') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-8">
            <form action="{{ route('items.update', $item->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Kode Obat --}}
                    <div>
                        <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">Kode Obat (SKU)</label>
                        <input type="text" name="code" id="code" required value="{{ old('code', $item->code) }}"
                            class="block w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border transition-all">
                        @error('code')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">Kategori</label>
                        <select name="category_id" id="category_id" required
                            class="block w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border bg-white transition-all">
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama Obat --}}
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Produk / Dagang</label>
                        <input type="text" name="name" id="name" required value="{{ old('name', $item->name) }}"
                            class="block w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border transition-all">
                        @error('name')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama Generik --}}
                    <div>
                        <label for="generic_name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Generik (Kandungan)</label>
                        <input type="text" name="generic_name" id="generic_name" value="{{ old('generic_name', $item->generic_name) }}"
                            class="block w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border transition-all">
                        @error('generic_name')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Produsen --}}
                    <div class="md:col-span-2">
                        <label for="manufacturer" class="block text-sm font-semibold text-gray-700 mb-2">Produsen / Manufaktur</label>
                        <input type="text" name="manufacturer" id="manufacturer" value="{{ old('manufacturer', $item->manufacturer) }}"
                            class="block w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border transition-all">
                        @error('manufacturer')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- AREA MANAJEMEN BATCH --}}
                    <div class="md:col-span-2 bg-primary-50/50 p-6 rounded-xl border border-primary-100">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <h3 class="text-lg font-bold text-primary-900 flex items-center">
                                    <i class="fas fa-boxes-stacked mr-2 text-primary-600"></i> Manajemen Batch & Stok
                                </h3>
                                <p class="text-xs text-primary-600 font-medium">Perbarui stok per batch dan pantau masa kadaluwarsa.</p>
                            </div>
                            <div class="bg-white px-4 py-2 rounded-lg border border-primary-200 shadow-sm text-right">
                                <span class="block text-[10px] uppercase font-bold text-gray-400 leading-none mb-1">Total Stok</span>
                                <span class="text-2xl font-black text-primary-600 leading-none" id="displayTotalStock">{{ $item->stock }}</span>
                            </div>
                        </div>

                        <div class="overflow-hidden rounded-lg border border-primary-200 bg-white">
                            <table class="min-w-full divide-y divide-primary-100" id="variantTable">
                                <thead class="bg-primary-50/30">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-primary-700 uppercase tracking-wider">Nomor Batch</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-primary-700 uppercase tracking-wider">Tgl Kadaluwarsa</th>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-primary-700 uppercase tracking-wider">Stok</th>
                                        <th scope="col" class="px-4 py-3 text-center text-xs font-bold text-primary-700 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-primary-50" id="variantContainer">
                                    {{-- Baris akan ditambahkan via JS --}}
                                </tbody>
                            </table>
                        </div>

                        <button type="button" onclick="addBatchRow()"
                            class="mt-4 inline-flex items-center px-4 py-2 border border-primary-300 rounded-lg shadow-sm text-sm font-bold text-primary-700 bg-white hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all">
                            <i class="fas fa-plus-circle mr-2"></i> Tambah Batch Baru
                        </button>
                    </div>

                    {{-- Satuan & Harga --}}
                    <div>
                        <label for="unit_id" class="block text-sm font-semibold text-gray-700 mb-2">Satuan</label>
                        <select name="unit_id" id="unit_id" required
                            class="block w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border bg-white transition-all">
                            <option value="">Pilih Satuan</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_id', $item->unit_id) == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }} ({{ $unit->symbol }})
                                </option>
                            @endforeach
                        </select>
                        @error('unit_id')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-semibold text-gray-700 mb-2">Harga Jual (Rp)</label>
                        <div class="relative rounded-lg shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm font-bold">Rp</span>
                            </div>
                            <input type="number" name="price" id="price" required value="{{ old('price', $item->price) }}"
                                min="0" step="1"
                                class="block w-full pl-10 pr-4 py-3 rounded-lg border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border transition-all">
                        </div>
                        @error('price')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi / Catatan Medik</label>
                        <textarea name="description" id="description" rows="3"
                            class="block w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border transition-all">{{ old('description', $item->description) }}</textarea>
                        @error('description')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2 flex justify-end gap-3 pt-6 border-t border-gray-100">
                        <button type="submit"
                            class="inline-flex items-center px-8 py-3.5 border border-transparent rounded-xl shadow-lg text-sm font-bold text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all transform hover:scale-[1.02]">
                            <i class="fas fa-save mr-2"></i> Perbarui Data Obat
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        let batchCount = 0;

        function addBatchRow(batchValue = '', expiryValue = '', stockValue = 0) {
            const container = document.getElementById('variantContainer');
            const index = batchCount++;
            
            const row = document.createElement('tr');
            row.id = `row-${index}`;
            row.className = 'hover:bg-primary-50/20 transition-colors';
            row.innerHTML = `
                <td class="px-4 py-3">
                    <input type="text" name="batch_numbers[]" value="${batchValue}" placeholder="Batch #" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-xs" required>
                </td>
                <td class="px-4 py-3">
                    <input type="date" name="expiry_dates[]" value="${expiryValue}" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-xs" required>
                </td>
                <td class="px-4 py-3">
                    <input type="number" name="stocks[]" value="${stockValue}" min="0" oninput="calculateTotal()" class="stock-input block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-xs" required>
                </td>
                <td class="px-4 py-3 text-center">
                    <button type="button" onclick="removeRow(${index})" class="text-red-500 hover:text-red-700 transition-colors">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            `;
            container.appendChild(row);
            calculateTotal();
        }

        function removeRow(index) {
            const row = document.getElementById(`row-${index}`);
            if (row) {
                row.remove();
                calculateTotal();
            }
        }

        function calculateTotal() {
            const total = Array.from(document.querySelectorAll('.stock-input'))
                               .reduce((sum, input) => sum + (parseInt(input.value) || 0), 0);
            document.getElementById('displayTotalStock').innerText = total;
        }

        document.addEventListener('DOMContentLoaded', function() {
            @if(old('batch_numbers'))
                @foreach(old('batch_numbers') as $i => $batch)
                    addBatchRow('{{ $batch }}', '{{ old('expiry_dates')[$i] ?? '' }}', '{{ old('stocks')[$i] ?? 0 }}');
                @endforeach
            @else
                const existingBatches = @json($item->sizes->toArray());
                if (existingBatches && existingBatches.length > 0) {
                    existingBatches.forEach(batch => {
                        addBatchRow(batch.batch_number, batch.expiry_date, batch.stock);
                    });
                } else {
                    addBatchRow();
                }
            @endif
        });
    </script>
@endsection
