@extends('layouts.backend')

@push('styles')
<style>
    /* Modal Animation */
    .modal-overlay {
        position: fixed; inset: 0; z-index: 50;
        background: rgba(0,0,0,0.45);
        backdrop-filter: blur(4px);
        display: flex; align-items: center; justify-content: center; padding: 1rem;
        opacity: 0; visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    .modal-overlay.is-open {
        opacity: 1; visibility: visible;
    }
    .modal-box {
        background: white; border-radius: 1rem; box-shadow: 0 25px 50px rgba(0,0,0,0.2);
        width: 100%; max-height: 90vh; overflow-y: auto;
        transform: scale(0.95) translateY(12px);
        transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.3s ease;
        opacity: 0;
    }
    .modal-overlay.is-open .modal-box {
        transform: scale(1) translateY(0); opacity: 1;
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-5">
        <div>
            <nav class="flex mb-1" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1">
                    <li><a href="{{ route('admin.pharmacare.index') }}" class="text-xs text-blue-500 hover:text-blue-700 font-medium">Pharmacare</a></li>
                    <li><span class="text-gray-300 mx-1.5 text-xs">/</span></li>
                    <li><span class="text-xs text-gray-400">Manajemen Pelanggan</span></li>
                </ol>
            </nav>
            <h1 class="text-xl font-bold text-gray-800 tracking-tight">Manajemen Pelanggan</h1>
            <p class="text-xs text-gray-400 mt-0.5">Kelola data pelanggan Pharmacare</p>
        </div>
        <a href="{{ route('admin.pharmacare.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 py-1.5 px-3 rounded-lg transition">
            <i class="fas fa-arrow-left text-[10px]"></i> Kembali
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-5 shadow-sm rounded-r-lg" role="alert">
            <p class="font-bold text-sm">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Table --}}
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <table class="w-full divide-y divide-gray-200 table-fixed">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-wider w-[18%]">Pelanggan</th>
                    <th class="px-3 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-wider w-[14%]">Saldo / Paylater</th>
                    <th class="px-3 py-3 text-center text-[10px] font-bold text-gray-500 uppercase tracking-wider w-[10%]">Resep</th>
                    <th class="px-3 py-3 text-center text-[10px] font-bold text-gray-500 uppercase tracking-wider w-[10%]">Langganan</th>
                    <th class="px-3 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-wider w-[24%]">Alamat Utama</th>
                    <th class="px-3 py-3 text-center text-[10px] font-bold text-gray-500 uppercase tracking-wider w-[24%]">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($customers as $customer)
                @php $primaryAddress = $customer->addresses->where('is_primary', true)->first(); @endphp
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-3 py-3">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-8 w-8 bg-teal-100 rounded-full flex items-center justify-center text-teal-700 font-bold text-xs">
                                {{ strtoupper(substr($customer->name, 0, 1)) }}
                            </div>
                            <div class="ml-2 min-w-0">
                                <div class="text-xs font-bold text-gray-900 truncate">{{ $customer->name }}</div>
                                <div class="text-[10px] text-gray-500 truncate">{{ $customer->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-3 py-3">
                        <div class="text-xs font-bold text-gray-900">Rp {{ number_format($customer->wallet_balance, 0, ',', '.') }}</div>
                        <div class="text-[10px] text-gray-400">{{ $customer->walletTransactions->count() }} Trx</div>
                        <div class="text-[10px] font-semibold text-teal-600 mt-0.5">PL: Rp {{ number_format($customer->paylater_limit, 0, ',', '.') }}</div>
                    </td>
                    <td class="px-3 py-3 text-center">
                        @if($customer->is_prescription_approved)
                            <span class="inline-flex items-center px-1.5 py-0.5 bg-blue-100 text-blue-700 text-[9px] font-black rounded-full uppercase">
                                <i class="fas fa-check-circle mr-0.5"></i> Verif
                            </span>
                        @else
                            <span class="inline-flex items-center px-1.5 py-0.5 bg-red-50 text-red-500 text-[9px] font-black rounded-full uppercase">
                                <i class="fas fa-times-circle mr-0.5"></i> Belum
                            </span>
                        @endif
                    </td>
                    <td class="px-3 py-3 text-center">
                        @if($customer->subscriptions->where('status', 'active')->count() > 0)
                            <span class="inline-flex items-center px-1.5 py-0.5 bg-green-100 text-green-700 text-[10px] font-bold rounded-full">
                                <i class="fas fa-sync-alt fa-spin mr-0.5 text-[8px]"></i> {{ $customer->subscriptions->where('status', 'active')->count() }} Aktif
                            </span>
                        @else
                            <span class="text-[10px] text-gray-400 italic">—</span>
                        @endif
                    </td>
                    <td class="px-3 py-3">
                        <div class="text-[11px] text-gray-700 truncate">
                            {{ $primaryAddress ? $primaryAddress->full_address : 'Belum diatur' }}
                        </div>
                    </td>
                    <td class="px-3 py-3 text-center">
                        <div class="inline-flex items-center gap-1.5">
                            <button
                                onclick="openHistoryModal('{{ addslashes($customer->name) }}', {{ json_encode($customer->storeOrders) }})"
                                class="inline-flex items-center px-2 py-1 bg-blue-50 text-blue-700 text-[10px] font-bold rounded-md border border-blue-100 hover:bg-blue-100 transition-colors">
                                <i class="fas fa-shopping-bag mr-1"></i> {{ $customer->storeOrders->count() }}
                            </button>
                            <button type="button"
                                onclick="openEditModal({{ $customer->id }}, '{{ addslashes($customer->name) }}', '{{ addslashes($customer->email) }}', '{{ $primaryAddress ? addslashes($primaryAddress->full_address) : '' }}', {{ $customer->wallet_balance }}, {{ $customer->paylater_limit }}, {{ $customer->is_prescription_approved ? 1 : 0 }})"
                                class="inline-flex items-center px-2 py-1 bg-teal-600 hover:bg-teal-700 text-white text-[10px] font-bold rounded-md shadow-sm transition-all">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-gray-400 italic text-sm">
                        Belum ada pelanggan terdaftar di sistem e-commerce.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-4 py-3 bg-gray-50 border-t border-gray-100">
            {{ $customers->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- ===================== MODAL EDIT ===================== -->
<div id="editModalOverlay" class="modal-overlay" onclick="handleOverlayClick(event, 'editModalOverlay')">
    <div class="modal-box" style="max-width: 28rem;" onclick="event.stopPropagation()">
        <div class="bg-teal-600 px-6 py-4 flex justify-between items-center text-white">
            <h3 class="text-base font-bold">Edit Data Pelanggan</h3>
            <button onclick="closeModal('editModalOverlay')" class="text-white/80 hover:text-white text-xl leading-none">&times;</button>
        </div>
        <form id="editForm" method="POST" class="p-5 space-y-4 max-h-[70vh] overflow-y-auto">
            @csrf @method('PUT')
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Nama Lengkap</label>
                <input type="text" name="name" id="edit_name" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Email</label>
                <input type="email" name="email" id="edit_email" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Saldo Dompet (Rp)</label>
                    <input type="number" name="wallet_balance" id="edit_wallet_balance" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none">
                </div>
                <div>
                    <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Limit Paylater (Rp)</label>
                    <input type="number" name="paylater_limit" id="edit_paylater_limit" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 outline-none">
                </div>
            </div>
            <div class="flex items-center bg-blue-50 p-3 rounded-lg">
                <input type="checkbox" name="is_prescription_approved" id="edit_is_prescription_approved" value="1" class="h-4 w-4 text-teal-600 border-gray-300 rounded">
                <label for="edit_is_prescription_approved" class="ml-2 text-sm font-bold text-blue-700">Verifikasi Resep Dokter</label>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Password Baru <span class="text-gray-400 font-normal">(kosongkan jika tidak diubah)</span></label>
                <input type="password" name="password" id="edit_password" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none" placeholder="••••••••">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-600 mb-1">Alamat Utama</label>
                <textarea name="address" id="edit_address" rows="2" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent outline-none" placeholder="Alamat lengkap..."></textarea>
            </div>
            <div class="pt-2 flex gap-2 border-t border-gray-100">
                <button type="button" onclick="closeModal('editModalOverlay')" class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-lg transition-colors">Batal</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold rounded-lg shadow-sm transition-colors">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- ===================== MODAL RIWAYAT TRANSAKSI ===================== -->
<div id="historyModalOverlay" class="modal-overlay" onclick="handleOverlayClick(event, 'historyModalOverlay')">
    <div class="modal-box" style="max-width: 42rem;" onclick="event.stopPropagation()">
        <div class="bg-blue-600 px-5 py-4 flex justify-between items-center text-white">
            <div>
                <h3 class="text-base font-bold">Riwayat Transaksi</h3>
                <p id="history_customer_name" class="text-xs text-blue-200 mt-0.5"></p>
            </div>
            <button onclick="closeModal('historyModalOverlay')" class="text-white/80 hover:text-white text-xl leading-none">&times;</button>
        </div>
        <div class="p-5">
            {{-- Summary --}}
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-blue-50 rounded-lg px-3 py-2 flex-1 text-center">
                    <span class="text-[10px] font-bold text-blue-500 uppercase tracking-wider block">Total Order</span>
                    <span id="history_total_orders" class="text-xl font-black text-blue-700">0</span>
                </div>
                <div class="bg-green-50 rounded-lg px-3 py-2 flex-1 text-center">
                    <span class="text-[10px] font-bold text-green-500 uppercase tracking-wider block">Total Belanja</span>
                    <span id="history_total_spent" class="text-xl font-black text-green-700">Rp 0</span>
                </div>
            </div>

            {{-- Table --}}
            <div class="rounded-lg border border-gray-200 overflow-hidden">
                <table class="w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2.5 text-left text-[10px] font-bold text-gray-500 uppercase">No Pesanan</th>
                            <th class="px-4 py-2.5 text-left text-[10px] font-bold text-gray-500 uppercase">Tanggal</th>
                            <th class="px-4 py-2.5 text-right text-[10px] font-bold text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-2.5 text-center text-[10px] font-bold text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody id="history_table_body" class="divide-y divide-gray-100 bg-white"></tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div id="history_pagination" class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100" style="display:none!important">
                <span id="history_page_info" class="text-xs text-gray-500 font-medium"></span>
                <div class="flex items-center gap-1">
                    <button id="history_prev_btn" onclick="historyPrevPage()"
                        class="inline-flex items-center px-2.5 py-1.5 bg-white border border-gray-200 text-xs font-bold text-gray-600 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-chevron-left text-[9px]"></i>
                    </button>
                    <div id="history_page_numbers" class="flex items-center gap-1"></div>
                    <button id="history_next_btn" onclick="historyNextPage()"
                        class="inline-flex items-center px-2.5 py-1.5 bg-white border border-gray-200 text-xs font-bold text-gray-600 rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-chevron-right text-[9px]"></i>
                    </button>
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <button onclick="closeModal('historyModalOverlay')" class="px-4 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-lg transition-colors">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // ============ MODAL HELPERS ============
    function openModal(id) {
        const el = document.getElementById(id);
        el.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(id) {
        const el = document.getElementById(id);
        el.classList.remove('is-open');
        document.body.style.overflow = '';
    }
    function handleOverlayClick(e, id) {
        if (e.target === document.getElementById(id)) closeModal(id);
    }
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            closeModal('editModalOverlay');
            closeModal('historyModalOverlay');
        }
    });

    // ============ MODAL EDIT ============
    function openEditModal(id, name, email, address, wallet, paylater, isVerified) {
        document.getElementById('editForm').action = `/admin/pharmacare/customers/${id}`;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_address').value = address;
        document.getElementById('edit_wallet_balance').value = wallet;
        document.getElementById('edit_paylater_limit').value = paylater;
        document.getElementById('edit_is_prescription_approved').checked = isVerified == 1;
        document.getElementById('edit_password').value = '';
        openModal('editModalOverlay');
    }

    // ============ MODAL HISTORY (Paginated) ============
    let historyOrders = [];
    let historyCurrentPage = 1;
    const historyPerPage = 5;

    function openHistoryModal(name, orders) {
        document.getElementById('history_customer_name').innerText = name;
        historyOrders = orders;
        historyCurrentPage = 1;

        const total = orders.length;
        const spent = orders.reduce((s, o) => s + parseFloat(o.grand_total || 0), 0);
        document.getElementById('history_total_orders').innerText = total;
        document.getElementById('history_total_spent').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(spent);

        renderHistoryPage();
        openModal('historyModalOverlay');
    }

    function renderHistoryPage() {
        const body = document.getElementById('history_table_body');
        const pagEl = document.getElementById('history_pagination');
        body.innerHTML = '';

        if (historyOrders.length === 0) {
            body.innerHTML = '<tr><td colspan="4" class="px-4 py-8 text-center text-gray-400 italic text-sm">Belum ada transaksi</td></tr>';
            pagEl.style.display = 'none';
            return;
        }

        const totalPages = Math.max(1, Math.ceil(historyOrders.length / historyPerPage));
        const start = (historyCurrentPage - 1) * historyPerPage;
        const end = Math.min(start + historyPerPage, historyOrders.length);

        historyOrders.slice(start, end).forEach(order => {
            const date = new Date(order.created_at).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            const statusMap = {
                paid: { cls: 'bg-green-100 text-green-700', lbl: 'Paid' },
                cancelled: { cls: 'bg-red-100 text-red-700', lbl: 'Cancelled' },
                pending: { cls: 'bg-yellow-100 text-yellow-700', lbl: 'Pending' },
            };
            const s = statusMap[order.order_status] || { cls: 'bg-gray-100 text-gray-600', lbl: order.order_status };
            body.innerHTML += `
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-4 py-2.5 text-xs font-bold text-gray-900">${order.order_number}</td>
                    <td class="px-4 py-2.5 text-xs text-gray-600">${date}</td>
                    <td class="px-4 py-2.5 text-xs font-bold text-right text-gray-900">Rp ${new Intl.NumberFormat('id-ID').format(order.grand_total)}</td>
                    <td class="px-4 py-2.5 text-center">
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase ${s.cls}">${s.lbl}</span>
                    </td>
                </tr>`;
        });

        pagEl.style.display = 'flex';
        document.getElementById('history_page_info').innerText = `${start + 1}–${end} dari ${historyOrders.length}`;
        document.getElementById('history_prev_btn').disabled = historyCurrentPage <= 1;
        document.getElementById('history_next_btn').disabled = historyCurrentPage >= totalPages;

        const nums = document.getElementById('history_page_numbers');
        nums.innerHTML = '';
        for (let p = 1; p <= totalPages; p++) {
            const active = p === historyCurrentPage ? 'bg-blue-600 text-white shadow-sm' : 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50';
            nums.innerHTML += `<button onclick="historyGoToPage(${p})" class="w-7 h-7 flex items-center justify-center text-xs font-bold rounded-lg transition-colors ${active}">${p}</button>`;
        }
    }

    function historyPrevPage() { if (historyCurrentPage > 1) { historyCurrentPage--; renderHistoryPage(); } }
    function historyNextPage() { if (historyCurrentPage < Math.ceil(historyOrders.length / historyPerPage)) { historyCurrentPage++; renderHistoryPage(); } }
    function historyGoToPage(p) { historyCurrentPage = p; renderHistoryPage(); }
</script>
@endpush
@endsection
