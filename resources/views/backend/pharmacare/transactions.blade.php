@extends('layouts.backend')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Pesanan Toko (Transaksi)</h1>
            <p class="text-sm text-gray-600 mt-1">Daftar pesanan dari pelanggan yang belum selesai atau perlu diproses.</p>
        </div>
        <a href="{{ route('admin.pharmacare.transaction-logs') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
            <i class="fas fa-history mr-1"></i> Lihat Log Transaksi
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

    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No. Order</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pelanggan</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total & Metode</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status Pembayaran</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status Pesanan</th>
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
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($order->payment_status === 'paid')
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Lunas</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Menunggu</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <form action="{{ route('admin.pharmacare.transactions.update', $order->id) }}" method="POST" class="flex flex-col gap-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="order_status" class="text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-1">
                                        <option value="ordered" {{ $order->order_status === 'ordered' ? 'selected' : '' }}>Dipesan</option>
                                        <option value="paid" {{ $order->order_status === 'paid' ? 'selected' : '' }}>Dibayar</option>
                                        <option value="processing" {{ $order->order_status === 'processing' ? 'selected' : '' }}>Diproses</option>
                                        <option value="completed" {{ $order->order_status === 'completed' ? 'selected' : '' }}>Selesai</option>
                                        <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                                    </select>
                                    <button type="submit" class="text-xs bg-blue-600 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded w-full">Update</button>
                                </form>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <button onclick="openItemsModal('{{ $order->id }}')" class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md transition duration-150">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </button>

                                <!-- Items Modal for this Order -->
                                <div id="itemsModal-{{ $order->id }}" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
                                    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white text-left">
                                        <div class="flex justify-between items-center mb-4">
                                            <h3 class="text-lg font-bold text-gray-900">Detail Pesanan: {{ $order->order_number }}</h3>
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
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-box-open text-gray-300 text-4xl mb-3"></i>
                                    <p class="text-lg font-medium text-gray-600">Belum ada pesanan yang perlu diproses</p>
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
    @vite(['resources/js/backend/pharmacare_transactions.js'])
@endpush
@endsection
