@extends('layouts.backend')

@section('title', 'Tambah Obat Baru')

@section('header')
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Obat Baru</h1>
            <p class="text-sm text-gray-500 mt-1">Tambahkan data obat baru beserta detail batch dan masa kadaluwarsa.</p>
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
            <form action="{{ route('items.store') }}" method="POST" id="itemForm">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Kode Obat --}}
                    <div>
                        <label for="code" class="block text-sm font-semibold text-gray-700 mb-2">Kode Obat (SKU)</label>
                        <input type="text" name="code" id="code" required value="{{ old('code') }}"
                            placeholder="Misal: OBT-PARA-500"
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
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                        <input type="text" name="name" id="name" required value="{{ old('name') }}"
                            placeholder="Contoh: Paracetamol 500mg"
                            class="block w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border transition-all">
                        @error('name')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama Generik --}}
                    <div>
                        <label for="generic_name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Generik (Kandungan)</label>
                        <input type="text" name="generic_name" id="generic_name" value="{{ old('generic_name') }}"
                            placeholder="Contoh: Acetaminophen"
                            class="block w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border transition-all">
                        @error('generic_name')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Produsen --}}
                    <div class="md:col-span-2">
                        <label for="manufacturer" class="block text-sm font-semibold text-gray-700 mb-2">Produsen / Manufaktur</label>
                        <input type="text" name="manufacturer" id="manufacturer" value="{{ old('manufacturer') }}"
                            placeholder="Contoh: Kimia Farma, Kalbe Farma, dll"
                            class="block w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border transition-all">
                        @error('manufacturer')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Stok Obat --}}
                    <div>
                        <label for="stock" class="block text-sm font-semibold text-gray-700 mb-2">Total Stok Persediaan</label>
                        <input type="number" name="stock" id="stock" required value="{{ old('stock', 0) }}" min="0"
                            placeholder="Contoh: 100"
                            class="block w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border transition-all">
                        @error('stock')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Satuan Terkecil --}}
                    <div>
                        <label for="unit_id" class="block text-sm font-semibold text-gray-700 mb-2">Satuan Terkecil</label>
                        <select name="unit_id" id="unit_id" required
                            class="block w-full px-4 py-3 rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm border bg-white transition-all">
                            <option value="">Pilih Satuan</option>
                            @foreach ($units as $unit)
                                <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }} ({{ $unit->symbol }})
                                </option>
                            @endforeach
                        </select>
                        @error('unit_id')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
@endsection
