@extends('layouts.app')

@section('title', 'Detail Permintaan Barang')

@section('header')
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Detail Permintaan Barang</h1>
        <div>
            <a href="{{ route('item-requests.index') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>
@endsection

@section('content')
    <div class="grid grid-cols-1 gap-6">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Informasi Permintaan</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Barang</label>
                        <p class="mt-1 text-sm text-gray-900 font-semibold">{{ $itemRequest->item->name }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Ukuran</label>
                        <p class="mt-1 text-sm text-gray-900">
                            @if($itemRequest->size)
                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded">
                                    {{ $itemRequest->size }}
                                </span>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Jumlah</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $itemRequest->quantity }} {{ $itemRequest->item->unit->symbol }}</p>
                    </div>
                    
                    {{-- Sisa kode sama seperti sebelumnya (Peminta, Tanggal, Status, dll) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Peminta</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $itemRequest->user->name }}</p>
                    </div>
                    {{-- ... --}}
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-500">Alasan Permintaan</label>
                    <div class="mt-1 p-3 bg-gray-50 rounded-md text-sm text-gray-900">
                        {{ $itemRequest->reason }}
                    </div>
                </div>
            </div>
        </div>
        
        {{-- Bagian Approval Buttons sama seperti sebelumnya --}}
        @if (auth()->user()->isAdmin() && $itemRequest->status === 'pending')
             {{-- ... --}}
        @endif
    </div>
@endsection