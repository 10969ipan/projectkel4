@extends('layouts.backend')

@section('title', 'Dashboard')

@section('header')
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-sm text-gray-500 mt-1">Ringkasan inventaris obat dan pemantauan stok secara real-time.</p>
    </div>
    <div class="flex items-center space-x-4 px-4 py-2 bg-primary-50 rounded-lg border border-primary-100 shadow-sm self-start sm:self-center">
        <span class="text-xs font-bold text-primary-600 flex items-center uppercase tracking-wider">
            <i class="far fa-calendar-alt mr-2 text-sm text-primary-500"></i> {{ now()->format('l, d F Y') }}
        </span>
    </div>
@endsection

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 mb-8">
        <!-- Total Pendapatan Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all duration-300 hover:shadow-md group">
            <div class="p-6 flex items-center">
                <div class="flex-shrink-0 bg-indigo-100 rounded-xl p-4 group-hover:bg-indigo-600 transition-colors duration-300">
                    <i class="fas fa-wallet text-indigo-600 text-xl group-hover:text-white transition-colors"></i>
                </div>
                <div class="ml-5">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Pendapatan Bulan Ini</h3>
                    <p class="text-2xl font-black text-gray-900 leading-tight">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="bg-indigo-50/30 px-6 py-3 border-t border-indigo-100">
                <span class="text-[10px] font-bold text-indigo-600 uppercase tracking-tighter italic">Total Transaksi Selesai</span>
            </div>
        </div>
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


    <!-- Grafik Penjualan & Top 5 Obat -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-8">
        <!-- Grafik Penjualan (Kiri, ambil 2 kolom) -->
        <div class="xl:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
             <div class="flex items-center justify-between mb-4">
                 <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider">Grafik Pendapatan Penjualan</h3>
                 <span class="text-[10px] font-medium text-gray-500 bg-gray-100 px-2 py-1 rounded">30 Hari Terakhir</span>
             </div>
             <div class="relative h-64 md:h-72">
                 <canvas id="salesChart" data-dates='@json($dates)' data-sales='@json($salesData)'></canvas>
             </div>
        </div>
        
        <!-- Top 5 Obat Terlaris (Kanan, 1 kolom) -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
             <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider">Top 5 Obat Terlaris</h3>
                <p class="text-[10px] font-medium text-gray-500 uppercase">Periode 30 Hari Terakhir</p>
             </div>
             <div class="p-6 flex-1 overflow-y-auto">
                 @forelse($topItems as $top)
                 <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-50 last:border-0 last:mb-0 last:pb-0">
                     <div class="flex items-center">
                         <div class="h-10 w-10 rounded-full bg-blue-50 flex flex-shrink-0 items-center justify-center text-blue-600 font-bold border border-blue-100">
                             {{ $loop->iteration }}
                         </div>
                         <div class="ml-4">
                             <p class="text-sm font-bold text-gray-900">{{ $top->item->name }}</p>
                             <p class="text-[10px] text-gray-500">{{ $top->item->category->name ?? 'Obat' }}</p>
                         </div>
                     </div>
                     <div class="text-right">
                         <p class="text-sm font-black text-primary-600">{{ $top->total_qty }}</p>
                         <p class="text-[10px] text-gray-500">Terjual</p>
                     </div>
                 </div>
                 @empty
                 <div class="text-center py-8">
                     <i class="fas fa-box-open text-gray-300 text-3xl mb-2"></i>
                     <p class="text-sm text-gray-500">Belum ada data penjualan.</p>
                 </div>
                 @endforelse
             </div>
        </div>
    </div>

    <!-- Antrian Internal & Mutasi Layout Lama (Existing) -->
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

    <!-- 10 LOG TRANSAKSI TERAKHIR (Store Orders) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <div>
                <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider">Riwayat Pesanan Pelanggan Toko</h3>
                <p class="text-[10px] font-medium text-gray-500 uppercase">10 Transaksi Terakhir Diupdate</p>
            </div>
            <a href="{{ route('admin.pharmacare.transaction-logs') }}" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors">
                Lihat Selengkapnya <i class="fas fa-arrow-right ml-1 text-xs"></i>
            </a>
        </div>
        <div class="overflow-x-auto text-xs">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-white">
                    <tr>
                        <th class="px-6 py-4 text-left font-bold text-gray-400 uppercase tracking-widest">No. Order</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-400 uppercase tracking-widest">Pelanggan</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-400 uppercase tracking-widest">Total</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-400 uppercase tracking-widest">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($latestStoreTransactions as $storeOrder)
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900">{{ $storeOrder->order_number }}</td>
                            <td class="px-6 py-4">
                                <span class="font-medium text-gray-700">{{ $storeOrder->user->name ?? 'User Terhapus' }}</span>
                            </td>
                            <td class="px-6 py-4 font-bold text-primary-600">Rp {{ number_format($storeOrder->grand_total, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                @if($storeOrder->order_status === 'completed')
                                    <span class="inline-flex px-2 py-1 rounded-md text-[10px] font-black uppercase bg-emerald-100 text-emerald-700">Selesai</span>
                                @elseif($storeOrder->order_status === 'cancelled')
                                    <span class="inline-flex px-2 py-1 rounded-md text-[10px] font-black uppercase bg-red-100 text-red-700">Batal</span>
                                @else
                                    <span class="inline-flex px-2 py-1 rounded-md text-[10px] font-black uppercase bg-yellow-100 text-yellow-700">{{ $storeOrder->order_status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-500 font-mono">{{ $storeOrder->created_at->format('d M Y, H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400 italic">Belum ada transaksi ritel.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/backend/dashboard_charts.js'])
@endpush
