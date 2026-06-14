@extends('layouts.backend')

@section('title', 'Riwayat Mutasi Stok')

@section('header')
    <div>
        <nav class="flex mb-1" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1">
                <li><a href="{{ route('dashboard') }}" class="text-xs text-blue-500 hover:text-blue-700 font-medium">Dashboard</a></li>
                <li><span class="text-gray-300 mx-1.5 text-xs">/</span></li>
                <li><span class="text-xs text-gray-400">Mutasi Stok</span></li>
            </ol>
        </nav>
        <h1 class="text-xl font-bold text-gray-800 tracking-tight">Riwayat Mutasi Stok</h1>
        <p class="text-xs text-gray-400 mt-0.5">Pantau riwayat barang/obat masuk dan keluar apotek</p>
    </div>
    <a href="{{ route('transactions.create') }}"
        class="inline-flex items-center gap-1.5 text-xs font-semibold text-white bg-primary-600 hover:bg-primary-700 py-1.5 px-3 rounded-lg shadow-sm transition">
        <i class="fas fa-plus text-[10px]"></i> Tambah Mutasi
    </a>
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Obat
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengguna
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions as $transaction)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $transaction->date->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $transaction->item->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $transaction->type === 'in' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $transaction->type === 'in' ? 'Masuk' : 'Keluar' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaction->quantity }}
                                {{ $transaction->item->unit->symbol }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaction->user->name }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $transaction->note ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 italic font-medium">Tidak ada data mutasi ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($transactions->hasPages())
            <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
@endsection
