@extends('layouts.backend')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Log Transaksi</h1>
            <p class="text-sm text-gray-600 mt-1">Riwayat pesanan yang sudah selesai atau dibatalkan.</p>
        </div>
        <a href="{{ route('admin.pharmacare.transactions') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Pesanan Aktif
        </a>
    </div>

    <form action="{{ route('admin.pharmacare.transaction-logs') }}" method="GET" class="flex flex-wrap items-center gap-4 mb-6 text-sm">
        <div class="flex items-center gap-2">
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Durasi:</span>
            <input type="date" name="date_start" value="{{ request('date_start') }}" class="py-1 px-2 border-gray-200 rounded text-xs focus:ring-blue-500 focus:border-blue-500">
            <span class="text-gray-300">-</span>
            <input type="date" name="date_end" value="{{ request('date_end') }}" class="py-1 px-2 border-gray-200 rounded text-xs focus:ring-blue-500 focus:border-blue-500">
        </div>
        
        <div class="flex items-center gap-2">
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status:</span>
            <select name="status" class="py-1 px-2 border-gray-200 rounded text-xs focus:ring-blue-500 focus:border-blue-500">
                <option value="">Semua</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Batal</option>
            </select>
        </div>

        <div class="flex items-center gap-2">
            <button type="submit" class="bg-blue-600 text-white px-3 py-1.5 rounded text-xs font-bold hover:bg-blue-700 transition">
                Filter
            </button>
            @if(request()->anyFilled(['date_start', 'date_end', 'status']))
                <a href="{{ route('admin.pharmacare.transaction-logs') }}" class="text-gray-400 hover:text-gray-600 text-xs font-medium underline px-1">
                    Reset
                </a>
            @endif
        </div>
    </form>


    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No. Order</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pelanggan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total & Metode</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status Akhir</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">{{ $order->order_number }}</div>
                                <div class="text-xs text-gray-500">{{ $order->created_at->format('d M Y H:i') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $order->user->name ?? 'User Terhapus' }}</div>
                                <div class="text-xs text-gray-500 truncate max-w-xs">{{ $order->address->full_address ?? 'Alamat tidak ditemukan' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-blue-600">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</div>
                                <div class="text-xs text-gray-600 uppercase">{{ $order->payment_method }} - {{ $order->shipping_method }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($order->order_status === 'completed')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">Selesai</span>
                                @elseif($order->order_status === 'cancelled')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 border border-red-200">Dibatalkan</span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 border border-gray-200">{{ ucfirst($order->order_status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <button onclick="openItemsModal('{{ $order->id }}')" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition duration-150">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </button>

                                <!-- Items Modal for this Order -->
                                <div id="itemsModal-{{ $order->id }}" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
                                    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white text-left">
                                        <div class="flex justify-between items-center mb-4">
                                            <h3 class="text-lg font-bold text-gray-900">Detail Histori: {{ $order->order_number }}</h3>
                                            <button onclick="closeItemsModal('{{ $order->id }}')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                                <i class="fas fa-times text-xl"></i>
                                            </button>
                                        </div>
                                        <div class="mt-2 text-sm text-gray-500 overflow-x-auto">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead>
                                                    <tr>
                                                        <th class="px-4 py-2 text-left">Item Name</th>
                                                        <th class="px-4 py-2 text-right">Price</th>
                                                        <th class="px-4 py-2 text-center">Qty</th>
                                                        <th class="px-4 py-2 text-right">Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($order->items as $orderItem)
                                                    <tr>
                                                        <td class="px-4 py-2">{{ $orderItem->item->name ?? 'Ikhtisar Item' }}</td>
                                                        <td class="px-4 py-2 text-right">Rp {{ number_format($orderItem->price, 0, ',', '.') }}</td>
                                                        <td class="px-4 py-2 text-center">{{ $orderItem->quantity }}</td>
                                                        <td class="px-4 py-2 text-right font-medium">Rp {{ number_format($orderItem->sub_total, 0, ',', '.') }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            
                                            @if($order->prescription_path)
                                                <div class="mt-4 p-4 bg-gray-50 rounded-lg border">
                                                    <p class="font-semibold mb-2">Resep Dokter:</p>
                                                    <a href="{{ asset('storage/' . $order->prescription_path) }}" target="_blank" class="text-blue-600 hover:underline">
                                                        <i class="fas fa-file-medical text-lg mr-2"></i> Lihat File Resep
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="mt-4 text-right">
                                            <button onclick="closeItemsModal('{{ $order->id }}')" class="px-4 py-2 bg-gray-100 text-gray-800 rounded hover:bg-gray-200">
                                                Tutup
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-file-invoice text-gray-300 text-4xl mb-3"></i>
                                    <p class="text-lg font-medium text-gray-600">Belum ada riwayat transaksi</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function openItemsModal(id) {
        document.getElementById('itemsModal-' + id).classList.remove('hidden');
    }
    function closeItemsModal(id) {
        document.getElementById('itemsModal-' + id).classList.add('hidden');
    }
</script>
@endsection
