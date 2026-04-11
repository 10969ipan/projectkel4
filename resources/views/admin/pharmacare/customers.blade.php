@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <nav class="flex mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li class="inline-flex items-center">
                        <a href="{{ route('admin.pharmacare.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Pharmacare</a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <span class="text-gray-400 mx-2">/</span>
                            <span class="text-gray-500 text-sm font-medium">Manajemen Pelanggan</span>
                        </div>
                    </li>
                </ol>
            </nav>
            <h1 class="text-3xl font-bold text-gray-900 border-l-4 border-teal-500 pl-4 py-1">
                <i class="fas fa-users text-teal-500 mr-2"></i> Manajemen Pelanggan Pharmacare
            </h1>
        </div>
        <div class="flex gap-3">
             <a href="{{ route('admin.pharmacare.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2 px-4 rounded-lg transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 shadow-sm rounded-r-lg" role="alert">
            <p class="font-bold">Berhasil!</p>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama & Email</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Password</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Alamat Utama</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Riwayat Transaksi</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($customers as $customer)
                    @php
                        $primaryAddress = $customer->addresses->where('is_primary', true)->first();
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 bg-teal-100 rounded-full flex items-center justify-center text-teal-700 font-bold">
                                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-gray-900">{{ $customer->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $customer->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-gray-400 font-mono">********</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 max-w-xs truncate">
                                {{ $primaryAddress ? $primaryAddress->full_address : 'Belum diatur' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button onclick="openHistoryModal({{ $customer->id }}, '{{ $customer->name }}', {{ json_encode($customer->storeOrders) }})" class="inline-flex items-center px-3 py-1 bg-blue-50 text-blue-700 text-xs font-bold rounded-lg border border-blue-100 hover:bg-blue-100 transition-colors">
                                <i class="fas fa-shopping-bag mr-1"></i> {{ $customer->storeOrders->count() }} Order
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            <button type="button" 
                                onclick="openEditModal({{ $customer->id }}, '{{ $customer->name }}', '{{ $customer->email }}', '{{ $primaryAddress ? $primaryAddress->full_address : '' }}')" 
                                class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-lg shadow-sm transition-all">
                                <i class="fas fa-edit mr-2"></i> Edit Data
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 whitespace-nowrap text-center text-gray-400 italic">
                            Belum ada pelanggan terdaftar di sistem e-commerce.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL EDIT CUSTOMER -->
<div id="editModal" class="fixed inset-0 z-50 overflow-y-auto hidden" style="background: rgba(0,0,0,0.5);">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden transform transition-all">
            <div class="bg-teal-600 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="text-xl font-bold">Edit Data Pelanggan</h3>
                <button onclick="closeEditModal()" class="text-white hover:text-gray-200 text-2xl">&times;</button>
            </div>
            <form id="editForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" id="edit_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" id="edit_email" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Password Baru (Kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" id="edit_password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent" placeholder="Ketik password baru...">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Alamat Utama</label>
                    <textarea name="address" id="edit_address" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent" placeholder="Masukkan alamat lengkap..."></textarea>
                </div>
                <div class="pt-4 flex gap-3">
                    <button type="button" onclick="closeEditModal()" class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg transition-colors">Batal</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-lg shadow-lg transition-colors">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL RIWAYAT TRANSAKSI -->
<div id="historyModal" class="fixed inset-0 z-50 overflow-y-auto hidden" style="background: rgba(0,0,0,0.5);">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden transform transition-all">
            <div class="bg-blue-600 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="text-xl font-bold">Riwayat Transaksi: <span id="history_customer_name"></span></h3>
                <button onclick="closeHistoryModal()" class="text-white hover:text-gray-200 text-2xl">&times;</button>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">No Pesanan</th>
                                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Tanggal</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase">Total</th>
                                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody id="history_table_body" class="divide-y divide-gray-100">
                            <!-- Data populated via JS -->
                        </tbody>
                    </table>
                </div>
                <div class="mt-6 flex justify-end">
                    <button onclick="closeHistoryModal()" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-lg transition-colors">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- SweetAlert2 -->
    <script src="{{ asset('sweetalert/sweetalert2.all.min.js') }}"></script>
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        @if (session('success'))
            Toast.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            });
        @endif

        @if (session('error'))
            Toast.fire({
                icon: 'error',
                title: '{{ session('error') }}'
            });
        @endif

        // MODAL EDIT LOGIC
        function openEditModal(id, name, email, address) {
            document.getElementById('editForm').action = `/admin/pharmacare/customers/${id}`;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_address').value = address;
            document.getElementById('edit_password').value = '';
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // MODAL HISTORY LOGIC
        function openHistoryModal(id, name, orders) {
            document.getElementById('history_customer_name').innerText = name;
            const body = document.getElementById('history_table_body');
            body.innerHTML = '';

            if (orders.length === 0) {
                body.innerHTML = '<tr><td colspan="4" class="px-4 py-8 text-center text-gray-400 italic">Belum ada transaksi</td></tr>';
            } else {
                orders.forEach(order => {
                    const date = new Date(order.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
                    const statusClass = order.order_status === 'paid' ? 'bg-green-100 text-green-800' : (order.order_status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800');
                    
                    body.innerHTML += `
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-sm font-bold text-gray-900">${order.order_number}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">${date}</td>
                            <td class="px-4 py-3 text-sm font-bold text-right text-gray-900">Rp ${new Intl.NumberFormat('id-ID').format(order.grand_total)}</td>
                            <td class="px-4 py-3 text-center text-xs">
                                <span class="px-2 py-1 rounded-full font-bold uppercase ${statusClass}">${order.order_status}</span>
                            </td>
                        </tr>
                    `;
                });
            }
            document.getElementById('historyModal').classList.remove('hidden');
        }

        function closeHistoryModal() {
            document.getElementById('historyModal').classList.add('hidden');
        }

        // Close on escape key
        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeEditModal();
                closeHistoryModal();
            }
        });
    </script>
@endsection
