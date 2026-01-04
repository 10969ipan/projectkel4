@extends('layouts.app')

@section('title', 'Manajemen Permintaan Barang')

@section('header')
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Manajemen Permintaan Barang</h1>
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 mt-4 md:mt-0">
            @if (auth()->user()->isStaff())
                <a href="{{ route('item-requests.create') }}"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-plus mr-2"></i> Permintaan Baru
                </a>
            @endif
            @if (auth()->user()->isAdmin())
                <div class="relative">
                    <label for="status-filter" class="sr-only">Filter Status</label>
                    <div class="flex items-center">
                        <span class="mr-2 text-sm text-gray-700">Filter:</span>
                        <select id="status-filter"
                            class="appearance-none block w-full pl-3 pr-10 py-2 text-base border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                            <option value="">Semua</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui
                            </option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak
                            </option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                            <i class="fas fa-chevron-down text-gray-400"></i>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('content')
    <div class="bg-white rounded-lg shadow-md overflow-hidden mt-4">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barang
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peminta
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($requests as $request)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $request->item->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $request->quantity }}
                                {{ $request->item->unit->symbol }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $request->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if ($request->status === 'pending')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                @elseif($request->status === 'approved')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Disetujui</span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Ditolak</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $request->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button type="button" 
                                        onclick="openDetailModal(this)"
                                        data-id="{{ $request->id }}"
                                        data-item-name="{{ $request->item->name }}"
                                        data-size="{{ $request->size ?? '-' }}"
                                        data-quantity="{{ $request->quantity }}"
                                        data-unit="{{ $request->item->unit->symbol }}"
                                        data-requester="{{ $request->user->name }}"
                                        data-date="{{ $request->created_at->format('d M Y H:i') }}"
                                        data-status="{{ $request->status }}"
                                        data-reason="{{ $request->reason }}"
                                        data-processed-by="{{ $request->processedBy->name ?? '-' }}"
                                        data-processed-at="{{ $request->processed_at ? $request->processed_at->format('d M Y H:i') : '-' }}"
                                        data-rejection-reason="{{ $request->rejection_reason ?? '-' }}"
                                        class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                        <i class="fas fa-eye mr-1"></i> Detail
                                    </button>
                                    @if (auth()->user()->isAdmin() && $request->status === 'pending')
                                        <form action="{{ route('item-requests.approve', $request->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="inline-flex items-center px-3 py-1 border border-green-500 rounded-md text-sm font-medium text-green-700 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                <i class="fas fa-check mr-1"></i> Setujui
                                            </button>
                                        </form>
                                        <button type="button" onclick="openRejectModal({{ $request->id }})"
                                            class="inline-flex items-center px-3 py-1 border border-red-500 rounded-md text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            <i class="fas fa-times mr-1"></i> Tolak
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Tidak ada permintaan
                                ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($requests->hasPages())
            <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

    <!-- Reject Modal -->
    <div id="reject-modal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Tolak Permintaan</h3>
                    <div class="mt-2">
                        <form id="reject-form" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="rejection_reason" class="block text-sm font-medium text-gray-700">Alasan
                                    penolakan</label>
                                <textarea name="rejection_reason" id="rejection_reason" rows="3" required
                                    class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md px-3 py-2 border"></textarea>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                    <button type="button" onclick="document.getElementById('reject-form').submit();"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:col-start-2 sm:text-sm">
                        Tolak
                    </button>
                    <button type="button" onclick="closeRejectModal()"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Detail Modal --}}
    <div id="detail-modal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeDetailModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6">
                <div class="absolute top-0 right-0 pt-4 pr-4">
                    <button type="button" onclick="closeDetailModal()"
                        class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <span class="sr-only">Tutup</span>
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div>
                    <div class="mt-3 sm:mt-0">
                        <h3 class="text-lg leading-6 font-bold text-gray-900 mb-4" id="modal-title">
                            <i class="fas fa-info-circle text-primary-600 mr-2"></i>Detail Permintaan Barang
                        </h3>
                        <div class="mt-4">
                            {{-- Grid Informasi --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Barang</label>
                                    <p class="text-sm font-semibold text-gray-900" id="detail-item-name">-</p>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Ukuran</label>
                                    <p class="text-sm text-gray-900" id="detail-size">-</p>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Jumlah</label>
                                    <p class="text-sm font-semibold text-gray-900" id="detail-quantity">-</p>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Peminta</label>
                                    <p class="text-sm text-gray-900" id="detail-requester">-</p>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Tanggal Permintaan</label>
                                    <p class="text-sm text-gray-900" id="detail-date">-</p>
                                </div>
                                <div class="bg-gray-50 p-3 rounded-lg">
                                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Status</label>
                                    <p class="text-sm" id="detail-status">-</p>
                                </div>
                            </div>

                            {{-- Alasan Permintaan --}}
                            <div class="mt-4 bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                                <label class="block text-xs font-medium text-blue-800 uppercase tracking-wider mb-2">
                                    <i class="fas fa-comment-alt mr-1"></i>Alasan Permintaan
                                </label>
                                <p class="text-sm text-blue-900" id="detail-reason">-</p>
                            </div>

                            {{-- Info Pemrosesan (jika sudah diproses) --}}
                            <div id="detail-processed-info" class="mt-4 hidden">
                                <div class="border-t border-gray-200 pt-4">
                                    <h4 class="text-sm font-semibold text-gray-900 mb-3">
                                        <i class="fas fa-clipboard-check mr-1"></i>Informasi Pemrosesan
                                    </h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div class="bg-gray-50 p-3 rounded-lg">
                                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Diproses Oleh</label>
                                            <p class="text-sm text-gray-900" id="detail-processed-by">-</p>
                                        </div>
                                        <div class="bg-gray-50 p-3 rounded-lg">
                                            <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Tanggal Pemrosesan</label>
                                            <p class="text-sm text-gray-900" id="detail-processed-at">-</p>
                                        </div>
                                    </div>
                                    {{-- Alasan Penolakan (jika ditolak) --}}
                                    <div id="detail-rejection-section" class="mt-3 hidden bg-red-50 border-l-4 border-red-400 p-4 rounded">
                                        <label class="block text-xs font-medium text-red-800 uppercase tracking-wider mb-2">
                                            <i class="fas fa-exclamation-circle mr-1"></i>Alasan Penolakan
                                        </label>
                                        <p class="text-sm text-red-900" id="detail-rejection-reason">-</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-6">
                    <button type="button" onclick="closeDetailModal()"
                        class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:text-sm">
                        <i class="fas fa-times mr-2"></i>Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Status filter change handler
        document.getElementById('status-filter')?.addEventListener('change', function() {
            window.location.href = '{{ route('item-requests.index') }}?status=' + this.value;
        });

        // Detail Modal Functions
        function openDetailModal(button) {
            // Ambil data dari button attributes
            const data = {
                itemName: button.dataset.itemName,
                size: button.dataset.size,
                quantity: button.dataset.quantity,
                unit: button.dataset.unit,
                requester: button.dataset.requester,
                date: button.dataset.date,
                status: button.dataset.status,
                reason: button.dataset.reason,
                processedBy: button.dataset.processedBy,
                processedAt: button.dataset.processedAt,
                rejectionReason: button.dataset.rejectionReason
            };

            // Isi data ke modal
            document.getElementById('detail-item-name').textContent = data.itemName;
            
            // Ukuran dengan badge jika ada
            const sizeElement = document.getElementById('detail-size');
            if (data.size && data.size !== '-') {
                sizeElement.innerHTML = `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">${data.size}</span>`;
            } else {
                sizeElement.textContent = '-';
            }
            
            document.getElementById('detail-quantity').textContent = `${data.quantity} ${data.unit}`;
            document.getElementById('detail-requester').textContent = data.requester;
            document.getElementById('detail-date').textContent = data.date;
            document.getElementById('detail-reason').textContent = data.reason;

            // Status dengan badge warna
            const statusElement = document.getElementById('detail-status');
            if (data.status === 'pending') {
                statusElement.innerHTML = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"><i class="fas fa-clock mr-1"></i>Pending</span>';
            } else if (data.status === 'approved') {
                statusElement.innerHTML = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-check-circle mr-1"></i>Disetujui</span>';
            } else {
                statusElement.innerHTML = '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800"><i class="fas fa-times-circle mr-1"></i>Ditolak</span>';
            }

            // Info pemrosesan (jika sudah diproses)
            const processedInfo = document.getElementById('detail-processed-info');
            if (data.status !== 'pending' && data.processedBy !== '-') {
                processedInfo.classList.remove('hidden');
                document.getElementById('detail-processed-by').textContent = data.processedBy;
                document.getElementById('detail-processed-at').textContent = data.processedAt;

                // Alasan penolakan (jika ditolak)
                const rejectionSection = document.getElementById('detail-rejection-section');
                if (data.status === 'rejected' && data.rejectionReason !== '-') {
                    rejectionSection.classList.remove('hidden');
                    document.getElementById('detail-rejection-reason').textContent = data.rejectionReason;
                } else {
                    rejectionSection.classList.add('hidden');
                }
            } else {
                processedInfo.classList.add('hidden');
            }

            // Tampilkan modal
            document.getElementById('detail-modal').classList.remove('hidden');
        }

        function closeDetailModal() {
            document.getElementById('detail-modal').classList.add('hidden');
        }

        // Close modal dengan ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeDetailModal();
            }
        });

        function openRejectModal(requestId) {
            const form = document.getElementById('reject-form');
            form.action = `/item-requests/${requestId}/reject`;
            document.getElementById('reject-modal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('reject-modal').classList.add('hidden');
        }
    </script>
@endsection
