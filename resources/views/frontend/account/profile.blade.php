<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Saya - Pharmacare</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/branding/pharmacare-logo-opt.png') }}">
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

        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 40px; }
        .header h1 { font-size: 2rem; font-weight: 800; color: var(--primary-blue); }
        .back-link { text-decoration: none; color: var(--text-muted); font-weight: 600; display: flex; align-items: center; gap: 8px; transition: color 0.2s; }
        .back-link:hover { color: var(--primary-blue); }

        .dashboard-grid { display: grid; grid-template-columns: 280px 1fr; gap: 30px; }

        .sidebar { background: white; border-radius: 20px; padding: 30px 20px; height: fit-content; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
        .nav-item { display: flex; align-items: center; gap: 12px; padding: 14px 20px; border-radius: 12px; cursor: pointer; color: var(--text-muted); font-weight: 600; margin-bottom: 8px; transition: all 0.3s; border: none; background: none; width: 100%; text-align: left; font-size: 0.95rem; text-decoration: none; }
        .nav-item:hover { background: #f0f7ff; color: var(--primary-blue); }
        .nav-item.active { background: var(--primary-blue); color: white; box-shadow: 0 4px 15px rgba(0, 118, 214, 0.3); }

        .content-card { background: white; border-radius: 20px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); min-height: 500px; }
        .content-section { display: none; }
        .content-section.active { display: block; animation: fadeIn 0.4s ease; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        h2 { font-size: 1.5rem; margin-bottom: 30px; }

        .form-group { margin-bottom: 25px; }
        .form-label { display: block; font-weight: 600; margin-bottom: 10px; font-size: 0.9rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; font-size: 0.8rem; }
        .form-control { width: 100%; padding: 14px 20px; border: 2px solid #E9ECEF; border-radius: 12px; font-size: 1rem; transition: border-color 0.2s; outline: none; }
        .form-control:focus { border-color: var(--primary-blue); }
        textarea.form-control { resize: vertical; }
        .btn-update { background: var(--primary-blue); color: white; border: none; padding: 14px 30px; border-radius: 12px; font-weight: 700; cursor: pointer; transition: background 0.2s; }
        .btn-update:hover { background: #005FA3; }

        /* Address Cards */
        .address-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .address-card { border: 2px solid #eee; border-radius: 16px; padding: 25px; position: relative; transition: border-color 0.2s; }
        .address-card.primary { border-color: var(--primary-blue); background: #f8fbff; }
        .badge-primary { position: absolute; top: 15px; right: 15px; background: var(--primary-blue); color: white; font-size: 0.7rem; padding: 4px 10px; border-radius: 20px; font-weight: 800; }
        .address-label { font-weight: 800; font-size: 1.1rem; margin-bottom: 8px; }
        .address-text { color: var(--text-muted); font-size: 0.9rem; line-height: 1.5; min-height: 40px; margin-bottom: 20px; }
        .address-actions { display: flex; gap: 10px; }
        .btn-sm { padding: 8px 15px; border-radius: 8px; font-size: 0.85rem; font-weight: 700; cursor: pointer; border: none; }
        .btn-primary-light { background: #E6F3FF; color: var(--primary-blue); }
        .btn-danger-light { background: #FFF5F5; color: #C92A2A; }
        .btn-add-address { background: var(--primary-blue); color: white; border: none; padding: 12px 24px; border-radius: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 8px; }

        .empty-state { text-align: center; padding: 60px 0; }
        .empty-state .icon { font-size: 4rem; margin-bottom: 20px; }
        .empty-state p { color: var(--text-muted); margin-bottom: 20px; }

        /* Modal */
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; }
        .modal-overlay.open { display: flex; }
        .modal-box { background: white; padding: 40px; border-radius: 20px; width: 90%; max-width: 500px; max-height: 90vh; overflow-y: auto; display: flex; flex-direction: column; animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
        .modal-title { font-size: 1.3rem; font-weight: 800; margin-bottom: 25px; flex-shrink: 0; }

        @keyframes slideUp { 
            from { transform: translateY(30px); opacity: 0; } 
            to { transform: translateY(0); opacity: 1; } 
        }
        .modal-footer { display: flex; gap: 10px; margin-top: 25px; }
        .btn-cancel { flex: 1; padding: 12px; border-radius: 10px; border: 1px solid #ddd; background: white; cursor: pointer; font-weight: 700; }

        .danger-zone { background: #FFF5F5; border: 2px solid #FFE0E0; border-radius: 16px; padding: 25px; margin-top: 40px; }
        .danger-zone h3 { color: #C92A2A; margin-bottom: 15px; }

        @media (max-width: 900px) { .dashboard-grid { grid-template-columns: 1fr; } .address-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Akun Saya</h1>
        <a href="{{ route('store.index') }}" class="back-link">Kembali ke Toko</a>
    </div>

    <div class="dashboard-grid">
        <!-- Sidebar -->
        <div class="sidebar">
            <div style="text-align: center; margin-bottom: 30px;">
                @if($user->avatar)
                    <img src="{{ $user->avatar }}"
                         alt="{{ $user->name }}"
                         style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary-blue); margin: 0 auto 12px; display: block; box-shadow: 0 4px 15px rgba(0,118,214,0.2);">
                @else
                    <div style="width: 70px; height: 70px; background: var(--primary-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: 800; margin: 0 auto 12px;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <div style="font-weight: 700; font-size: 1.1rem;">{{ $user->name }}</div>
                <div style="font-size: 0.85rem; color: var(--text-muted);">{{ $user->email }}</div>
            </div>

            <div style="font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; padding: 0 10px; margin-bottom: 8px;">Pengaturan Akun</div>
            <button class="nav-item active" id="btn-profile" onclick="showSection('profile', this)">Edit Profil</button>
            <button class="nav-item" id="btn-addresses" onclick="showSection('addresses', this)">Alamat Pengiriman</button>
            <button class="nav-item" id="btn-password" onclick="showSection('password', this)">Ganti Password</button>
            <button class="nav-item" id="btn-wallet" onclick="toggleModal('walletModal')">Dompet Saya</button>

            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #f0f0f0;">
                <a href="{{ route('account.orders') }}" class="nav-item" style="color: var(--primary-blue);">Riwayat Pesanan</a>
            </div>

            <form action="{{ route('store.logout') }}" method="POST" style="margin-top: 8px;">
                @csrf
                <button type="submit" class="nav-item" style="color: #FF6B6B;">Keluar</button>
            </form>
        </div>

        <!-- Main Content -->
        <div class="content-card">

            <!-- SECTION: PROFILE -->
            <div id="section-profile" class="content-section active">
                <h2>Edit Profil</h2>
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
                    <button type="submit" class="btn-update">Simpan Perubahan</button>
                </form>
            </div>

            <!-- SECTION: ADDRESSES -->
            <div id="section-addresses" class="content-section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                    <h2>Alamat Pengiriman</h2>
                    <button class="btn-add-address" onclick="toggleModal('addressModal')">+ Tambah Alamat</button>
                </div>

                @if($addresses->count() > 0)
                    <div class="address-grid">
                        @foreach($addresses as $addr)
                        <div class="address-card {{ $addr->is_primary ? 'primary' : '' }}">
                            @if($addr->is_primary)
                                <span class="badge-primary">UTAMA</span>
                            @endif
                            <div class="address-label">{{ $addr->label }}</div>
                            <p class="address-text" style="margin-bottom: 5px;">{{ $addr->full_address }}</p>
                            @if($addr->province_id && $addr->province_id !== '-' && !is_numeric($addr->province_id))
                                <p style="font-size: 0.85rem; color: #64748b; font-weight: 600; margin-bottom: 10px;"><i class="fas fa-map-marker-alt"></i> {{ $addr->province_id }}</p>
                            @endif
                            <div class="address-actions">
                                @if(!$addr->is_primary)
                                <form action="{{ route('account.address.primary', $addr->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn-sm btn-primary-light">Jadikan Utama</button>
                                </form>
                                @endif
                                <form action="{{ route('account.address.delete', $addr->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus alamat ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-sm btn-danger-light">Hapus</button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div class="icon">📍</div>
                        <p>Belum ada alamat tersimpan.<br>Tambahkan alamat pengiriman Anda sekarang.</p>
                        <button class="btn-add-address" onclick="toggleModal('addressModal')">+ Tambah Alamat Pertama</button>
                    </div>
                @endif
            </div>

            <!-- SECTION: PASSWORD -->
            <div id="section-password" class="content-section">
                <h2>Ganti Password</h2>
                <form action="{{ route('account.password.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label class="form-label">Password Saat Ini</label>
                        <input type="password" name="current_password" class="form-control" placeholder="Masukkan password lama" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="new_password" class="form-control" placeholder="Minimal 8 karakter" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="new_password_confirmation" class="form-control" placeholder="Ulangi password baru" required>
                    </div>
                    <button type="submit" class="btn-update">Update Password</button>
                </form>
            </div>

        </div>
    </div>
</div>

<!-- Modal Tambah Alamat -->
<div id="addressModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-title">Tambah Alamat Baru</div>
        <form action="{{ route('account.address.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Label Alamat</label>
                <input type="text" name="label" class="form-control" placeholder="Cth: Rumah, Kantor, Kos..." required>
            </div>
            <div class="form-group">
                <label class="form-label">Kecamatan / Kota Tujuan</label>
                <div style="position: relative;">
                    <input type="text" id="destination_search" class="form-control" placeholder="Ketik nama kecamatan atau kota..." autocomplete="off" required>
                    <input type="hidden" name="city_id" id="destination_id" required>
                    <input type="hidden" name="province_id" value="-">
                    <div id="destination_results" style="display: none; position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; border-radius: 8px; max-height: 200px; overflow-y: auto; z-index: 1000; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-top: 5px;"></div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Alamat Lengkap</label>
                <textarea name="full_address" class="form-control" rows="3" placeholder="Jalan, No. Rumah, Kelurahan, Kecamatan..." required></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="toggleModal('addressModal')">Batal</button>
                <button type="submit" class="btn-update" style="flex: 1;">Simpan Alamat</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Dompet Saya -->
<div id="walletModal" class="modal-overlay">
    <div class="modal-box" style="max-width: 400px; padding: 0; overflow: hidden; display: flex; flex-direction: column;">
        <div style="background: linear-gradient(135deg, var(--primary-blue), #005BAA); padding: 30px 25px; color: white; flex-shrink: 0;">
            <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9; margin-bottom: 8px;">Saldo Dompet Tersedia</div>
            <div style="font-size: 2rem; font-weight: 800; margin-bottom: 20px;">Rp {{ number_format($user->wallet_balance, 0, ',', '.') }}</div>
            <form action="{{ url('/account/wallet/topup') }}" method="POST">
                @csrf
                <input type="hidden" name="amount" value="100000">
                <button type="submit" style="background: white; color: var(--primary-blue); border: none; padding: 12px 20px; border-radius: 12px; font-weight: 800; font-size: 0.9rem; cursor: pointer; transition: 0.3s; width: 100%; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    Isi Saldo (Simulasi Rp 100.000)
                </button>
            </form>
        </div>
        <div style="padding: 20px 25px; overflow-y: auto; flex: 1;">
            <h4 style="font-size: 1rem; font-weight: 800; margin-bottom: 15px; color: #1e293b;">Riwayat Transaksi</h4>
            @forelse($transactions as $trx)
            <div style="display: flex; justify-content: space-between; align-items: flex-start; padding: 12px 0; border-bottom: 1px solid #f0f0f0;">
                <div style="flex: 1; padding-right: 15px;">
                    <div style="font-weight: 700; font-size: 0.85rem; color: #334155; line-height: 1.4; margin-bottom: 4px;">{{ $trx->description }}</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $trx->created_at->format('d M Y, H:i') }}</div>
                </div>
                <div style="font-weight: 800; font-size: 0.9rem; flex-shrink: 0; color: {{ $trx->type === 'payment' ? '#ef4444' : '#10b981' }};">
                    {{ $trx->type === 'payment' ? '-' : '+' }}Rp {{ number_format($trx->amount, 0, ',', '.') }}
                </div>
            </div>
            @empty
            <div style="text-align: center; padding: 30px 10px; color: var(--text-muted); font-size: 0.85rem; font-style: italic;">Belum ada riwayat transaksi.</div>
            @endforelse
        </div>
        <div style="padding: 15px 25px; text-align: center; background: #f8fafc; border-top: 1px solid #f0f0f0; flex-shrink: 0;">
            <button type="button" class="btn-cancel" onclick="toggleModal('walletModal')" style="width: 100%; font-size: 0.9rem; padding: 10px;">Tutup</button>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="{{ asset('sweetalert/sweetalert2.all.min.js') }}" defer></script>
<script>
    function showSection(id, btn) {
        document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
        document.getElementById('section-' + id).classList.add('active');
        document.querySelectorAll('.nav-item').forEach(b => b.classList.remove('active'));
        if (btn) btn.classList.add('active');
        const url = new URL(window.location);
        url.searchParams.set('tab', id);
        window.history.pushState({}, '', url);
    }

    function toggleModal(id) {
        document.getElementById(id).classList.toggle('open');
    }

    // Close modal on outside click
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) this.classList.remove('open');
        });
    });

    // Auto-open from ?tab= param
    document.addEventListener('DOMContentLoaded', function() {
        const params = new URLSearchParams(window.location.search);
        const tab = params.get('tab');
        const tabMap = { 'profile': 'profile', 'addresses': 'addresses', 'password': 'password' };
        const target = tabMap[tab] || 'profile';
        showSection(target, document.getElementById('btn-' + target));

        // Auto-open Add Address modal if requested
        if (params.get('add_address') === '1') {
            toggleModal('addressModal');
            // Clean up the URL so it doesn't stay open on next refresh/redirect
            const newUrl = new URL(window.location);
            newUrl.searchParams.delete('add_address');
            window.history.replaceState({}, '', newUrl);
        }

        // Autocomplete Destinasi Komerce
        const searchInput = document.getElementById('destination_search');
        const resultsBox = document.getElementById('destination_results');
        const hiddenId = document.getElementById('destination_id');
        let debounceTimer;

        if(searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(debounceTimer);
                const query = this.value.trim();
                
                if (query.length < 3) {
                    resultsBox.style.display = 'none';
                    return;
                }

                debounceTimer = setTimeout(() => {
                    fetch(`/search-destinations?search=${encodeURIComponent(query)}`)
                        .then(res => res.json())
                        .then(data => {
                            resultsBox.innerHTML = '';
                            if (data.success && data.data.length > 0) {
                                data.data.forEach(item => {
                                    const div = document.createElement('div');
                                    div.style.padding = '10px 15px';
                                    div.style.cursor = 'pointer';
                                    div.style.borderBottom = '1px solid #eee';
                                    div.innerHTML = `<div style="font-weight: bold; color: #1e293b;">${item.district_name}, ${item.city_name}</div>
                                                     <div style="font-size: 0.8rem; color: #64748b;">${item.province_name} - ${item.zip_code}</div>`;
                                    div.addEventListener('mouseover', () => div.style.background = '#f8fafc');
                                    div.addEventListener('mouseout', () => div.style.background = 'white');
                                    div.addEventListener('click', () => {
                                        searchInput.value = item.label;
                                        hiddenId.value = item.id;
                                        const provinceInput = searchInput.closest('form').querySelector('input[name="province_id"]');
                                        if (provinceInput) provinceInput.value = item.label;
                                        resultsBox.style.display = 'none';
                                    });
                                    resultsBox.appendChild(div);
                                });
                                resultsBox.style.display = 'block';
                            } else {
                                resultsBox.innerHTML = '<div style="padding: 10px 15px; color: #94a3b8;">Tidak ditemukan.</div>';
                                resultsBox.style.display = 'block';
                            }
                        })
                        .catch(err => {
                            console.error(err);
                        });
                }, 500);
            });

            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
                    resultsBox.style.display = 'none';
                }
            });
        }

        const Toast = Swal.mixin({
            toast: true, position: 'top-end',
            showConfirmButton: false, timer: 3000, timerProgressBar: true
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
    });
</script>
</body>
</html>
