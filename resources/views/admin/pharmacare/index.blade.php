@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900 border-l-4 border-blue-500 pl-4 py-1">
            <i class="fas fa-store text-blue-500 mr-2"></i> Pharmacare E-Commerce
        </h1>
        <a href="{{ route('store.index') }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow transition duration-200">
            <i class="fas fa-external-link-alt mr-2"></i> Buka Tampilan Toko
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Store Orders Panel -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-blue-800 text-white px-6 py-4">
                <h2 class="text-xl font-bold"><i class="fas fa-shopping-bag mr-2"></i> Pesanan Toko Online</h2>
            </div>
            <div class="p-6">
                @if(isset($orders) && count($orders) > 0)
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pelanggan</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($orders as $order)
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">#{{ $order->id }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm text-gray-900">{{ $order->user->name ?? 'Unknown' }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm font-semibold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        {{ $order->status }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-gray-500 italic text-center py-4">Belum ada pesanan dari aplikasi pelanggan.</p>
                @endif
            </div>
        </div>

        <!-- Customer App / Limit Paylater Management -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-teal-600 text-white px-6 py-4 flex justify-between items-center">
                <h2 class="text-xl font-bold"><i class="fas fa-users mr-2"></i> Pelanggan E-Commerce</h2>
                <a href="{{ route('admin.pharmacare.customers') }}" class="text-xs bg-white text-teal-700 px-3 py-1 rounded-full font-bold hover:bg-teal-50 transition">
                    Lihat Semua
                </a>
            </div>
            <div class="p-6">
                 @if(isset($customers) && count($customers) > 0)
                    <div class="space-y-4">
                        @foreach($customers as $customer)
                        <div class="border rounded-lg p-4 flex justify-between items-center">
                            <div>
                                <h3 class="font-bold text-gray-900">{{ $customer->name }}</h3>
                                <p class="text-sm text-gray-500">
                                    Resep Divalidasi: 
                                    @if($customer->is_prescription_approved)
                                        <span class="text-green-600 font-bold">✓ Ya</span>
                                    @else
                                        <span class="text-red-600 font-bold">✗ Belum</span>
                                        <form action="{{ route('admin.pharmacare.approve', $customer->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded ml-2 hover:bg-blue-200">Setujui Resep</button>
                                        </form>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <form action="{{ route('admin.pharmacare.paylater', $customer->id) }}" method="POST" class="flex gap-2">
                                    @csrf
                                    <input type="number" name="limit" value="{{ $customer->paylater_limit }}" class="w-32 border-gray-300 rounded-md shadow-sm text-sm" placeholder="Limit">
                                    <button type="submit" class="bg-teal-500 text-white px-3 py-1 rounded text-sm hover:bg-teal-600">Simpan Limit</button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 italic text-center py-4">
                        Database belum memiliki user dengan `store_role` = 'customer'.
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
