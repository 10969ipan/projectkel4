@extends('layouts.app')

@section('title', 'Manajemen Barang')

@section('header')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Manajemen Barang</h1>

        @if(auth()->user()->isAdmin())
            <a href="{{ route('items.create') }}"
                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 mt-4 md:mt-0">
                <i class="fas fa-plus mr-2"></i> Tambah Barang
            </a>
        @endif
    </div>
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        {{-- Bagian Pencarian Manual --}}
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <div class="flex gap-2">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" id="liveSearchInput" placeholder="cari barang, kode, atau kategori..."
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                </div>
                <button id="searchBtn" type="button"
                    class="inline-flex items-center px-4 py-2 border border-primary-600 rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-search mr-2"></i> Cari
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="itemTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama
                            Barang</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detail
                            Varian (Size : Stok)</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total
                            Stok</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori
                        </th>

                        @if(auth()->user()->isAdmin())
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50 item-row">
                            {{-- Nama & Kode --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 item-name">
                                {{ $item->name }}
                                <div class="text-xs text-gray-500">{{ $item->code }}</div>
                            </td>

                            {{-- Detail Stok per Ukuran --}}
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @if($item->sizes->isNotEmpty())
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($item->sizes as $variant)
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border 
                                                                                                                                                {{ $variant->stock > 0 ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                                                {{ $variant->size }} : {{ $variant->stock }}
                                            </span>
                                        @endforeach
                                    </div>
                                @elseif($item->size)
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        {{ $item->size }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>

                            {{-- Total Stok --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($item->stock == 0)
                                        bg-red-100 text-red-800
                                    @elseif($item->stock <= 10)
                                        bg-red-100 text-red-800
                                    @elseif($item->stock <= 20)
                                        bg-yellow-100 text-yellow-800
                                    @else
                                        bg-green-100 text-green-800
                                    @endif">
                                    {{ $item->stock }} {{ $item->unit->symbol }}
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->category->name }}
                            </td>

                            @if(auth()->user()->isAdmin())
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('items.edit', $item->id) }}"
                                            class="inline-flex items-center px-3 py-1 border border-blue-500 rounded-md text-sm font-medium text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <i class="fas fa-edit mr-1"></i> Edit
                                        </a>
                                        <form action="{{ route('items.destroy', $item->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                class="btn-delete inline-flex items-center px-3 py-1 border border-red-500 rounded-md text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                <i class="fas fa-trash mr-1"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr id="noDataRow">
                            <td colspan="{{ auth()->user()->isAdmin() ? 5 : 4 }}"
                                class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada barang ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($items, 'links'))
            <div class="px-6 py-4 border-t border-gray-200 pagination-container">
                {{ $items->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // --- FITUR MANUAL SEARCH (KLIK TOMBOL UNTUK MENCARI) ---
                const searchInput = document.getElementById('liveSearchInput');
                const rows = document.querySelectorAll('.item-row');
                const pagination = document.querySelector('.pagination-container');
                const searchBtn = document.getElementById('searchBtn');

                // Fungsi untuk melakukan pencarian
                function performSearch() {
                    const filter = searchInput.value.toLowerCase();

                    // Sembunyikan pagination saat mencari agar tidak bingung
                    if (filter.length > 0) {
                        if (pagination) pagination.style.display = 'none';
                    } else {
                        if (pagination) pagination.style.display = 'block';
                    }

                    rows.forEach(row => {
                        const text = row.querySelector('.item-name').textContent.toLowerCase();
                        if (text.includes(filter)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                }

                // Event listener untuk tombol search
                if (searchBtn) {
                    searchBtn.addEventListener('click', function () {
                        performSearch();
                    });
                }

                // Event listener untuk Enter key pada input
                searchInput.addEventListener('keypress', function (e) {
                    if (e.key === 'Enter') {
                        performSearch();
                    }
                });

                // --- FITUR DELETE CONFIRMATION ---
                const deleteButtons = document.querySelectorAll('.btn-delete');
                deleteButtons.forEach(button => {
                    button.addEventListener('click', function (e) {
                        e.preventDefault();
                        const form = this.closest('form');
                        Swal.fire({
                            title: 'Apakah Anda yakin?',
                            text: "Data barang ini akan dihapus permanen!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Ya, hapus!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });
            });
        </script>
    @endpush
@endsection