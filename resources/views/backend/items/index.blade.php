@extends('layouts.backend')

@section('title', 'Daftar Obat')

@section('header')
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Inventaris Obat</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola stok obat dan pantau persediaan barang secara real-time.</p>
    </div>

    @if(auth()->user()->isAdmin())
        <a href="{{ route('items.create') }}"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-200 self-start sm:self-center">
            <i class="fas fa-pills mr-2"></i> Tambah Obat Baru
        </a>
    @endif
@endsection

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        {{-- Pencarian --}}
        <div class="p-5 border-b border-gray-200 bg-gray-50/50">
            <div class="flex gap-3">
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" id="liveSearchInput" placeholder="Cari nama obat, generik, kode, atau produsen..."
                        class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 sm:text-sm transition-all">
                </div>
                <button id="searchBtn" type="button"
                    class="inline-flex items-center px-5 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                    Saring
                </button>
            </div>
        </div>

        <div class="overflow-x-auto text-sm text-gray-600">
            <table class="min-w-full divide-y divide-gray-200" id="itemTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Informasi Obat</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Produsen</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Deskripsi Medis</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Stok Total</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                        @if(auth()->user()->isAdmin())
                            <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($items as $item)
                        <tr class="hover:bg-primary-50/30 transition-colors item-row">
                            {{-- Nama & Generik --}}
                            <td class="px-6 py-4 item-name">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12 flex items-center justify-center rounded-lg bg-gray-100 border border-gray-200 overflow-hidden">
                                        @if($item->image_path)
                                            <img src="{{ Str::startsWith($item->image_path, ['http://', 'https://']) ? $item->image_path : asset('storage/' . $item->image_path) }}" alt="{{ $item->name }}" class="h-full w-full object-cover" loading="lazy">
                                        @else
                                            <i class="fas fa-prescription-bottle-alt text-gray-400"></i>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $item->name }}</div>
                                        <div class="text-xs text-gray-500 font-medium italic">{{ $item->generic_name ?? 'Tanpa Generik' }}</div>
                                        <div class="text-[10px] text-primary-500 mt-0.5 tracking-wider font-mono">{{ $item->code }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Produsen --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                    {{ $item->manufacturer ?? 'N/A' }}
                                </span>
                            </td>

                            {{-- Deskripsi Medis --}}
                            <td class="px-6 py-4">
                                @if($item->description)
                                    <div class="text-xs text-gray-600 max-w-[200px] truncate" title="{{ $item->description }}">
                                        <i class="fas fa-info-circle text-primary-500 mr-1"></i>
                                        {{ $item->description }}
                                    </div>
                                @else
                                    <span class="text-[10px] bg-amber-50 text-amber-600 border border-amber-200 px-2 py-0.5 rounded-full font-bold uppercase tracking-wider">
                                        Belum ada deskripsi
                                    </span>
                                @endif
                            </td>


                            {{-- Total Stok --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold {{ $item->stock <= 10 ? 'text-red-600' : 'text-gray-900' }}">
                                        {{ $item->stock }} <span class="text-[10px] font-normal uppercase">{{ $item->unit->symbol }}</span>
                                    </span>
                                    @if($item->stock <= 10)
                                        <span class="text-[10px] text-red-500 font-bold uppercase tracking-tighter">Stok Kritis</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Kategori --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs font-medium text-gray-600">{{ $item->category->name }}</div>
                            </td>

                            @if(auth()->user()->isAdmin())
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex justify-center space-x-2">
                                        <a href="{{ route('items.edit', $item->id) }}"
                                            class="p-2 text-primary-600 hover:bg-primary-100 rounded-lg transition-colors border border-primary-200" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('items.destroy', $item->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                class="btn-delete p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors border border-red-200" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isAdmin() ? 6 : 5 }}" class="px-6 py-10 text-center text-gray-500 italic">
                                <i class="fas fa-box-open text-3xl mb-3 block text-gray-200"></i>
                                Data obat tidak tersedia
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($items, 'links'))
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $items->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const searchInput = document.getElementById('liveSearchInput');
                const rows = document.querySelectorAll('.item-row');
                const searchBtn = document.getElementById('searchBtn');

                function performSearch() {
                    const filter = searchInput.value.toLowerCase();
                    rows.forEach(row => {
                        const content = row.innerText.toLowerCase();
                        row.style.display = content.includes(filter) ? '' : 'none';
                    });
                }

                if (searchBtn) searchBtn.addEventListener('click', performSearch);
                searchInput.addEventListener('keypress', (e) => e.key === 'Enter' && performSearch());

                const deleteButtons = document.querySelectorAll('.btn-delete');
                deleteButtons.forEach(button => {
                    button.addEventListener('click', function (e) {
                        e.preventDefault();
                        const form = this.closest('form');
                        Swal.fire({
                            title: 'Hapus Obat?',
                            text: "Semua riwayat transaksi terkait obat ini akan ikut terhapus secara permanen.",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#e11d48',
                            cancelButtonColor: '#475569',
                            confirmButtonText: 'Ya, Hapus Data!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) form.submit();
                        });
                    });
                });
            });
        </script>
    @endpush
@endsection
