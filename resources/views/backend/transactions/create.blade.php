@extends('layouts.backend')

@section('title', 'Tambah Transaksi Baru')

@section('header')
    <div>
        <nav class="flex mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1">
                <li><a href="{{ route('transactions.index') }}" class="text-xs text-blue-500 hover:text-blue-700 font-medium">Mutasi Stok</a></li>
                <li><span class="text-gray-300 mx-1.5 text-xs">/</span></li>
                <li><span class="text-xs text-gray-400">Tambah Transaksi</span></li>
            </ol>
        </nav>
        <h1 class="text-xl font-bold text-gray-800 tracking-tight">Tambah Transaksi Baru</h1>
        <p class="text-xs text-gray-400 mt-0.5">Catat mutasi masuk atau keluar stok obat secara manual</p>
    </div>
    <a href="{{ route('transactions.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 py-1.5 px-3 rounded-lg transition">
        <i class="fas fa-arrow-left text-[10px]"></i> Kembali
    </a>
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <form action="{{ route('transactions.store') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label for="item_id" class="block text-sm font-medium text-gray-700 mb-1">Barang</label>
                        <select name="item_id" id="item_id" required
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            <option value="">Pilih Barang</option>
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}" {{ (old('item_id', request('item_id')) == $item->id) ? 'selected' : '' }}
                                    data-unit-symbol="{{ $item->unit->symbol }}"
                                    data-stock="{{ $item->stock }}">
                                    {{ $item->name }} (Stok: {{ $item->stock }} {{ $item->unit->symbol }})
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
                            <option value="out" {{ old('type') == 'out' ? 'selected' : '' }}>Keluar (Kurangi stok)</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                        <input type="number" name="quantity" id="quantity" required value="{{ old('quantity', 1) }}" min="1"
                            class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md px-3 py-2 border">
                        @error('quantity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
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
            const quantityInput = document.getElementById('quantity');
            const stockErrorMessage = document.getElementById('stock-error-message');
            const submitButton = document.getElementById('submit-button');

            function validateStock() {
                const selectedOption = itemSelect.options[itemSelect.selectedIndex];
                const type = typeSelect.value;
                const quantity = parseInt(quantityInput.value);
                
                stockErrorMessage.classList.add('hidden');
                submitButton.disabled = false;

                if (selectedOption && selectedOption.value !== '' && type === 'out') {
                    const currentStock = parseInt(selectedOption.getAttribute('data-stock'));
                    const unitSymbol = selectedOption.getAttribute('data-unit-symbol');

                    if (quantity > currentStock) {
                        stockErrorMessage.innerText = `Stok tidak mencukupi! Stok saat ini: ${currentStock} ${unitSymbol}.`;
                        stockErrorMessage.classList.remove('hidden');
                        submitButton.disabled = true;
                    }
                }
            }

            itemSelect.addEventListener('change', validateStock);
            typeSelect.addEventListener('change', validateStock);
            quantityInput.addEventListener('input', validateStock);
        });
    </script>
@endsection
