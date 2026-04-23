@extends('layouts.backend')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Pesanan Toko (Transaksi)</h1>
            <p class="text-sm text-gray-600 mt-1">Daftar pesanan dari pelanggan yang belum selesai atau perlu diproses.</p>
        </div>
        <a href="{{ route('admin.pharmacare.transaction-logs') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
            Lihat Log Transaksi
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif
    
    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <span class="block sm:inline">{{ session('error') }}</span>
    </div>
    @endif

    <div class="bg-white shadow-sm rounded-2xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Informasi Pesanan</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total & Pembayaran</th>
                        <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status Pengiriman</th>
                        <th class="px-6 py-4 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($orders as $order)
                        <tr class="hover:bg-blue-50/30 transition-colors duration-200">
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-900 mb-0.5">{{ $order->order_number }}</span>
                                    <span class="text-xs text-gray-500 mb-2">{{ $order->created_at->format('d M Y, H:i') }}</span>
                                    <div class="flex items-center gap-2 mt-1">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-xs font-bold">
                                            {{ strtoupper(substr($order->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-xs font-bold text-gray-700">{{ $order->user->name ?? 'User Terhapus' }}</span>
                                            <span class="text-[10px] text-gray-400 truncate max-w-[180px]">{{ $order->address->full_address ?? 'Alamat tidak ditemukan' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="text-sm font-extrabold text-blue-600 mb-1">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-gray-100 text-gray-600 uppercase">{{ $order->payment_method }}</span>
                                        @if($order->payment_status === 'paid')
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-emerald-100 text-emerald-700 uppercase">Lunas</span>
                                        @else
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-amber-100 text-amber-700 uppercase">Pending</span>
                                        @endif
                                    </div>
                                    <span class="text-[10px] text-gray-400 font-medium">Metode: {{ $order->shipping_method }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <form action="{{ route('admin.pharmacare.transactions.update', $order->id) }}" method="POST" class="max-w-[200px]">
                                    @csrf
                                    @method('PUT')
                                    <div class="flex flex-col gap-2">
                                        <select name="order_status" class="text-xs border-gray-200 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-1.5 px-2 bg-gray-50/50 font-semibold text-gray-700">
                                            <option value="ordered" {{ $order->order_status === 'ordered' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                                            <option value="paid" {{ $order->order_status === 'paid' ? 'selected' : '' }}>Sudah Dibayar</option>
                                            <option value="processing" {{ $order->order_status === 'processing' ? 'selected' : '' }}>Sedang Diproses</option>
                                            <option value="completed" {{ $order->order_status === 'completed' ? 'selected' : '' }}>Selesai / Terkirim</option>
                                            <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                        </select>
                                        
                                        <div class="relative">
                                            <input type="text" name="tracking_number" value="{{ $order->tracking_number }}" 
                                                   placeholder="Nomor Resi..." 
                                                   class="w-full text-[10px] border-gray-200 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 py-1.5 px-2 bg-gray-50/50 font-medium text-gray-600">
                                            @if($order->tracking_number)
                                                <div class="absolute right-2 top-1.5 text-emerald-500">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                                </div>
                                            @endif
                                        </div>

                                        <button type="submit" class="text-[10px] bg-gray-800 hover:bg-black text-white font-bold py-1.5 px-3 rounded-lg transition-all duration-200 shadow-sm active:scale-95">
                                            Update Status
                                        </button>
                                    </div>
                                </form>
                            </td>
                            <td class="px-6 py-5 whitespace-nowrap text-center">
                                <div class="flex flex-col gap-2 items-center">
                                    <button onclick="openItemsModal('{{ $order->id }}')" class="w-24 text-[10px] font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 py-2 rounded-lg transition-colors">
                                        LIHAT DETAIL
                                    </button>
                                    <a href="{{ route('admin.pharmacare.invoice', $order->id) }}" target="_blank" class="w-24 text-[10px] font-bold text-emerald-600 bg-emerald-50 hover:bg-emerald-100 py-2 rounded-lg transition-colors inline-block text-center">
                                        CETAK INVOICE
                                    </a>
                                </div>

                                <!-- Items Modal for this Order -->
                                <div id="itemsModal-{{ $order->id }}" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm overflow-y-auto h-full w-full hidden z-50">
                                    <div class="relative top-20 mx-auto p-0 border-none w-11/12 md:w-3/4 lg:w-1/2 shadow-2xl rounded-2xl bg-white text-left overflow-hidden">
                                        <div class="bg-gray-900 px-6 py-4 flex justify-between items-center">
                                            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Detail Pesanan: {{ $order->order_number }}</h3>
                                            <button onclick="closeItemsModal('{{ $order->id }}')" class="text-gray-400 hover:text-white transition-colors">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                            </button>
                                        </div>
                                        <div class="p-6">
                                            <div class="mb-6 grid grid-cols-2 gap-4">
                                                <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                                                    <span class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Informasi Pengiriman</span>
                                                    <p class="text-xs font-bold text-gray-700">{{ $order->user->name ?? '-' }}</p>
                                                    <p class="text-[11px] text-gray-500 mt-1 leading-relaxed">{{ $order->address->full_address ?? '-' }}</p>
                                                </div>
                                                <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                                                    <span class="text-[10px] font-bold text-gray-400 uppercase block mb-1">Metode & Kurir</span>
                                                    <p class="text-xs font-bold text-gray-700 uppercase">{{ $order->payment_method }}</p>
                                                    <p class="text-[11px] text-gray-500 mt-1 leading-relaxed">{{ $order->shipping_method }}</p>
                                                </div>
                                            </div>

                                            <div class="mt-2 text-sm text-gray-500">
                                                <table class="min-w-full divide-y divide-gray-100">
                                                    <thead class="bg-gray-50/50">
                                                        <tr>
                                                            <th class="px-4 py-2 text-left text-[10px] font-bold text-gray-400 uppercase">Produk</th>
                                                            <th class="px-4 py-2 text-right text-[10px] font-bold text-gray-400 uppercase">Harga</th>
                                                            <th class="px-4 py-2 text-center text-[10px] font-bold text-gray-400 uppercase">Qty</th>
                                                            <th class="px-4 py-2 text-right text-[10px] font-bold text-gray-400 uppercase">Subtotal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-gray-50">
                                                        @foreach($order->items as $orderItem)
                                                        <tr>
                                                            <td class="px-4 py-3 text-xs font-bold text-gray-700">{{ $orderItem->item->name ?? 'Ikhtisar Item' }}</td>
                                                            <td class="px-4 py-3 text-right text-xs text-gray-500">Rp {{ number_format($orderItem->price, 0, ',', '.') }}</td>
                                                            <td class="px-4 py-3 text-center text-xs text-gray-700 font-bold">{{ $orderItem->quantity }}</td>
                                                            <td class="px-4 py-3 text-right text-xs font-bold text-blue-600">Rp {{ number_format($orderItem->sub_total, 0, ',', '.') }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot class="bg-blue-50/30">
                                                        <tr>
                                                            <td colspan="3" class="px-4 py-2 text-right text-[10px] font-bold text-gray-500 uppercase">Ongkos Kirim</td>
                                                            <td class="px-4 py-2 text-right text-xs font-bold text-gray-700">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                                                        </tr>
                                                        <tr class="border-t-2 border-blue-100">
                                                            <td colspan="3" class="px-4 py-3 text-right text-xs font-bold text-gray-700 uppercase">Total Bayar</td>
                                                            <td class="px-4 py-3 text-right text-sm font-extrabold text-blue-600">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                                
                                                @if($order->prescription_path)
                                                    <div class="mt-6 p-4 bg-red-50 rounded-xl border border-red-100 flex items-center justify-between">
                                                        <div class="flex items-center gap-3">
                                                            <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center text-red-600">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                                            </div>
                                                            <div>
                                                                <p class="text-xs font-bold text-red-800">Resep Dokter Terlampir</p>
                                                                <p class="text-[10px] text-red-600">Pastikan untuk memverifikasi file ini.</p>
                                                            </div>
                                                        </div>
                                                        <a href="{{ asset('storage/' . $order->prescription_path) }}" target="_blank" class="px-4 py-2 bg-white text-red-600 text-xs font-bold rounded-lg border border-red-200 hover:bg-red-50 transition-colors shadow-sm">
                                                            BUKA FILE
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 px-6 py-4 text-right">
                                            <button onclick="closeItemsModal('{{ $order->id }}')" class="px-6 py-2 bg-white text-gray-600 text-xs font-bold rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors">
                                                TUTUP
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 text-gray-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                                    </div>
                                    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Belum ada pesanan aktif</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function openItemsModal(orderId) {
            document.getElementById('itemsModal-' + orderId).classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeItemsModal(orderId) {
            document.getElementById('itemsModal-' + orderId).classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }

        // Close on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === "Escape") {
                const modals = document.querySelectorAll('[id^="itemsModal-"]');
                modals.forEach(modal => {
                    if (!modal.classList.contains('hidden')) {
                        const id = modal.id.split('-')[1];
                        closeItemsModal(id);
                    }
                });
            }
        });
    </script>
@endpush
@endsection
