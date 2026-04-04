@extends('layouts.app')

@section('title', 'Dashboard SIMA-APOTEK')

@section('header')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard Apotek</h1>
            <p class="text-sm text-gray-500 mt-1">Ringkasan inventaris obat dan pemantauan masa kadaluwarsa.</p>
        </div>
        <div class="flex items-center space-x-4 mt-2 md:mt-0 px-4 py-2 bg-primary-50 rounded-lg border border-primary-100 shadow-sm">
            <span class="text-xs font-bold text-primary-600 flex items-center uppercase tracking-wider">
                <i class="far fa-calendar-alt mr-2 text-sm text-primary-500"></i> {{ now()->format('l, d F Y') }}
            </span>
        </div>
    </div>
@endsection

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Obat Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-md group">
            <div class="p-6 flex items-center">
                <div class="flex-shrink-0 bg-primary-100 rounded-xl p-4 group-hover:bg-primary-600 transition-colors duration-300">
                    <i class="fas fa-pills text-primary-600 text-xl group-hover:text-white transition-colors"></i>
                </div>
                <div class="ml-5">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Obat</h3>
                    <p class="text-2xl font-black text-gray-900 leading-tight">{{ $totalItems }}</p>
                </div>
            </div>
            <div class="bg-gray-50/50 px-6 py-3 border-t border-gray-100">
                <a href="{{ route('items.index') }}" class="text-xs font-bold text-primary-600 hover:text-primary-700 flex items-center uppercase tracking-tighter">
                    Kelola Inventaris <i class="fas fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>
        </div>

        <!-- Segera Kadaluwarsa Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-md group">
            <div class="p-6 flex items-center">
                <div class="flex-shrink-0 bg-orange-100 rounded-xl p-4 group-hover:bg-orange-600 transition-colors duration-300">
                    <i class="fas fa-hourglass-half text-orange-600 text-xl group-hover:text-white transition-colors"></i>
                </div>
                <div class="ml-5">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Segera Kadaluwarsa</h3>
                    <p class="text-2xl font-black text-orange-600 leading-tight">{{ $expiringSoonCount }}</p>
                </div>
            </div>
            <div class="bg-orange-50/30 px-6 py-3 border-t border-orange-100">
                <span class="text-[10px] font-bold text-orange-600 uppercase tracking-tighter italic">Batas 30 Hari Kedepan</span>
            </div>
        </div>

        <!-- Sudah Kadaluwarsa Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-md group">
            <div class="p-6 flex items-center">
                <div class="flex-shrink-0 bg-red-100 rounded-xl p-4 group-hover:bg-red-600 transition-colors duration-300">
                    <i class="fas fa-calendar-times text-red-600 text-xl group-hover:text-white transition-colors"></i>
                </div>
                <div class="ml-5">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Sudah Kadaluwarsa</h3>
                    <p class="text-2xl font-black text-red-600 leading-tight">{{ $expiredCount }}</p>
                </div>
            </div>
            <div class="bg-red-50/30 px-6 py-3 border-t border-red-100 font-bold">
                <span class="text-[10px] text-red-600 uppercase tracking-tighter italic">Segera Lakukan Retur</span>
            </div>
        </div>

        <!-- Permintaan Pending Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-md group">
            <div class="p-6 flex items-center">
                <div class="flex-shrink-0 bg-emerald-100 rounded-xl p-4 group-hover:bg-emerald-600 transition-colors duration-300">
                    <i class="fas fa-file-medical text-emerald-600 text-xl group-hover:text-white transition-colors"></i>
                </div>
                <div class="ml-5">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Antrian Permintaan</h3>
                    <p class="text-2xl font-black text-emerald-600 leading-tight">{{ $pendingRequests }}</p>
                </div>
            </div>
            <div class="bg-emerald-50/30 px-6 py-3 border-t border-emerald-100">
                <a href="{{ route('item-requests.index') }}?status=pending" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center uppercase tracking-tighter">
                    Verifikasi Permintaan <i class="fas fa-arrow-right ml-2 transition-transform group-hover:translate-x-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @if (auth()->user()->isAdmin())
            <a href="{{ route('items.create') }}" class="bg-primary-600 p-5 rounded-2xl shadow-sm flex flex-col items-center justify-center text-center hover:bg-primary-700 transition-all duration-200 transform hover:scale-[1.03]">
                <i class="fas fa-plus-circle text-white text-3xl mb-3"></i>
                <p class="text-sm font-bold text-white uppercase tracking-tighter">Obat Baru</p>
            </a>
            <a href="{{ route('transactions.create') }}" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 flex flex-col items-center justify-center text-center hover:bg-gray-50 transition-all duration-200 group">
                <i class="fas fa-exchange-alt text-primary-600 text-2xl mb-3 group-hover:rotate-180 transition-transform duration-500"></i>
                <p class="text-sm font-bold text-gray-700 uppercase tracking-tighter">Input Transaksi</p>
            </a>
        @endif
        <a href="{{ route('item-requests.index') }}" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 flex flex-col items-center justify-center text-center hover:bg-gray-50 transition-all duration-200 group">
            <i class="fas fa-notes-medical text-primary-600 text-2xl mb-3 group-hover:scale-110 transition-transform"></i>
            <p class="text-sm font-bold text-gray-700 uppercase tracking-tighter">Permintaan Obat</p>
        </a>
        @if (auth()->user()->isAdmin())
            <a href="{{ route('reports.stock') }}" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 flex flex-col items-center justify-center text-center hover:bg-gray-50 transition-all duration-200 group">
                <i class="fas fa-file-prescription text-primary-600 text-2xl mb-3 group-hover:rotate-6 transition-transform"></i>
                <p class="text-sm font-bold text-gray-700 uppercase tracking-tighter">Laporan Stok</p>
            </a>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        {{-- Transaksi Terakhir --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <div>
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider">Log Mutasi Terakhir</h3>
                    <p class="text-[10px] font-medium text-gray-500 uppercase">Riwayat pergerakan stok real-time</p>
                </div>
                <a href="{{ route('transactions.index') }}" class="p-2 bg-primary-50 text-primary-600 rounded-lg hover:bg-primary-100 transition-colors">
                    <i class="fas fa-external-link-alt text-xs"></i>
                </a>
            </div>
            <div class="overflow-x-auto text-xs">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-white">
                        <tr>
                            <th class="px-6 py-4 text-left font-bold text-gray-400 uppercase tracking-widest">Produk</th>
                            <th class="px-6 py-4 text-left font-bold text-gray-400 uppercase tracking-widest">Jenis</th>
                            <th class="px-6 py-4 text-left font-bold text-gray-400 uppercase tracking-widest">Qty</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse(\App\Models\Transaction::with(['item', 'user'])->latest()->take(5)->get() as $transaction)
                            <tr class="hover:bg-primary-50/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $transaction->item->name }}</div>
                                    <div class="text-[10px] text-gray-400 font-mono">{{ $transaction->date->format('d/m/Y') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2 py-1 rounded-md text-[10px] font-black uppercase {{ $transaction->type === 'in' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $transaction->type === 'in' ? 'Masuk' : 'Keluar' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-700">
                                    {{ $transaction->quantity }} {{ $transaction->item->unit->symbol }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-400 italic font-medium">Belum ada mutasi barang hari ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Permintaan Terakhir --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <div>
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider">Antrian Permintaan</h3>
                    <p class="text-[10px] font-medium text-gray-500 uppercase">Status permintaan dari Departemen/Unit</p>
                </div>
                <a href="{{ route('item-requests.index') }}" class="p-2 bg-emerald-50 text-emerald-600 rounded-lg hover:bg-emerald-100 transition-colors">
                    <i class="fas fa-external-link-alt text-xs"></i>
                </a>
            </div>
            <div class="overflow-x-auto text-xs">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-white">
                        <tr>
                            <th class="px-6 py-4 text-left font-bold text-gray-400 uppercase tracking-widest">Produk</th>
                            <th class="px-6 py-4 text-left font-bold text-gray-400 uppercase tracking-widest">Status</th>
                            <th class="px-6 py-4 text-left font-bold text-gray-400 uppercase tracking-widest">Peminta</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse(\App\Models\ItemRequest::with(['item', 'user'])->latest()->take(5)->get() as $request)
                            <tr class="hover:bg-emerald-50/30 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $request->item->name }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $request->quantity }} {{ $request->item->unit->symbol }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($request->status === 'pending')
                                        <span class="px-2 py-1 rounded-md text-[10px] font-black uppercase bg-yellow-100 text-yellow-700 border border-yellow-200">Pending</span>
                                    @elseif($request->status === 'approved')
                                        <span class="px-2 py-1 rounded-md text-[10px] font-black uppercase bg-emerald-100 text-emerald-700 border border-emerald-200">Disetujui</span>
                                    @else
                                        <span class="px-2 py-1 rounded-md text-[10px] font-black uppercase bg-red-100 text-red-700 border border-red-200">Ditolak</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-700">{{ $request->user->name }}</div>
                                    <div class="text-[10px] text-gray-400 font-mono">{{ $request->created_at->diffForHumans() }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-400 italic font-medium">Belum ada riwayat permintaan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
