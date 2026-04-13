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
        <h1>Pesanan Anda</h1>
        <a href="{{ route('store.index') }}" class="back-link">← Kembali ke Toko</a>
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

            <div style="font-size: 0.7rem; font-weight: 800; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; padding: 0 10px; margin-bottom: 8px;">Transaksi</div>
            <button class="nav-item active" id="btn-orders" onclick="showSection('orders', this)">Pesanan</button>
            <button class="nav-item" id="btn-subscriptions" onclick="showSection('subscriptions', this)">Langganan</button>
            
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #f0f0f0;">
                <a href="{{ route('account.profile') }}" class="nav-item" style="color: var(--primary-blue);">Pengaturan Akun</a>
            </div>
        </div>

        <!-- Main Content Area -->
        <div style="display: flex; flex-direction: column; gap: 30px;">
            
            <!-- SINGLE RELEVANT WELLNESS ARTICLE -->
            @if(count($wellnessArticles) > 0)
            @php $article = $wellnessArticles->first(); @endphp
            <script>window._dashboardArticle = {!! \Illuminate\Support\Js::from($article) !!};</script>

            <div class="wellness-highlights" style="position: relative; overflow: hidden; border-radius: 24px; box-shadow: 0 15px 35px rgba(0,118,214,0.1); height: 280px; background-image: linear-gradient(to right, rgba(0,0,0,0.85) 10%, rgba(0,0,0,0.4) 50%, transparent 100%), url('{{ asset($article->image_path) }}'); background-size: cover; background-position: center; display: flex; flex-direction: column; justify-content: center; padding: 40px;">
                <div style="max-width: 450px; color: white; position: relative; z-index: 10;">
                    <div style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 10px; color: #93c5fd;">Terkait Pesanan Anda</div>
                    <h3 style="font-size: 1.8rem; font-weight: 800; margin-top: 0; margin-bottom: 15px; line-height: 1.2;">{{ $article->title }}</h3>
                    <p style="font-size: 1rem; opacity: 0.9; line-height: 1.6; margin-bottom: 20px;">{{ Str::limit($article->content, 120) }}</p>
                    <a href="javascript:void(0)" onclick="window.openArticleModal(window._dashboardArticle)" style="display: inline-flex; align-items: center; gap: 8px; color: white; text-decoration: none; font-weight: 700; font-size: 0.9rem; border-bottom: 2px solid var(--primary-blue); cursor: pointer; position: relative;">Baca Selengkapnya <i class="fas fa-arrow-right" style="font-size: 0.7rem;"></i></a>
                </div>
            </div>

            @endif


            <div class="content-card" style="min-height: 600px;">
            
            <!-- SECTION: ORDERS -->
            <div id="section-orders" class="content-section">
                <h2>Pesanan Saya</h2>
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

                            @if(in_array($order->order_status, ['cancelled', 'rejected']))
                                <div style="display: flex; justify-content: center; margin-bottom: 25px; padding: 15px; background: #FFF5F5; border-radius: 12px; border: 1px dashed #ffa8a8;">
                                    <div style="font-weight: 800; color: #C92A2A; display: flex; align-items: center; gap: 8px;">
                                        <i class="fas fa-times-circle"></i> Pesanan Dibatalkan
                                    </div>
                                </div>
                            @else
                                <!-- Progress Tracker with SVG Icons -->
                                <div style="display: flex; justify-content: space-between; margin-bottom: 25px; position: relative; padding: 0 10px;">
                                    @php
                                        $steps = [
                                            ['id' => 'ordered', 'label' => 'Dipesan', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>'],
                                            ['id' => 'paid',    'label' => 'Dibayar',  'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>'],
                                            ['id' => 'processing','label' => 'Dikirim',  'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>'],
                                            ['id' => 'completed','label' => 'Selesai',  'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>'],
                                        ];
                                        $currentIdx = 0;
                                        foreach($steps as $idx => $s) {
                                            if($order->order_status == $s['id']) $currentIdx = $idx;
                                        }
                                    @endphp
                                    <div style="position: absolute; top: 20px; left: 10%; right: 10%; height: 3px; background: #eee; z-index: 1; border-radius:99px;"></div>
                                    <div style="position: absolute; top: 20px; left: 10%; height: 3px; width: calc({{ $currentIdx }} * (80% / 3)); background: var(--primary-blue); z-index: 1; border-radius:99px; transition: width 0.5s ease;"></div>

                                    @foreach($steps as $idx => $step)
                                    @php $done = $idx <= $currentIdx; $active = $idx == $currentIdx; @endphp
                                    <div style="display: flex; flex-direction: column; align-items: center; gap: 8px; z-index: 2; flex: 1;">
                                        <div style="width: 42px; height: 42px; background: {{ $done ? 'var(--primary-blue)' : 'white' }}; border: 2px solid {{ $done ? 'var(--primary-blue)' : '#e2e8f0' }}; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: {{ $done ? 'white' : '#cbd5e1' }}; transition: all 0.4s; box-shadow: {{ $active ? '0 0 0 4px rgba(0,118,214,0.15)' : 'none' }};">
                                            {!! $step['icon'] !!}
                                        </div>
                                        <span style="font-size: 0.72rem; font-weight: 800; color: {{ $done ? 'var(--primary-blue)' : '#94a3b8' }}; text-transform: uppercase; letter-spacing: 0.5px;">{{ $step['label'] }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            @endif

                            @php
                                $hasPrescription = !empty($order->prescription_path) || $user->is_prescription_approved;
                                $reqPres = false;
                                foreach($order->items as $it) { if($it->item && $it->item->requires_prescription) { $reqPres = true; break; } }
                                $orderItemsJson = [];
                                foreach($order->items as $oi) {
                                    $orderItemsJson[] = [
                                        'name'     => $oi->item->name ?? 'Item Dihapus',
                                        'qty'      => $oi->quantity,
                                        'price'    => $oi->price,
                                        'subtotal' => $oi->sub_total,
                                        'image'    => $oi->item->image_path ?? null,
                                    ];
                                }
                            @endphp
                            <div style="display: flex; justify-content: space-between; align-items: center; background: #fafafa; padding: 15px 20px; border-radius: 12px; gap: 10px; flex-wrap: wrap;">
                                <div style="font-size: 0.9rem; color: var(--text-muted);">
                                    Dikirim ke: <strong>{{ $order->address->label ?? 'Alamat Default' }}</strong>
                                </div>
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    @if($order->order_status === 'ordered' && $order->payment_status === 'pending')
                                        <button onclick="openPaymentModal({
                                            id: {{ $order->id }},
                                            number: '{{ $order->order_number }}',
                                            subtotal: {{ $order->sub_total }},
                                            reqPres: {{ $reqPres ? 'true' : 'false' }},
                                            hasPres: {{ $hasPrescription ? 'true' : 'false' }},
                                            url: '{{ route('account.orders.pay.post', $order->id) }}'
                                        })" style="background: #2F9E44; color: white; padding: 8px 18px; border-radius: 8px; font-size: 0.85rem; font-weight: 700; border:none; cursor:pointer;">Bayar Sekarang</button>
                                        <form action="{{ route('account.orders.cancel', $order->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan dan menghapus pesanan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background: #FFF5F5; color: #C92A2A; border: none; padding: 8px 14px; border-radius: 8px; font-size: 0.85rem; font-weight: 700; cursor: pointer;">Batalkan</button>
                                        </form>
                                    @endif
                                    <button onclick="openOrderDetail({{ $order->id }})" style="background: white; border: 1px solid #ddd; padding: 8px 18px; border-radius: 8px; font-size: 0.85rem; font-weight: 700; cursor: pointer;">Lihat Detail</button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div style="margin-top: 25px; display: flex; justify-content: center; width: 100%;">
                        {{ $orders->links('vendor.pagination.pharmacare') }}
                    </div>
                @else
                    <div class="empty-state">
                        <div style="font-size:3rem; color:#ddd;"><i class="fas fa-shopping-bag"></i></div>
                        <p>Anda belum memiliki riwayat pesanan.</p>
                        <a href="{{ route('store.index') }}" style="color: var(--primary-blue); font-weight: 700; text-decoration: none; display: block; margin-top: 15px;">Ayo berbelanja sekarang!</a>
                    </div>
                @endif
            </div>

            <!-- SECTION: SUBSCRIPTIONS -->
            <div id="section-subscriptions" class="content-section">
                <h2>Langganan Obat Saya</h2>
                @if($subscriptions->count() > 0)
                    <div style="display: grid; grid-template-columns: 1fr; gap: 20px;">
                        @foreach($subscriptions as $sub)
                        <div style="background: #fdfdfd; border: 1px solid #edf2f7; border-radius: 16px; padding: 20px; display: flex; align-items: center; gap: 20px;">
                            <div style="width: 80px; height: 80px; background: white; border: 1px solid #eee; border-radius: 12px; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0;">
                                @if($sub->item->image_path)
                                    <img src="{{ asset($sub->item->image_path) }}" style="width: 100%; height: 100%; object-fit: contain;" loading="lazy">
                                @else
                                    <span style="font-size: 2rem;">💊</span>
                                @endif
                            </div>
                            <div style="flex: 1;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                    <div>
                                        <h4 style="font-size: 1.1rem; color: #1e293b; margin-bottom: 4px;">{{ $sub->item->name }}</h4>
                                        <div style="color: #64748b; font-size: 0.85rem;">Interval: <strong>Setiap {{ $sub->interval_days }} Hari</strong></div>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; font-weight: 700;">Status</div>
                                        <span class="badge badge-delivered" style="background: #ecfdf5; color: #059669;">{{ strtoupper($sub->status) }}</span>
                                    </div>
                                </div>
                                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px dashed #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                                    <div style="font-size: 0.85rem; color: #475569;">
                                        <i class="far fa-calendar-alt"></i> Pengiriman Berikutnya: <strong>{{ \Carbon\Carbon::parse($sub->next_delivery_date)->format('d M Y') }}</strong>
                                    </div>
                                    <div style="color: #059669; font-weight: 800; font-size: 0.95rem;">
                                        Hemat 10% Aktif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <div style="font-size:3rem; color:#ddd;"><i class="fas fa-sync-alt"></i></div>
                        <p>Anda belum berlangganan obat apapun.</p>
                        <p style="font-size: 0.85rem; margin-top: 10px;">Pilih opsi "Langganan" saat membeli obat untuk pengiriman otomatis.</p>
                    </div>
                @endif
            </div>

            <!-- SECTION: ADDRESSES -->
            <div id="section-addresses" class="content-section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                    <h2>Alamat Pengiriman</h2>
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

            <!-- SECTION: PASSWORD -->
            <div id="section-password" class="content-section">
                <h2>Ganti Password</h2>
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
                    <button type="submit" class="btn-update">Update Password</button>
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

{{-- Pre-encode all order data safely as a JS map --}}
<script>
    const orderDataMap = {
        @foreach($orders as $order)
        @php
            $mapItems = [];
            foreach($order->items as $oi) {
                $mapItems[] = [
                    'name'     => $oi->item->name ?? 'Item Dihapus',
                    'qty'      => $oi->quantity,
                    'price'    => $oi->price,
                    'subtotal' => $oi->sub_total,
                    'image'    => $oi->item->image_path ?? null,
                ];
            }
            $orderData = [
                'number'          => $order->order_number,
                'date'            => $order->created_at->format('d M Y, H:i'),
                'created_at'      => $order->created_at->format('d M Y, H:i'),
                'updated_at'      => $order->updated_at->format('d M Y, H:i'),
                'customer'        => $user->name,
                'address'         => $order->address->full_address ?? 'Alamat belum diisi',
                'address_label'   => $order->address->label ?? '-',
                'payment_method'  => strtoupper($order->payment_method ?? '-'),
                'shipping_method' => ($order->shipping_method === 'instant' ? 'Instant Delivery (2 Jam)' : 'JNE / J&T Reguler'),
                'shipping_cost'   => $order->shipping_cost ?? 0,
                'sub_total'       => $order->sub_total,
                'grand_total'     => $order->grand_total,
                'status'          => $order->order_status,
                'items'           => $mapItems,
            ];
        @endphp
        {{ $order->id }}: {!! json_encode($orderData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!},
        @endforeach

        @if($justPaidOrder)
        {{ $justPaidOrder->id }}: {!! json_encode([
            'number'          => $justPaidOrder->order_number,
            'date'            => $justPaidOrder->created_at->format('d M Y, H:i'),
            'created_at'      => $justPaidOrder->created_at->format('d M Y, H:i'),
            'updated_at'      => $justPaidOrder->updated_at->format('d M Y, H:i'),
            'customer'        => $user->name,
            'address'         => $justPaidOrder->address->full_address ?? 'Alamat belum diisi',
            'address_label'   => $justPaidOrder->address->label ?? '-',
            'payment_method'  => strtoupper($justPaidOrder->payment_method ?? '-'),
            'shipping_method' => ($justPaidOrder->shipping_method === 'instant' ? 'Instant Delivery (2 Jam)' : 'JNE / J&T Reguler'),
            'shipping_cost'   => $justPaidOrder->shipping_cost ?? 0,
            'sub_total'       => $justPaidOrder->sub_total,
            'grand_total'     => $justPaidOrder->grand_total,
            'status'          => $justPaidOrder->order_status,
            'items'           => collect($justPaidOrder->items)->map(fn($oi) => [
                'name'     => $oi->item->name ?? 'Item Dihapus',
                'qty'      => $oi->quantity,
                'price'    => $oi->price,
                'subtotal' => $oi->sub_total,
                'image'    => $oi->item->image_path ?? null,
            ])->toArray(),
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!},
        @endif
    };
</script>

    <!-- ORDER DETAIL MODAL -->
    <div id="orderDetailModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15,23,42,0.7); z-index:99998; align-items:flex-start; justify-content:center; overflow-y:auto; padding:40px 20px; backdrop-filter:blur(4px);">
        <div style="background:white; border-radius:24px; width:100%; max-width:680px; position:relative; box-shadow: 0 25px 50px rgba(0,0,0,0.25); animation: slideUp 0.35s cubic-bezier(0.23,1,0.32,1);">
            <!-- Header -->
            <div style="background:var(--primary-blue); color:white; padding:30px; border-radius:32px 32px 0 0; position:relative;">
                <button onclick="document.getElementById('orderDetailModal').style.display='none'" 
                        style="position:absolute; right:24px; top:50%; transform:translateY(-50%); border:none; background:rgba(255,255,255,0.2); color:white; width:42px; height:42px; border-radius:50%; cursor:pointer; font-size:1.2rem; transition:all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); display:flex; align-items:center; justify-content:center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);" 
                        onmouseover="this.style.background='rgba(255,255,255,0.35)'; this.style.transform='translateY(-50%) scale(1.1)'" 
                        onmouseout="this.style.background='rgba(255,255,255,0.2)'; this.style.transform='translateY(-50%) scale(1)'"
                        onmousedown="this.style.transform='translateY(-50%) scale(0.9)'">✕</button>
                <div style="font-size:0.75rem; font-weight:700; text-transform:uppercase; letter-spacing:1px; opacity:0.8; margin-bottom:6px;">Detail Pesanan</div>
                <div id="od-number" style="font-size:1.5rem; font-weight:800;">ORD-XXXX</div>
                <div id="od-date" style="font-size:0.85rem; opacity:0.75; margin-top:4px;"></div>
            </div>

            <div style="padding:28px 30px;">
                <!-- Status Timeline -->
                <div id="od-timeline" style="display:flex; justify-content:space-between; position:relative; margin-bottom:28px; padding:0 5px;"></div>

                <!-- Customer Info -->
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:22px;">
                    <div style="background:#f8fafc; border-radius:14px; padding:18px;">
                        <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#94a3b8; letter-spacing:1px; margin-bottom:8px;">Pelanggan</div>
                        <div id="od-customer" style="font-weight:700; font-size:0.95rem; color:#1e293b;"></div>
                    </div>
                    <div style="background:#f8fafc; border-radius:14px; padding:18px;">
                        <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#94a3b8; letter-spacing:1px; margin-bottom:8px;">Metode & Pengiriman</div>
                        <div id="od-payment" style="font-weight:700; font-size:0.9rem; color:#1e293b;"></div>
                        <div id="od-shipping-method" style="font-size:0.8rem; color:#64748b; margin-top:3px;"></div>
                    </div>
                </div>

                <!-- Address -->
                <div style="background:#eff6ff; border:1px solid #bfdbfe; border-radius:14px; padding:16px 20px; margin-bottom:22px; display:flex; gap:12px; align-items:flex-start;">
                    <div style="color:#3b82f6; flex-shrink:0; margin-top:2px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <div>
                        <div id="od-addr-label" style="font-weight:700; font-size:0.9rem; color:#1d4ed8; margin-bottom:4px;"></div>
                        <div id="od-address" style="font-size:0.9rem; color:#374151; line-height:1.5;"></div>
                    </div>
                </div>

                <!-- Items List -->
                <div style="font-size:0.7rem; font-weight:800; text-transform:uppercase; color:#94a3b8; letter-spacing:1px; margin-bottom:12px;">Daftar Produk</div>
                <div id="od-items" style="display:flex; flex-direction:column; gap:10px; margin-bottom:20px;"></div>

                <!-- Price Summary -->
                <div style="background:#f8fafc; border-radius:14px; padding:18px;">
                    <div style="display:flex; justify-content:space-between; margin-bottom:10px; font-size:0.9rem; color:#64748b;"><span>Subtotal Produk</span><span id="od-subtotal" style="font-weight:600; color:#1e293b;"></span></div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:14px; font-size:0.9rem; color:#64748b;"><span>Ongkos Kirim</span><span id="od-ship-cost" style="font-weight:600; color:#1e293b;"></span></div>
                    <div style="display:flex; justify-content:space-between; padding-top:14px; border-top:2px solid #e2e8f0; font-size:1.1rem; font-weight:800; color:var(--primary-blue);"><span>Total Bayar</span><span id="od-grand-total"></span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- PAYMENT MODAL -->
    <div id="paymentModal" style="display:none; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:99999; align-items:flex-start; justify-content:center; overflow-y:auto; padding: 40px 20px;">
        <div style="background:white; border-radius:24px; width:100%; max-width:800px; position:relative; animation: slideUp 0.3s ease-out;">
            <button onclick="toggleModal('paymentModal')" style="position: absolute; right: 20px; top: 20px; border: none; background: #f0f0f0; width: 32px; height: 32px; border-radius: 50%; cursor: pointer;">✕</button>
            <div style="padding: 30px;">
                <h3 style="margin-bottom:25px; font-size: 1.4rem;">Konfirmasi Pembayaran</h3>
                
                <form id="paymentForm" method="POST">
                    @csrf
                    <div class="pay-modal-grid">
                        <div class="left-col">
                            <div class="pay-section">
                                <div class="pay-title">Info Pesanan</div>
                                <div class="pay-row"><span>No Pesanan</span><strong id="pay-order-number">ORD-XXX</strong></div>
                                <div id="pres-warning-box" style="display:none; margin-top:15px; background:#FFF5F5; border:1px solid #FFC9C9; color:#C92A2A; padding:12px; border-radius:10px; font-size:0.8rem;">
                                    ⚠️ <strong>Resep Diperlukan:</strong> Pesanan ini mengandung obat keras dan resep Anda belum terverifikasi. Pembayaran diblokir.
                                </div>
                                <div id="pres-success-box" style="display:none; margin-top:15px; background:#E6FCF5; border:1px solid #C3FAE8; color:#087F5B; padding:12px; border-radius:10px; font-size:0.8rem;">
                                    ✅ <strong>Resep Terverifikasi:</strong> Resep manual terlampir atau akun sudah diapprove Dokter.
                                </div>
                            </div>

                            <div class="pay-section">
                                <div class="pay-title">Pilih Durasi Pengiriman</div>
                                <label><input type="radio" name="shipping_method" value="instant" checked onchange="calcTotal()">
                                    <div class="pay-method-card"><strong>Instant Delivery</strong><br><span style="font-size:0.8rem; color:#666;">2 Jam Sampai (Rp 15.000)</span></div>
                                </label>
                                <label><input type="radio" name="shipping_method" value="regular" onchange="calcTotal()">
                                    <div class="pay-method-card"><strong>JNE / J&T Reguler</strong><br><span style="font-size:0.8rem; color:#666;">2-3 Hari Kerja (Rp 10.000)</span></div>
                                </label>
                            </div>
                        </div>

                        <div class="right-col">
                            <div class="pay-section" style="background:#f8f9fa;">
                                <div class="pay-title">Metode Pembayaran</div>
                                <div style="display: grid; grid-template-columns: 1fr; gap: 10px;">
                                    <label><input type="radio" name="payment_method" value="qris" checked>
                                        <div class="pay-method-card"><strong>QRIS</strong></div>
                                    </label>
                                    <label><input type="radio" name="payment_method" value="wallet">
                                        <div class="pay-method-card" style="border-color: #059669; background: #ecfdf5;">
                                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                                <strong style="color: #059669;"><i class="fas fa-wallet"></i> Saldo Dompet</strong>
                                                <span style="font-size: 0.75rem; color: #065f46; font-weight: 800;">Rp {{ number_format($user->wallet_balance, 0, ',', '.') }}</span>
                                            </div>
                                        </div>
                                    </label>
                                    <label><input type="radio" name="payment_method" value="paylater">
                                        <div class="pay-method-card"><strong>Paylater</strong></div>
                                    </label>
                                </div>

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

    <!-- SUCCESS MODAL (POST-PAYMENT) -->
    @if($justPaidOrder)
    <div id="successModal" style="display:flex; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:9999999; align-items:center; justify-content:center; backdrop-filter:blur(10px);">
        <div style="background:white; border-radius:32px; width:100%; max-width:480px; text-align:center; padding:50px 40px; position:relative; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); animation: zoomIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);">
            <button onclick="document.getElementById('successModal').style.display='none'" 
                    style="position:absolute; right:20px; top:20px; border:none; background:#f1f5f9; color:#64748b; width:42px; height:42px; border-radius:50%; cursor:pointer; font-weight:800; display:flex; align-items:center; justify-content:center; font-size:1.1rem; transition:all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);"
                    onmouseover="this.style.background='#e2e8f0'; this.style.transform='scale(1.1)'"
                    onmouseout="this.style.background='#f1f5f9'; this.style.transform='scale(1)'"
                    onmousedown="this.style.transform='scale(0.9)'">✕</button>
            
            <!-- Success Animation (CSS only) -->
            <div style="width:100px; height:100px; background:#f0fdf4; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 30px; border:4px solid #fff; box-shadow:0 0 0 4px #f0fdf4;">
                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="animation: checkmark 0.6s ease-in-out forwards;"><polyline points="20 6 9 17 4 12"/></svg>
            </div>

            @if($justPaidOrder->payment_status === 'paid')
                <h3 style="font-size:1.8rem; font-weight:800; color:#0f172a; margin-bottom:12px;">Yeay! Pembayaran Berhasil</h3>
                <p style="color:#64748b; font-size:1rem; line-height:1.6; margin-bottom:30px;">Pesanan kamu sedang kami siapkan. Terima kasih telah mempercayai Pharmacare.</p>
            @else
                <h3 style="font-size:1.8rem; font-weight:800; color:#0f172a; margin-bottom:12px;">Pesanan Berhasil Dibuat!</h3>
                <p style="color:#64748b; font-size:1rem; line-height:1.6; margin-bottom:30px;">Silakan selesaikan pembayaran agar pesanan kamu bisa segera kami proses.</p>
            @endif

            <div style="background:#f8fafc; border-radius:24px; padding:25px; margin-bottom:30px; text-align:left;">
                <div style="display:flex; justify-content:space-between; margin-bottom:15px; border-bottom:1px dashed #e2e8f0; padding-bottom:15px;">
                    <span style="font-size:0.85rem; color:#64748b; font-weight:600;">No Pesanan</span>
                    <span style="font-size:0.85rem; color:#0f172a; font-weight:800;">#{{ $justPaidOrder->order_number }}</span>
                </div>

                @if($justPaidOrder->payment_method === 'bank')
                <div style="margin-bottom:20px; background:white; border:1px solid #e2e8f0; border-radius:16px; padding:15px;">
                    <div style="font-size:0.75rem; color:#64748b; margin-bottom:10px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">Transfer Pembayaran (BCA)</div>
                    <div style="display:flex; align-items:center; gap:12px;">
                        <img src="https://upload.wikimedia.org/wikipedia/id/thumb/5/55/BNI_logo.svg/1200px-BNI_logo.svg.png" style="height:20px; filter: grayscale(1);" alt="Bank">
                        <div style="text-align:left;">
                            <div style="font-size:1.1rem; color:#0f172a; font-weight:800; letter-spacing:1px;">8832 1922 33</div>
                            <div style="font-size:0.75rem; color:#64748b; font-weight:600;">a/n PT Pharmacare Indonesia</div>
                        </div>
                    </div>
                </div>
                @endif

                <div style="display:flex; justify-content:space-between; margin-bottom:15px;">
                    <span style="font-size:0.85rem; color:#64748b; font-weight:600;">Total Tagihan</span>
                    <span style="font-size:0.95rem; color:#0076D6; font-weight:800;">Rp {{ number_format($justPaidOrder->grand_total, 0, ',', '.') }}</span>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="font-size:0.85rem; color:#64748b; font-weight:600;">Metode Pembayaran</span>
                    <span style="font-size:0.85rem; color:#0f172a; font-weight:800; text-transform:uppercase;">{{ $justPaidOrder->payment_method }}</span>
                </div>
            </div>

            <div style="display:flex; flex-direction:column; gap:12px;">
                <button onclick="document.getElementById('successModal').style.display='none'; openOrderDetail({{ $justPaidOrder->id }})" style="width:100%; padding:16px; background:#0076D6; color:white; border:none; border-radius:16px; font-weight:800; font-size:1rem; cursor:pointer; transition:0.2s; box-shadow:0 10px 15px -3px rgba(0,118,214,0.3);" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">Lihat Detail Pesanan</button>
                <button onclick="document.getElementById('successModal').style.display='none'" style="width:100%; padding:14px; background:white; color:#64748b; border:2px solid #e2e8f0; border-radius:16px; font-weight:700; font-size:0.95rem; cursor:pointer;">{{ $justPaidOrder->payment_status === 'paid' ? 'Tutup' : 'Selesai' }}</button>
            </div>
        </div>
    </div>
    <script>
    (function() {
        var url = new URL(window.location.href);
        if (url.searchParams.has('just_paid')) {
            url.searchParams.delete('just_paid');
            history.replaceState(null, '', url.pathname + (url.search || ''));
        }
    })();
    </script>
    @endif

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

        function openOrderDetail(orderId) {
            const data = orderDataMap[orderId];
            if (!data) return;
            const fmt = n => 'Rp ' + new Intl.NumberFormat('id-ID').format(n);
            const statuses = {
                'ordered':    { label: 'Dipesan',  color: '#0076D6', icon: '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 01-8 0"/></svg>' },
                'paid':       { label: 'Dibayar',  color: '#0076D6', icon: '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>' },
                'processing': { label: 'Dikirim',  color: '#0076D6', icon: '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="1" y="3" width="15" height="13" rx="1"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>' },
                'completed':  { label: 'Selesai',  color: '#0076D6', icon: '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>' },
            };
            const statusOrder = ['ordered','paid','processing','completed'];
            const currentIdx = statusOrder.indexOf(data.status);

            // Inject header
            document.getElementById('od-number').innerText = '#' + data.number;
            document.getElementById('od-date').innerText = data.date;

            // Build Timeline with timestamps
            const timeline = document.getElementById('od-timeline');
            
            if (data.status === 'cancelled' || data.status === 'rejected') {
                timeline.innerHTML = `
                    <div style="padding: 15px; background: #FFF5F5; border-radius: 12px; border: 1px dashed #ffa8a8; text-align: center; width: 100%;">
                        <div style="font-weight: 800; color: #C92A2A; display: inline-flex; align-items: center; gap: 8px;">
                            <i class="fas fa-times-circle"></i> Pesanan Dibatalkan
                        </div>
                    </div>`;
            } else {
                const timestamps = {
                    'ordered':    data.created_at,
                    'paid':       data.status !== 'ordered' ? data.updated_at : null,
                    'processing': (data.status === 'processing' || data.status === 'completed') ? data.updated_at : null,
                    'completed':  data.status === 'completed' ? data.updated_at : null,
                };
                timeline.innerHTML = `
                    <div style="position:absolute; top:21px; left:10%; right:10%; height:3px; background:#e2e8f0; border-radius:99px;"></div>
                    <div style="position:absolute; top:21px; left:10%; height:3px; width:calc(${currentIdx} * (80% / 3)); background:#0076D6; border-radius:99px; transition:width 0.5s ease;"></div>
                    ${statusOrder.map((s, i) => {
                        const done = i <= currentIdx;
                        const act = i === currentIdx;
                        const st = statuses[s];
                        const ts = timestamps[s];
                        return `<div style="display:flex; flex-direction:column; align-items:center; gap:5px; z-index:2; flex:1;">
                            <div style="width:44px; height:44px; border-radius:50%; display:flex; align-items:center; justify-content:center; background:${done ? '#0076D6' : 'white'}; border:2px solid ${done ? '#0076D6' : '#e2e8f0'}; color:${done ? 'white' : '#cbd5e1'}; transition:0.4s; box-shadow:${act ? '0 0 0 5px rgba(0,118,214,0.15)' : 'none'};">${st.icon}</div>
                            <span style="font-size:0.68rem; font-weight:800; color:${done ? '#0076D6' : '#94a3b8'}; text-transform:uppercase; letter-spacing:0.5px;">${st.label}</span>
                            ${done && ts ? `<span style="font-size:0.6rem; color:#94a3b8; font-weight:500; text-align:center; line-height:1.3;">${ts}</span>` : '<span style="font-size:0.6rem; color:transparent;">-</span>'}
                        </div>`;
                    }).join('')}
                `;
            }

            // Customer & Payment
            document.getElementById('od-customer').innerText = data.customer;
            document.getElementById('od-payment').innerText = data.payment_method;
            document.getElementById('od-shipping-method').innerText = data.shipping_method;
            document.getElementById('od-addr-label').innerText = data.address_label;
            document.getElementById('od-address').innerText = data.address;

            // Items
            const itemsContainer = document.getElementById('od-items');
            itemsContainer.innerHTML = data.items.map(item => `
                <div style="display:flex; align-items:center; gap:14px; padding:14px 16px; background:#fff; border:1px solid #f0f0f0; border-radius:12px;">
                    <div style="width:50px; height:50px; background:#f1f5f9; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; overflow:hidden;">
                        ${item.image ? `<img src="/${item.image}" style="width:100%; height:100%; object-fit:contain;">` : '<span style="font-size:1.4rem;">💊</span>'}
                    </div>
                    <div style="flex:1; min-width:0;">
                        <div style="font-weight:700; font-size:0.9rem; color:#1e293b; margin-bottom:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${item.name}</div>
                        <div style="font-size:0.82rem; color:#64748b;">${item.qty} x ${fmt(item.price)}</div>
                    </div>
                    <div style="text-align:right; flex-shrink:0;">
                        <div style="font-weight:800; color:#0f172a; font-size:0.95rem;">${fmt(item.subtotal)}</div>
                    </div>
                </div>
            `).join('');

            // Totals
            document.getElementById('od-subtotal').innerText = fmt(data.sub_total);
            document.getElementById('od-ship-cost').innerText = fmt(data.shipping_cost);
            document.getElementById('od-grand-total').innerText = fmt(data.grand_total);

            document.getElementById('orderDetailModal').style.display = 'flex';
        }

        // Close wellness modal on backdrop click
        if (document.getElementById('wellnessModal')) {
            document.getElementById('wellnessModal').addEventListener('click', function(e) {
                if (e.target === this) this.style.display = 'none';
            });
        }

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

        window.openArticleModal = function(article) {
            if (!article) return;
            document.getElementById('wm-title').textContent = article.title || '';
            document.getElementById('wm-content').textContent = article.content || '';
            document.getElementById('wm-img').src = '/' + (article.image_path || '');
            document.getElementById('wellnessModal').style.display = 'flex';
        };

        window.closeArticleModal = function() {
            document.getElementById('wellnessModal').style.display = 'none';
        };
    </script>

    <!-- WELLNESS MODAL HTML -->
    <div id="wellnessModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; z-index:999999; background:rgba(15,23,42,0.8); backdrop-filter:blur(8px); align-items:center; justify-content:center; padding:20px;" onclick="if(event.target===this) window.closeArticleModal()">
        <div style="background:white; border-radius:30px; width:100%; max-width:600px; overflow:hidden; position:relative; box-shadow: 0 25px 50px rgba(0,0,0,0.3); animation: slideUp 0.4s cubic-bezier(0.23,1,0.32,1);">
            <button onclick="window.closeArticleModal()" style="position:absolute; right:20px; top:20px; border:none; background:white; color:#1e293b; width:36px; height:36px; border-radius:50%; cursor:pointer; font-size:1.1rem; z-index:10; display:flex; align-items:center; justify-content:center; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">✕</button>
            <div style="height:250px; overflow:hidden;">
                <img id="wm-img" src="" style="width:100%; height:100%; object-fit:cover;">
            </div>
            <div style="padding:40px;">
                <div style="font-size:0.75rem; font-weight:800; color:#0076D6; text-transform:uppercase; letter-spacing:1.5px; margin-bottom:12px;">Tips & Insight Kesehatan</div>
                <h2 id="wm-title" style="font-size:1.8rem; font-weight:800; color:#1e293b; margin-bottom:20px; line-height:1.2;"></h2>
                <p id="wm-content" style="font-size:1.1rem; color:#475569; line-height:1.7; white-space:pre-wrap;"></p>
                <div style="margin-top:30px; padding-top:20px; border-top:1px solid #f1f5f9;">
                    <button onclick="window.closeArticleModal()" style="width:100%; padding:14px; background:#0076D6; color:white; border:none; border-radius:15px; font-weight:700; cursor:pointer;">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        @keyframes slideUp { from { transform: translateY(20px); opacity:0; } to { transform: translateY(0); opacity:1; } }
        @keyframes zoomIn { from { transform: scale(0.95); opacity:0; } to { transform: scale(1); opacity:1; } }
        @keyframes checkmark { from { stroke-dasharray: 100; stroke-dashoffset: 100; } to { stroke-dasharray: 100; stroke-dashoffset: 0; } }
    </style>
</body>
</html>
