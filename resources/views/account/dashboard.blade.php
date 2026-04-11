<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Saya - Pharmacare</title>
    <style>
        :root {
            --primary-blue: #0076D6;
            --bg-body: #F4F7FA;
            --text-dark: #2D3436;
            --text-muted: #636E72;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
        body { background-color: var(--bg-body); color: var(--text-dark); line-height: 1.6; }

        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }

        /* Header Section */
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 40px; }
        .header h1 { font-size: 2rem; font-weight: 800; color: var(--primary-blue); }
        .back-link { text-decoration: none; color: var(--text-muted); font-weight: 600; display: flex; align-items: center; gap: 8px; transition: color 0.2s; }
        .back-link:hover { color: var(--primary-blue); }

        /* Dashboard Grid */
        .dashboard-grid { display: grid; grid-template-columns: 280px 1fr; gap: 30px; }

        /* Sidebar Nav */
        .sidebar { background: white; border-radius: 20px; padding: 30px 20px; height: fit-content; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 14px 20px; border-radius: 12px; cursor: pointer; color: var(--text-muted); font-weight: 600; margin-bottom: 10px; transition: all 0.3s; border: none; background: none; width: 100%; text-align: left; font-size: 0.95rem; }
        .nav-item:hover { background: #f0f7ff; color: var(--primary-blue); }
        .nav-item.active { background: var(--primary-blue); color: white; box-shadow: 0 4px 15px rgba(0, 118, 214, 0.3); }

        /* Main Content */
        .content-card { background: white; border-radius: 20px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); min-height: 500px; }
        .content-section { display: none; }
        .content-section.active { display: block; animation: fadeIn 0.4s ease; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        h2 { font-size: 1.5rem; margin-bottom: 30px; display: flex; align-items: center; gap: 12px; }

        /* Order Table */
        .order-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .order-table th { text-align: left; padding: 15px 20px; color: var(--text-muted); font-size: 0.85rem; text-transform: uppercase; border-bottom: 2px solid #f0f0f0; }
        .order-table td { padding: 20px; border-bottom: 1px solid #f0f0f0; font-size: 0.95rem; }
        .order-table tr:last-child td { border-bottom: none; }

        /* Badges */
        .badge { display: inline-block; padding: 5px 12px; border-radius: 30px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
        .badge-pending { background: #FFF9DB; color: #F08C00; }
        .badge-paid { background: #E7F5FF; color: #1971C2; }
        .badge-shipped { background: #F3F0FF; color: #6741D9; }
        .badge-delivered { background: #E6FCF5; color: #099268; }
        .badge-canceled { background: #FFF5F5; color: #C92A2A; }

        /* Forms */
        .form-group { margin-bottom: 25px; }
        .form-label { display: block; font-weight: 600; margin-bottom: 10px; font-size: 0.9rem; }
        .form-control { width: 100%; padding: 14px 20px; border: 2px solid #E9ECEF; border-radius: 12px; font-size: 1rem; transition: border-color 0.2s; outline: none; }
        .form-control:focus { border-color: var(--primary-blue); }
        .btn-update { background: var(--primary-blue); color: white; border: none; padding: 14px 30px; border-radius: 12px; font-weight: 700; cursor: pointer; transition: background 0.2s; display: flex; align-items: center; gap: 10px; }
        .btn-update:hover { background: #005FA3; }

        /* Empty State */
        .empty-state { text-align: center; padding: 60px 0; }
        .empty-state div { font-size: 4rem; margin-bottom: 20px; }
        .empty-state p { color: var(--text-muted); }

        @media (max-width: 900px) { .dashboard-grid { grid-template-columns: 1fr; } }

        /* Payment Modal Specific Styles */
        .pay-modal-grid { display: grid; grid-template-columns: 1.2fr 1fr; gap: 20px; }
        .pay-section { padding: 20px; border-radius: 12px; border: 1px solid #f0f0f0; margin-bottom: 20px; }
        .pay-title { font-weight: 800; font-size: 1rem; margin-bottom: 15px; color: var(--primary-blue); }
        .pay-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 0.9rem; }
        .pay-method-card { border: 2px solid #eee; border-radius: 12px; padding: 12px; cursor: pointer; transition: 0.2s; margin-bottom: 10px; }
        .pay-method-card:hover { border-color: var(--primary-blue); background: #f8fbff; }
        input[type="radio"]:checked + .pay-method-card { border-color: var(--primary-blue); background: #E6F3FF; }
        input[type="radio"] { display: none; }
        @media (max-width: 600px) { .pay-modal-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>📦 Pesanan Anda</h1>
        <a href="{{ route('store.index') }}" class="back-link">❮ Kembali ke Toko</a>
    </div>

    <div class="dashboard-grid">
        <!-- Sidebar Nav -->
        <div class="sidebar">
            <div style="text-align: center; margin-bottom: 30px;">
                <div style="width: 70px; height: 70px; background: var(--primary-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: 800; margin: 0 auto 15px;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div style="font-weight: 700; font-size: 1.1rem;">{{ $user->name }}</div>
                <div style="font-size: 0.85rem; color: var(--text-muted);">{{ $user->email }}</div>
            </div>

            <!-- Group: Pesanan -->
            <div style="font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; padding: 0 10px; margin-bottom: 8px;">Transaksi</div>
            <button class="nav-item active" id="btn-orders" onclick="showSection('orders', this)">
                📦 Pesanan
            </button>

            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #f0f0f0;">
                <a href="{{ route('account.profile') }}" class="nav-item" style="text-decoration: none; display: flex; color: var(--primary-blue);">
                    👤 Akun Saya
                </a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content-card">
            
            <!-- SECTION: ORDERS -->
            <div id="section-orders" class="content-section">
                <h2>📦 Pesanan Saya</h2>
                @if($orders->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        @foreach($orders as $order)
                        <div style="border: 1px solid #eee; border-radius: 16px; padding: 25px; transition: all 0.3s;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                                <div>
                                    <div style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 5px;">PESANAN #{{ $order->order_number }}</div>
                                    <div style="font-weight: 700; font-size: 1.1rem;">{{ $order->created_at->format('d M Y, H:i') }}</div>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 5px;">Total Belanja</div>
                                    <div style="font-weight: 800; font-size: 1.2rem; color: var(--primary-blue);">Rp {{ number_format($order->grand_total, 0, ',', '.') }}</div>
                                </div>
                            </div>

                            <!-- Progress Tracker -->
                            <div style="display: flex; justify-content: space-between; margin-bottom: 25px; position: relative; padding: 0 10px;">
                                <div style="position: absolute; top: 15px; left: 10%; right: 10%; height: 2px; background: #eee; z-index: 1;"></div>
                                @php
                                    $steps = [
                                        ['id' => 'ordered', 'label' => 'Dipesan', 'icon' => '📝'],
                                        ['id' => 'paid', 'label' => 'Dibayar', 'icon' => '💳'],
                                        ['id' => 'shipped', 'label' => 'Dikirim', 'icon' => '🚚'],
                                        ['id' => 'delivered', 'label' => 'Selesai', 'icon' => '✅'],
                                    ];
                                    $currentIdx = 0;
                                    foreach($steps as $idx => $s) {
                                        if($order->order_status == $s['id']) $currentIdx = $idx;
                                    }
                                @endphp

                                @foreach($steps as $idx => $step)
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 8px; z-index: 2; flex: 1;">
                                    <div style="width: 32px; height: 32px; background: {{ $idx <= $currentIdx ? 'var(--primary-blue)' : 'white' }}; border: 2px solid {{ $idx <= $currentIdx ? 'var(--primary-blue)' : '#eee' }}; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: {{ $idx <= $currentIdx ? 'white' : '#ccc' }}; font-size: 0.9rem; transition: all 0.3s;">
                                        {{ $idx <= $currentIdx ? '✓' : '' }}
                                    </div>
                                    <span style="font-size: 0.75rem; font-weight: 700; color: {{ $idx <= $currentIdx ? 'var(--text-dark)' : 'var(--text-muted)' }}">{{ $step['label'] }}</span>
                                </div>
                                @endforeach
                            </div>

                            @php
                                $itemDetails = '';
                                foreach($order->items as $oi) {
                                    $itemDetails .= '- ' . ($oi->item->name ?? 'Item dihapus') . ' (' . $oi->quantity . 'x)&#10;';
                                }
                            @endphp
                            <div style="display: flex; justify-content: space-between; align-items: center; background: #fafafa; padding: 15px 20px; border-radius: 12px; gap: 10px; flex-wrap: wrap;">
                                <div style="font-size: 0.9rem; color: var(--text-muted);">
                                    📍 Dikirim ke: <strong>{{ $order->address->label ?? 'Alamat Default' }}</strong>
                                </div>
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    @if($order->order_status === 'ordered' && $order->payment_status === 'pending')
                                        @php
                                            $hasPrescription = !empty($order->prescription_path) || $user->is_prescription_approved;
                                            $reqPres = false;
                                            foreach($order->items as $it) { if($it->item && $it->item->requires_prescription) { $reqPres = true; break; } }
                                        @endphp
                                        <button onclick="openPaymentModal({
                                            id: {{ $order->id }},
                                            number: '{{ $order->order_number }}',
                                            subtotal: {{ $order->sub_total }},
                                            reqPres: {{ $reqPres ? 'true' : 'false' }},
                                            hasPres: {{ $hasPrescription ? 'true' : 'false' }},
                                            url: '{{ route('account.orders.pay.post', $order->id) }}'
                                        })" style="background: #2F9E44; color: white; padding: 8px 18px; border-radius: 8px; font-size: 0.85rem; font-weight: 700; border:none; cursor:pointer; display: inline-flex; align-items: center; gap: 6px;">💳 Bayar Sekarang</button>
                                        <form action="{{ route('account.orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan dan menghapus pesanan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background: #FFF5F5; color: #C92A2A; border: none; padding: 8px 14px; border-radius: 8px; font-size: 0.85rem; font-weight: 700; cursor: pointer;">🗑 Batalkan</button>
                                        </form>
                                    @endif
                                    <button onclick="Swal.fire({title: 'Detail Order #{{ $order->order_number }}', html: '<pre style=\'text-align:left; font-family:inherit;\'>{{ $itemDetails }}</pre>', icon: 'info'})" style="background: white; border: 1px solid #ddd; padding: 8px 18px; border-radius: 8px; font-size: 0.85rem; font-weight: 700; cursor: pointer;">Lihat Detail</button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div>🛒</div>
                        <p>Anda belum memiliki riwayat pesanan.</p>
                        <a href="{{ route('store.index') }}" style="color: var(--primary-blue); font-weight: 700; text-decoration: none; display: block; margin-top: 15px;">Ayo berbelanja sekarang!</a>
                    </div>
                @endif
            </div>

            <!-- SECTION: ADDRESSES -->
            <div id="section-addresses" class="content-section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                    <h2>📍 Alamat Pengiriman</h2>
                    <button onclick="toggleModal('addressModal')" style="background: var(--primary-blue); color: white; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 700; cursor: pointer;">+ Tambah Alamat</button>
                </div>

                @if($addresses->count() > 0)
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        @foreach($addresses as $addr)
                        <div style="border: 2px solid {{ $addr->is_primary ? 'var(--primary-blue)' : '#eee' }}; border-radius: 16px; padding: 25px; position: relative;">
                            @if($addr->is_primary)
                                <span style="position: absolute; top: 15px; right: 15px; background: var(--primary-blue); color: white; font-size: 0.7rem; padding: 4px 10px; border-radius: 20px; font-weight: 800;">UTAMA</span>
                            @endif
                            <div style="font-weight: 800; font-size: 1.1rem; margin-bottom: 10px;">{{ $addr->label }}</div>
                            <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 20px; min-height: 40px;">{{ $addr->full_address }}</p>
                            
                            <div style="display: flex; gap: 10px;">
                                @if(!$addr->is_primary)
                                <form action="{{ route('account.address.primary', $addr->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" style="background: #E6F3FF; color: var(--primary-blue); border: none; padding: 8px 15px; border-radius: 8px; font-size: 0.85rem; font-weight: 700; cursor: pointer;">Jadikan Utama</button>
                                </form>
                                @endif
                                <form action="{{ route('account.address.delete', $addr->id) }}" method="POST" onsubmit="return confirm('Hapus alamat ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="background: #FFF5F5; color: #C92A2A; border: none; padding: 8px 15px; border-radius: 8px; font-size: 0.85rem; font-weight: 700; cursor: pointer;">Hapus</button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div>📍</div>
                        <p>Belum ada alamat pengiriman tersimpan.</p>
                    </div>
                @endif
            </div>

            <!-- Modal Tambah Alamat (Simple Hidden Div) -->
            <div id="addressModal" style="display:none; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
                <div style="background:white; padding:40px; border-radius:20px; width:100%; max-width:500px;">
                    <h3 style="margin-bottom:20px;">Tambah Alamat Baru</h3>
                    <form action="{{ route('account.address.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Label Alamat (Contoh: Rumah / Kantor)</label>
                            <input type="text" name="label" class="form-control" placeholder="Masukan label alamat" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Alamat Lengkap</label>
                            <textarea name="full_address" class="form-control" rows="3" placeholder="Masukan alamat detail (Jalan, No Rumah, Kelurahan, dll)" required></textarea>
                        </div>
                        <div style="display:flex; gap:10px; margin-top:20px;">
                            <button type="button" onclick="toggleModal('addressModal')" style="flex:1; padding:12px; border-radius:10px; border:1px solid #ddd; background:white; cursor:pointer; font-weight:700;">Batal</button>
                            <button type="submit" style="flex:1; padding:12px; border-radius:10px; border:none; background:var(--primary-blue); color:white; cursor:pointer; font-weight:700;">Simpan Alamat</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- SECTION: PROFILE -->
            <div id="section-profile" class="content-section">
                <h2>👤 Edit Profil</h2>
                <form action="{{ route('account.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Alamat Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>
                    <button type="submit" class="btn-update">💾 Simpan Perubahan</button>
                </form>
            </div>

            <!-- SECTION: PASSWORD -->
            <div id="section-password" class="content-section">
                <h2>🔐 Ganti Password</h2>
                <form action="{{ route('account.password.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label class="form-label">Password Saat Ini</label>
                        <input type="password" name="current_password" class="form-control" placeholder="••••••••" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="new_password" class="form-control" placeholder="Minimal 8 karakter" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="new_password_confirmation" class="form-control" placeholder="••••••••" required>
                    </div>
                    <button type="submit" class="btn-update">🚀 Update Password</button>
                </form>
            </div>

        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="{{ asset('sweetalert/sweetalert2.all.min.js') }}"></script>
<script>
    function showSection(id, btn) {
        document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
        document.getElementById('section-' + id).classList.add('active');
        document.querySelectorAll('.nav-item').forEach(b => b.classList.remove('active'));
        if(btn) btn.classList.add('active');
        // Update URL without reload
        const url = new URL(window.location);
        url.searchParams.set('tab', id);
        window.history.pushState({}, '', url);
    }

    // Auto-open tab from ?tab= URL param
    document.addEventListener('DOMContentLoaded', function() {
        const params = new URLSearchParams(window.location.search);
        const tab = params.get('tab');
        // Map 'account' → open profile; specific tabs open directly
        const tabMap = {
            'orders': 'orders',
            'account': 'profile',
            'addresses': 'addresses',
            'profile': 'profile',
            'password': 'password',
        };
        const target = tabMap[tab] || 'orders';
        const btn = document.getElementById('btn-' + target);
        showSection(target, btn);
    });

    function toggleModal(id) {
        const modal = document.getElementById(id);
        modal.style.display = modal.style.display === 'none' ? 'flex' : 'none';
    }

    // Close modal on click outside
    window.onclick = function(event) {
        if (event.target.id === 'addressModal') {
            toggleModal('addressModal');
        }
    }

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });

    @if(session('success'))
        Toast.fire({ icon: 'success', title: '{{ session('success') }}' });
    @endif

    @if(session('error'))
        Toast.fire({ icon: 'error', title: '{{ session('error') }}' });
    @endif

    @if($errors->any())
        Toast.fire({ icon: 'error', title: '{{ $errors->first() }}' });
    @endif
</script>

    <!-- PAYMENT MODAL -->
    <div id="paymentModal" style="display:none; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:99999; align-items:flex-start; justify-content:center; overflow-y:auto; padding: 40px 20px;">
        <div style="background:white; border-radius:24px; width:100%; max-width:800px; position:relative; animation: slideUp 0.3s ease-out;">
            <button onclick="toggleModal('paymentModal')" style="position: absolute; right: 20px; top: 20px; border: none; background: #f0f0f0; width: 32px; height: 32px; border-radius: 50%; cursor: pointer;">✕</button>
            <div style="padding: 30px;">
                <h3 style="margin-bottom:25px; font-size: 1.4rem;">💳 Konfirmasi Pembayaran</h3>
                
                <form id="paymentForm" method="POST">
                    @csrf
                    <div class="pay-modal-grid">
                        <div class="left-col">
                            <div class="pay-section">
                                <div class="pay-title">📦 Info Pesanan</div>
                                <div class="pay-row"><span>No Pesanan</span><strong id="pay-order-number">ORD-XXX</strong></div>
                                <div id="pres-warning-box" style="display:none; margin-top:15px; background:#FFF5F5; border:1px solid #FFC9C9; color:#C92A2A; padding:12px; border-radius:10px; font-size:0.8rem;">
                                    ⚠️ <strong>Resep Diperlukan:</strong> Pesanan ini mengandung obat keras dan resep Anda belum terverifikasi. Pembayaran diblokir.
                                </div>
                                <div id="pres-success-box" style="display:none; margin-top:15px; background:#E6FCF5; border:1px solid #C3FAE8; color:#087F5B; padding:12px; border-radius:10px; font-size:0.8rem;">
                                    ✅ <strong>Resep Terverifikasi:</strong> Resep manual terlampir atau akun sudah diapprove Dokter.
                                </div>
                            </div>

                            <div class="pay-section">
                                <div class="pay-title">🚚 Pilih Durasi Pengiriman</div>
                                <label><input type="radio" name="shipping_method" value="instant" checked onchange="calcTotal()">
                                    <div class="pay-method-card">⚡ <strong>Instant Delivery</strong><br><span style="font-size:0.8rem; color:#666;">2 Jam Sampai (Rp 15.000)</span></div>
                                </label>
                                <label><input type="radio" name="shipping_method" value="regular" onchange="calcTotal()">
                                    <div class="pay-method-card">🚚 <strong>JNE / J&T Reguler</strong><br><span style="font-size:0.8rem; color:#666;">2-3 Hari Kerja (Rp 10.000)</span></div>
                                </label>
                            </div>
                        </div>

                        <div class="right-col">
                            <div class="pay-section" style="background:#f8f9fa;">
                                <div class="pay-title">🏦 Metode Pembayaran</div>
                                <label><input type="radio" name="payment_method" value="qris" checked>
                                    <div class="pay-method-card">📱 <strong>QRIS</strong></div>
                                </label>
                                <label><input type="radio" name="payment_method" value="paylater">
                                    <div class="pay-method-card">💳 <strong>Paylater</strong></div>
                                </label>

                                <div style="margin-top:20px; padding-top:15px; border-top:1px dashed #ddd;">
                                    <div class="pay-row"><span>Subtotal</span><span id="pay-subtotal">Rp 0</span></div>
                                    <div class="pay-row"><span>Ongkir</span><span id="pay-shipping">Rp 15.000</span></div>
                                    <div class="pay-row" style="font-weight:800; border-top:1px solid #eee; padding-top:10px; color:var(--primary-blue); font-size:1.1rem;"><span>Total</span><span id="pay-total">Rp 0</span></div>
                                </div>

                                <button type="submit" id="pay-confirm-btn" style="width:100%; padding:15px; background:#2F9E44; color:white; border:none; border-radius:12px; font-weight:800; margin-top:20px; cursor:pointer;">Konfirmasi & Bayar</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('sweetalert/sweetalert2.all.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check for pay_id in URL
            const urlParams = new URLSearchParams(window.location.search);
            const payId = urlParams.get('pay_id');
            if (payId) {
                // Find and trigger the button for this order
                const btn = document.querySelector(`button[onclick*="id: ${payId},"]`);
                if (btn) btn.click();
            }
        });

        let currentSubtotal = 0;
        function openPaymentModal(data) {
            currentSubtotal = data.subtotal;
            document.getElementById('pay-order-number').innerText = data.number;
            document.getElementById('pay-subtotal').innerText = 'Rp ' + data.subtotal.toLocaleString('id-ID');
            document.getElementById('paymentForm').action = data.url;

            // Prescription logic
            const warnBox = document.getElementById('pres-warning-box');
            const successBox = document.getElementById('pres-success-box');
            const btn = document.getElementById('pay-confirm-btn');

            warnBox.style.display = 'none';
            successBox.style.display = 'none';
            btn.disabled = false;
            btn.style.opacity = '1';

            if (data.reqPres) {
                if (!data.hasPres) {
                    warnBox.style.display = 'block';
                    btn.disabled = true;
                    btn.style.opacity = '0.5';
                    btn.innerText = 'Resep Diperlukan';
                } else {
                    successBox.style.display = 'block';
                    btn.innerText = 'Konfirmasi & Bayar';
                }
            } else {
                btn.innerText = 'Konfirmasi & Bayar';
            }

            calcTotal();
            toggleModal('paymentModal');
        }

        function calcTotal() {
            const ship = document.querySelector('input[name="shipping_method"]:checked').value;
            const cost = ship === 'instant' ? 15000 : 10000;
            const total = currentSubtotal + cost;
            document.getElementById('pay-shipping').innerText = 'Rp ' + cost.toLocaleString('id-ID');
            document.getElementById('pay-total').innerText = 'Rp ' + total.toLocaleString('id-ID');
        }

        function showSection(section, btn) {
            document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
            document.getElementById('section-' + section).classList.add('active');
            document.querySelectorAll('.nav-item').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        }

        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.style.display = (modal.style.display === 'none' || modal.style.display === '') ? 'flex' : 'none';
        }
    </script>
    
    <style>@keyframes slideUp { from { transform: translateY(20px); opacity:0; } to { transform: translateY(0); opacity:1; } }</style>
</body>
</html>
