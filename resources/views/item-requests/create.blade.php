@extends('layouts.app')

@section('title', 'Buat Permintaan Barang Baru')

@section('header')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Buat Permintaan Barang Baru</h1>
        <div>
            <a href="{{ route('item-requests.index') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <form action="{{ route('item-requests.store') }}" method="POST">
                @csrf
                <div class="space-y-6">
                    {{-- Pilih Barang --}}
                    <div>
                        <label for="item_id" class="block text-sm font-medium text-gray-700 mb-1">Barang</label>
                        <select name="item_id" id="item_id" required
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            <option value="">Pilih Barang</option>
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}" 
                                    data-total-stock="{{ $item->stock }}"
                                    data-has-sizes="{{ $item->sizes->count() > 0 }}"
                                    data-sizes='@json($item->sizes)'
                                    {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('item_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Pilih Ukuran (Dinamis) --}}
                    <div id="size-container" class="hidden">
                        <label for="size" class="block text-sm font-medium text-gray-700 mb-1">Ukuran</label>
                        <select name="size" id="size" 
                            class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                            <option value="">Pilih Ukuran</option>
                            {{-- Option diisi via JS --}}
                        </select>
                        @error('size')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jumlah --}}
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Jumlah</label>
                        <input type="number" name="quantity" id="quantity" required value="{{ old('quantity', 1) }}"
                            min="1"
                            class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md px-3 py-2 border">
                        @error('quantity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        
                        {{-- Info Stok --}}
                        <p class="mt-2 text-sm text-gray-500">
                            Stok tersedia: <span id="available-stock" class="font-bold text-gray-800">0</span>
                        </p>
                    </div>

                    <div>
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Alasan Permintaan</label>
                        <textarea name="reason" id="reason" rows="3" required
                            class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md px-3 py-2 border">{{ old('reason') }}</textarea>
                        @error('reason')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" id="submit-btn"
                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <i class="fas fa-paper-plane mr-2"></i> Kirim Permintaan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const itemSelect = document.getElementById('item_id');
            const sizeSelect = document.getElementById('size');
            const sizeContainer = document.getElementById('size-container');
            const quantityInput = document.getElementById('quantity');
            const stockDisplay = document.getElementById('available-stock');
            const submitBtn = document.getElementById('submit-btn');

            let currentMaxStock = 0;

            // Reset saat halaman dimuat
            handleItemChange();

            // Event Listeners
            itemSelect.addEventListener('change', handleItemChange);
            sizeSelect.addEventListener('change', handleSizeChange);
            quantityInput.addEventListener('input', validateQuantity);

            function handleItemChange() {
                const selectedOption = itemSelect.options[itemSelect.selectedIndex];
                
                // Reset Size Dropdown
                sizeSelect.innerHTML = '<option value="">Pilih Ukuran</option>';
                sizeContainer.classList.add('hidden');
                sizeSelect.removeAttribute('required');
                currentMaxStock = 0;
                stockDisplay.innerText = '0';

                if (selectedOption && selectedOption.value) {
                    const hasSizes = selectedOption.getAttribute('data-has-sizes') == '1';
                    
                    if (hasSizes) {
                        // Jika barang punya varian ukuran
                        sizeContainer.classList.remove('hidden');
                        sizeSelect.setAttribute('required', 'required');
                        
                        const sizes = JSON.parse(selectedOption.getAttribute('data-sizes'));
                        sizes.forEach(variant => {
                            const opt = document.createElement('option');
                            opt.value = variant.size;
                            opt.text = `${variant.size} (Stok: ${variant.stock})`;
                            opt.setAttribute('data-stock', variant.stock);
                            sizeSelect.appendChild(opt);
                        });
                        
                        stockDisplay.innerText = '- (Pilih Ukuran)';
                    } else {
                        // Jika barang simple (tanpa varian)
                        currentMaxStock = parseInt(selectedOption.getAttribute('data-total-stock'));
                        stockDisplay.innerText = currentMaxStock;
                    }
                }
                validateQuantity();
            }

            function handleSizeChange() {
                const selectedOption = sizeSelect.options[sizeSelect.selectedIndex];
                if (selectedOption && selectedOption.value) {
                    currentMaxStock = parseInt(selectedOption.getAttribute('data-stock'));
                    stockDisplay.innerText = currentMaxStock;
                } else {
                    currentMaxStock = 0;
                    stockDisplay.innerText = '-';
                }
                validateQuantity();
            }

            function validateQuantity() {
                const qty = parseInt(quantityInput.value) || 0;
                
                if (qty > currentMaxStock || currentMaxStock === 0) {
                    quantityInput.classList.add('border-red-500', 'text-red-600');
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                } else {
                    quantityInput.classList.remove('border-red-500', 'text-red-600');
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
        });
    </script>
@endsection