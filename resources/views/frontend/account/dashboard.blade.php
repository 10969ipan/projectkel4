<!DOCTYPE html>
<html lang="id">
<!-- deployment-trigger: rollback-sync -->
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
        .badge-pending { background: #FFF9DB; color: #856404; }
        .badge-paid { background: #E7F5FF; color: #1971C2; }
        .badge-shipped { background: #F3F0FF; color: #6741D9; }
        .badge-delivered { background: #E6FCF5; color: #087f5b; }
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

        /* Dashboard-style Payment Modal */
        #orderDetailModal, #addressModal, #paymentModal { 
            display:none; 
            position: fixed; 
            top:0; left:0; width:100%; height:100%; 
            background:rgba(15,23,42,0.6); 
            backdrop-filter: blur(12px);
            z-index:9999; 
            align-items: center; 
            justify-content: center; 
            overflow-y:auto; 
            padding: 50px 20px; 
            /* Hide scrollbar for overlay */
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        #orderDetailModal::-webkit-scrollbar, 
        #addressModal::-webkit-scrollbar, 
        #paymentModal::-webkit-scrollbar { display: none; }

        #orderDetailModal > div, #addressModal > div, #paymentModal > div { 
            background:white; 
            border-radius:28px; 
            width:100%; 
            position:relative; 
            animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 30px 60px rgba(0,0,0,0.25);
            margin: 0 auto; /* To ensure it stays centered if flex fails */
            /* Hide scrollbar for content */
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        #orderDetailModal > div::-webkit-scrollbar, 
        #addressModal > div::-webkit-scrollbar, 
        #paymentModal > div::-webkit-scrollbar { display: none; }

        #paymentModal > div { max-width: 450px; }
        #addressModal > div { max-width: 500px; }
        #orderDetailModal > div { max-width: 680px; }
        
        .pay-modal-grid { display: flex; flex-direction: column; gap: 15px; }
        .pay-section { padding: 12px 15px; border-radius: 16px; background: #ffffff; border: 1.5px solid #f8fafc; box-shadow: 0 4px 12px rgba(0,0,0,0.02); margin-bottom: 0; text-align: left; }
        .pay-title { font-weight: 800; font-size: 0.85rem; margin-bottom: 12px; color: #0076D6; }
        .pay-row { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.8rem; color: #475569; }
        
        .pay-method-card { 
            border: 1.5px solid #e2e8f0; 
            border-radius: 12px; 
            padding: 12px 15px; 
            cursor: pointer; 
            transition: all 0.3s ease; 
            margin-bottom: 8px; 
            display: flex; 
            flex-direction: column; 
            justify-content: center;
            background: #fff;
        }
        .pay-method-card strong { font-size: 0.85rem; }
        .pay-method-card div { font-size: 0.6rem !important; }
        .pay-method-card:hover { border-color: #0076D6; background: #f0f7ff; }
        input[type="radio"]:checked + .pay-method-card { 
            border-color: #0076D6; 
            background: #f0f7ff; 
            box-shadow: 0 4px 12px rgba(0,118,214,0.1); 
            border-width: 2px;
        }
        input[type="radio"] { display: none; }
        
        /* Ensure SweetAlert appears above modals */
        .swal2-container { z-index: 1000000 !important; }

        /* Skeleton Animation */
        @keyframes shimmer {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }
        
        .skeleton {
            background: linear-gradient(90deg, #f3f4f6 25%, #e5e7eb 50%, #f3f4f6 75%);
            background-size: 1000px 100%;
            animation: shimmer 2s infinite linear;
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .container { margin: 20px auto; padding: 0 15px; }
            .header { flex-direction: row-reverse; justify-content: space-between; align-items: center; margin-bottom: 25px; gap: 0; }
            .header h1 { font-size: 1.4rem; text-align: right; }
            .back-link { font-size: 0.9rem; }
            .dashboard-grid { gap: 20px; }
            .sidebar { padding: 20px 15px; }
            .content-card { padding: 20px; border-radius: 16px; }
            
            /* Order List Mobile */
            #section-orders h2 { font-size: 1.3rem; margin-bottom: 20px; }
            .content-section > div { gap: 15px !important; }
            [id^="section-orders"] .content-section > div > div { padding: 15px !important; border-radius: 12px !important; }
            
            .order-header-flex { flex-direction: column; align-items: flex-start !important; gap: 10px; }
            .order-header-flex > div:last-child { text-align: left !important; width: 100%; padding-top: 10px; border-top: 1px solid #f1f5f9; }
            
            /* Progress Tracker Mobile */
            .progress-tracker { padding: 0 !important; margin-bottom: 20px !important; }
            .progress-tracker span { font-size: 0.6rem !important; }
            .progress-tracker-circle { width: 32px !important; height: 32px !important; }
            .progress-tracker-line { top: 16px !important; }
            
            .order-action-bar { padding: 12px !important; border-radius: 12px !important; flex-direction: column !important; align-items: stretch !important; gap: 10px !important; }
            .order-action-bar button, .order-action-bar a { width: 100% !important; text-align: center; justify-content: center; }
            
            /* Subscriptions Mobile */
            [id^="section-subscriptions"] > div > div { flex-direction: column; align-items: flex-start !important; gap: 15px !important; }
            [id^="section-subscriptions"] .sub-image { width: 60px !important; height: 60px !important; }
            
            /* Addresses Mobile */
            [id^="section-addresses"] > div:last-child { grid-template-columns: 1fr !important; }
            
            /* Modals Mobile */
            #orderDetailModal, #addressModal, #paymentModal { padding: 20px 15px !important; align-items: center !important; justify-content: center !important; }
            #orderDetailModal > div, #addressModal > div, #paymentModal > div { border-radius: 24px !important; max-width: 100% !important; width: 100% !important; max-height: 85vh !important; }
            
            /* Wellness Banner Mobile */
            .wellness-highlights { height: auto !important; padding: 25px 20px !important; border-radius: 20px !important; }
            .wellness-highlights h3 { font-size: 1.3rem !important; margin-bottom: 10px !important; }
            .wellness-highlights p { font-size: 0.85rem !important; margin-bottom: 15px !important; }
            .wellness-highlights a { font-size: 0.8rem !important; }

            /* Wellness Modal Mobile */
            #wellnessModal { padding: 20px 15px !important; }
            #wellnessModal > div { max-height: 85vh; border-radius: 24px !important; display: flex; flex-direction: column; }
            #wellnessModal > div > div:nth-child(2) { height: 160px !important; flex-shrink: 0; } /* Image container */
            #wellnessModal > div > div:nth-child(3) { padding: 25px 20px !important; overflow-y: auto; } /* Text container */
            #wellnessModal h2 { font-size: 1.3rem !important; margin-bottom: 15px !important; }
            #wellnessModal p { font-size: 0.9rem !important; }
        }

        [x-cloak] { display: none !important; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.0/dist/cdn.min.js"></script>
    <!-- Midtrans Snap JS -->
    @if(config('services.midtrans.is_production'))
        <script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    @else
        <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
    @endif
</head>
<body x-data="{ pageLoading: true }" x-init="window.onload = () => { setTimeout(() => { pageLoading = false }, 500) }">

<div class="container">
    <div class="header">
        <h1>Pesanan Anda</h1>
        <a href="{{ route('store.index') }}" class="back-link">← Kembali ke Toko</a>
    </div>

    <div class="dashboard-grid">
        <!-- Sidebar Nav -->
        <div class="sidebar">
            <div style="text-align: center; margin-bottom: 30px;">
                @if($user->avatar)
                    <img src="{{ $user->avatar }}"
                         alt="{{ $user->name }}"
                         style="width: 70px; height: 70px; border-radius: 50%; object-fit: cover; border: 3px solid var(--primary-blue); margin: 0 auto 15px; display: block; box-shadow: 0 4px 15px rgba(0,118,214,0.2);">
                @else
                    <div style="width: 70px; height: 70px; background: var(--primary-blue); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem; font-weight: 800; margin: 0 auto 15px;">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
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

            <div class="wellness-highlights" style="position: relative; overflow: hidden; border-radius: 24px; box-shadow: 0 15px 35px rgba(0,118,214,0.1); height: 280px; background: #000; display: flex; flex-direction: column; justify-content: center;">
                <img src="{{ asset($article->image_path) }}" 
                     alt="{{ $article->title }}"
                     loading="lazy"
                     width="800" height="280"
                     style="position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; z-index: 1;">
                
                <div style="position: absolute; inset: 0; background: linear-gradient(to right, rgba(0,0,0,0.85) 10%, rgba(0,0,0,0.4) 50%, transparent 100%); z-index: 2;"></div>

                <div style="max-width: 450px; color: white; position: relative; z-index: 10; padding: 40px;">
                    <div style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 10px; color: #93c5fd;">Terkait Pesanan Anda</div>
                    <h3 style="font-size: 1.8rem; font-weight: 800; margin-top: 0; margin-bottom: 15px; line-height: 1.2;">{{ $article->title }}</h3>
                    <p style="font-size: 1rem; opacity: 0.9; line-height: 1.6; margin-bottom: 20px;">{{ Str::limit($article->content, 120) }}</p>
                    <a href="#wellness" onclick="window.openArticleModal(window._dashboardArticle)" style="display: inline-flex; align-items: center; gap: 8px; color: white; text-decoration: none; font-weight: 700; font-size: 0.9rem; border-bottom: 2px solid var(--primary-blue); cursor: pointer; position: relative;">Baca Selengkapnya <i class="fas fa-arrow-right" style="font-size: 0.7rem;"></i></a>
                </div>
            </div>

            @endif


            <div class="content-card" style="min-height: 600px; position: relative;">
                
                <!-- Skeleton Loader -->
                <div x-show="pageLoading" class="space-y-6">
                    <div class="h-8 w-48 skeleton mb-8"></div>
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        <template x-for="i in 3">
                            <div style="border: 1px solid #eee; border-radius: 16px; padding: 25px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                                    <div class="space-y-2">
                                        <div class="h-4 w-32 skeleton"></div>
                                        <div class="h-6 w-48 skeleton"></div>
                                    </div>
                                    <div class="text-right space-y-2">
                                        <div class="h-4 w-20 skeleton ml-auto"></div>
                                        <div class="h-6 w-32 skeleton ml-auto"></div>
                                    </div>
                                </div>
                                <div class="h-12 w-full skeleton"></div>
                            </div>
                        </template>
                    </div>
                </div>

                <div x-show="!pageLoading" x-cloak>
            
            <!-- SECTION: ORDERS -->
            <div id="section-orders" class="content-section">
                <h2>Pesanan Saya</h2>
                @if($orders->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 20px;">
                        @foreach($orders as $order)
                        <div style="border: 1px solid #eee; border-radius: 16px; padding: 25px; transition: all 0.3s;">
                            <div class="order-header-flex" style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
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
                                <div class="progress-tracker" style="display: flex; justify-content: space-between; margin-bottom: 25px; position: relative; padding: 0 10px;">
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
                                    <div class="progress-tracker-line" style="position: absolute; top: 20px; left: 10%; right: 10%; height: 3px; background: #eee; z-index: 1; border-radius:99px;"></div>
                                    <div class="progress-tracker-line" style="position: absolute; top: 20px; left: 10%; height: 3px; width: calc({{ $currentIdx }} * (80% / 3)); background: var(--primary-blue); z-index: 1; border-radius:99px; transition: width 0.5s ease;"></div>

                                    @foreach($steps as $idx => $step)
                                    @php $done = $idx <= $currentIdx; $active = $idx == $currentIdx; @endphp
                                    <div style="display: flex; flex-direction: column; align-items: center; gap: 8px; z-index: 2; flex: 1;">
                                        <div class="progress-tracker-circle" style="width: 42px; height: 42px; background: {{ $done ? 'var(--primary-blue)' : 'white' }}; border: 2px solid {{ $done ? 'var(--primary-blue)' : '#e2e8f0' }}; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: {{ $done ? 'white' : '#cbd5e1' }}; transition: all 0.4s; box-shadow: {{ $active ? '0 0 0 4px rgba(0,118,214,0.15)' : 'none' }};">
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
                            @endphp
                            <div class="order-action-bar" style="display: flex; justify-content: space-between; align-items: center; background: #fafafa; padding: 15px 20px; border-radius: 12px; gap: 10px; flex-wrap: wrap;">
                                <div style="font-size: 0.9rem; color: var(--text-muted);">
                                    Dikirim ke: <strong>{{ $order->address->label ?? 'Alamat Default' }}</strong>
                                </div>
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    @if($order->order_status === 'ordered' && $order->payment_status === 'pending')
                                        <button onclick="openPaymentModal({
                                            id: {{ $order->id }},
                                            number: '{{ $order->order_number }}',
                                            subtotal: {{ $order->sub_total }},
                                            shipping_method: '{{ $order->shipping_method }}',
                                            shipping_cost: {{ $order->shipping_cost ?? 0 }},
                                            reqPres: {{ $reqPres ? 'true' : 'false' }},
                                            hasPres: {{ $hasPrescription ? 'true' : 'false' }},
                                            url: '{{ route('account.orders.pay.post', $order->id) }}'
                                        })" style="background: #2F9E44; color: white; padding: 8px 18px; border-radius: 8px; font-size: 0.85rem; font-weight: 700; border:none; cursor:pointer;">Bayar Sekarang</button>
                                        
                                        @if($order->payment_method === 'midtrans')
                                            <button onclick="checkMidtransStatus({{ $order->id }}, this)" 
                                                    style="background: #E7F5FF; color: #1971C2; border: 1px solid #1971C2; padding: 8px 14px; border-radius: 8px; font-size: 0.85rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                                                <i class="fas fa-sync-alt"></i> Cek Status
                                            </button>
                                        @endif
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
                                    <img src="{{ asset($sub->item->image_path) }}" width="80" height="80" style="width: 100%; height: 100%; object-fit: contain;" loading="lazy">
                                @else
                                    <span style="font-size: 2rem;">💊</span>
                                @endif
                            </div>
                            <div class="sub-details" style="flex: 1; width: 100%;">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px;">
                                    <div style="flex: 1;">
                                        <h4 style="font-size: 1.1rem; color: #1e293b; margin-bottom: 4px; line-height: 1.3;">{{ $sub->item->name }}</h4>
                                        <div style="color: #64748b; font-size: 0.85rem;">Interval: <strong>Setiap {{ $sub->interval_days }} Hari</strong></div>
                                    </div>
                                    <div style="text-align: right; flex-shrink: 0;">
                                        <div style="font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; font-weight: 800; margin-bottom: 3px;">Status</div>
                                        <span class="badge badge-delivered" style="background: #ecfdf5; color: #059669; padding: 4px 10px;">{{ strtoupper($sub->status) }}</span>
                                    </div>
                                </div>
                                <div class="sub-footer" style="margin-top: 15px; padding-top: 15px; border-top: 1px dashed #e2e8f0; display: flex; justify-content: space-between; align-items: center; gap: 10px;">
                                    <div style="font-size: 0.85rem; color: #475569; line-height: 1.4;">
                                        <span style="display: block; font-size: 0.75rem; color: #94a3b8; margin-bottom: 2px;"><i class="far fa-calendar-alt"></i> Pengiriman Berikutnya:</span>
                                        <strong>{{ \Carbon\Carbon::parse($sub->next_delivery_date)->format('d M Y') }}</strong>
                                    </div>
                                    <div style="color: #059669; font-weight: 800; font-size: 0.9rem; text-align: right; background: #f0fdf4; padding: 6px 12px; border-radius: 8px; border: 1px solid #bbf7d0;">
                                        Hemat 10%
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
            <div id="addressModal" style="display:none; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999;">
                <div style="background:white; padding:40px; border-radius:20px; width:100%; max-width:500px;">
                    <h3 style="margin-bottom:20px;">Tambah Alamat Baru</h3>
                    <form action="{{ route('account.address.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="form-label">Label Alamat (Contoh: Rumah / Kantor)</label>
                            <input type="text" name="label" class="form-control" placeholder="Masukan label alamat" required>
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
</div>
</div>

<!-- SweetAlert2 -->
<script src="{{ asset('sweetalert/sweetalert2.all.min.js') }}" defer></script>
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

    let Toast;
    document.addEventListener('DOMContentLoaded', function() {
        Toast = Swal.mixin({
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
    });
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
                'shipping_method' => $order->shipping_method,
                'shipping_cost'   => $order->shipping_cost ?? 0,
                'sub_total'       => $order->sub_total,
                'grand_total'     => $order->grand_total,
                'tracking_number' => $order->tracking_number,
                'city_id'         => $order->address->city_id ?? null,
                'items'           => $mapItems,
                'status'          => $order->order_status,
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
            'shipping_method' => $justPaidOrder->shipping_method,
            'shipping_cost'   => $justPaidOrder->shipping_cost ?? 0,
            'sub_total'       => $justPaidOrder->sub_total,
            'grand_total'     => $justPaidOrder->grand_total,
            'tracking_number' => $justPaidOrder->tracking_number,
            'city_id'         => $justPaidOrder->address->city_id ?? null,
            'items'           => collect($justPaidOrder->items)->map(fn($oi) => [
                'name'     => $oi->item->name ?? 'Item Dihapus',
                'qty'      => $oi->quantity,
                'price'    => $oi->price,
                'subtotal' => $oi->sub_total,
                'image'    => $oi->item->image_path ?? null,
            ])->toArray(),
            'status'          => $justPaidOrder->order_status,
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!},
        @endif
    };
</script>

    <!-- ORDER DETAIL MODAL -->
    <div id="orderDetailModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(15,23,42,0.7); z-index:99998; overflow-y:auto; backdrop-filter:blur(4px);">
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
                        <div id="od-tracking-number" style="font-size:0.75rem; color:#059669; font-weight:700; margin-top:5px; display:none;"></div>
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

                <!-- Action Button: Invoice -->
                <div style="margin-top: 20px;">
                    <a id="od-invoice-btn" href="#" target="_blank" style="display: block; text-align: center; padding: 14px; background: #f1f5f9; color: #1e293b; border-radius: 12px; font-weight: 700; text-decoration: none; transition: 0.2s;" onmouseover="this.style.background='#e2e8f0'" onmouseout="this.style.background='#f1f5f9'">
                        <i class="fas fa-file-invoice mr-1"></i> Lihat & Cetak Invoice
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- PAYMENT MODAL -->
    <div id="paymentModal">
        <div>
            <div class="modal-header" style="padding: 15px 20px 10px; display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 1.15rem; font-weight: 800; color: #1e293b;">Konfirmasi Pembayaran</h3>
                <button onclick="toggleModal('paymentModal')" style="border: none; background: #f1f5f9; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; color: #64748b; font-weight: bold; display: flex; align-items: center; justify-content: center; transition: 0.2s;">✕</button>
            </div>
            
            <div class="modal-body" style="padding: 0 20px 20px;">
                <form id="paymentForm" method="POST">
                    @csrf
                    <input type="hidden" name="shipping_cost" id="input-shipping-cost" value="15000">
                    <div class="pay-modal-grid">
                        <div class="pay-section" style="padding: 12px 15px;">
                            <div class="pay-title">Info Pesanan</div>
                            <div class="pay-row"><span>No Pesanan</span><strong id="pay-order-number" style="color: #1e293b;">ORD-XXX</strong></div>
                            <div id="pres-warning-box" style="display:none; margin-top:10px; background:#fff1f2; border:1px solid #ef4444; color:#991b1b; padding:10px 15px; border-radius:12px; font-size:0.75rem;">
                                ⚠️ <strong>Resep Diperlukan:</strong> Pesanan ini mengandung obat keras dan resep belum terverifikasi.
                            </div>
                            <div id="pres-success-box" style="display:none; margin-top:10px; background:#ecfdf5; border:1px solid #10b981; color:#065f46; padding:10px 15px; border-radius:12px; font-size:0.75rem;">
                                ✅ <strong>Resep Terverifikasi.</strong>
                            </div>
                        </div>

                        <div class="pay-section" style="padding: 12px 15px;">
                            <div class="pay-title">Pilih Durasi Pengiriman</div>
                            
                            <input type="hidden" id="dashboard-city-id" value="">
                            <div style="display: flex; gap: 8px; margin-bottom: 12px;">
                                <select id="dashboard-courier" class="form-control" style="flex: 1; padding: 8px 12px; font-size: 0.8rem; height: auto; border: 1.5px solid #e2e8f0; border-radius: 10px;">
                                    <option value="">-- Pilih Kurir --</option>
                                    <option value="jne">JNE</option>
                                    <option value="pos">POS</option>
                                    <option value="tiki">TIKI</option>
                                </select>
                                <button type="button" id="btn-cek-ongkir-dash" onclick="cekOngkirDashboard()" style="padding: 8px 12px; background: #0076D6; color: white; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; font-size: 0.75rem;">
                                    Cek
                                </button>
                            </div>

                            <div id="dashboard-ro-results" class="method-list">
                                <div style="text-align:center; font-size:0.75rem; color:#94a3b8; padding: 5px 0;">Cek tarif untuk melihat pilihan kurir.</div>
                            </div>

                            <div id="order-shipping-option" style="margin-top: 10px;">
                                <!-- Selected shipping from DB will go here if not changed -->
                            </div>
                        </div>

                        <div class="pay-section" style="background:#fcfdfe; padding: 12px 15px;">
                            <div class="pay-title">Metode Pembayaran</div>
                            <div class="pay-method-grid">
                                <label><input type="radio" name="payment_method" value="midtrans" checked onchange="calcTotal()">
                                    <div class="pay-method-card">
                                        <strong>Bayar Online (Midtrans)</strong>
                                        <div style="font-size: 0.6rem; color: #64748b; margin-top: 2px;">CC, GoPay, ShopeePay, VA</div>
                                    </div>
                                </label>
                                <label><input type="radio" name="payment_method" value="qris" onchange="calcTotal()">
                                    <div class="pay-method-card"><strong>QRIS</strong></div>
                                </label>
                                <label><input type="radio" name="payment_method" value="wallet" onchange="calcTotal()">
                                    <div class="pay-method-card">
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <strong>Saldo Dompet</strong>
                                            <span style="font-size: 0.75rem; color: #065f46; font-weight: 800;">Rp {{ number_format($user->wallet_balance, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </label>
                                <label><input type="radio" name="payment_method" value="paylater" onchange="calcTotal()">
                                    <div class="pay-method-card"><strong>Paylater</strong></div>
                                </label>
                            </div>

                            <div style="margin-top:15px; padding-top:12px; border-top:1.5px dashed #e2e8f0;">
                                <div class="pay-row"><span>Subtotal</span><span id="pay-subtotal" style="font-weight: 700; color: #1e293b;">Rp 0</span></div>
                                <div class="pay-row"><span>Ongkir</span><span id="pay-shipping" style="font-weight: 700; color: #1e293b;">Rp 15.000</span></div>
                                <div class="pay-row" style="font-weight:900; border-top:1.5px solid #f1f5f9; padding-top:12px; color:#0076D6; font-size:1.1rem; margin-top: 8px;"><span>Total</span><span id="pay-total">Rp 0</span></div>
                            </div>

                            <button type="submit" id="pay-confirm-btn" style="width:100%; padding:14px; background:#059669; color:white; border:none; border-radius:12px; font-weight:800; margin-top:15px; cursor:pointer; transition: 0.3s; box-shadow: 0 8px 20px rgba(5,150,105,0.15); font-size: 0.9rem;">Konfirmasi & Bayar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- SUCCESS MODAL (POST-PAYMENT) -->
    @if($justPaidOrder)
    <div id="successModal" style="display:flex; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:9999999; align-items:center; justify-content:center; backdrop-filter:blur(10px);">
        <div style="background:white; border-radius:28px; width:90%; max-width:400px; max-height:90vh; overflow-y:auto; text-align:center; padding:35px 25px; position:relative; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); animation: zoomIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);">
            <button onclick="document.getElementById('successModal').style.display='none'" 
                    style="position:absolute; right:15px; top:15px; border:none; background:#f1f5f9; color:#64748b; width:32px; height:32px; border-radius:50%; cursor:pointer; font-weight:800; display:flex; align-items:center; justify-content:center; font-size:1rem; transition:all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);"
                    onmouseover="this.style.background='#e2e8f0'; this.style.transform='scale(1.1)'"
                    onmouseout="this.style.background='#f1f5f9'; this.style.transform='scale(1)'"
                    onmousedown="this.style.transform='scale(0.9)'">✕</button>
            
            <!-- Success Animation (CSS only) -->
            <div style="width:80px; height:80px; background:#f0fdf4; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; border:4px solid #fff; box-shadow:0 0 0 4px #f0fdf4;">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="animation: checkmark 0.6s ease-in-out forwards;"><polyline points="20 6 9 17 4 12"/></svg>
            </div>

            @if($justPaidOrder->payment_status === 'paid')
                <h3 style="font-size:1.4rem; font-weight:800; color:#0f172a; margin-bottom:10px;">Yeay! Pembayaran Berhasil</h3>
                <p style="color:#64748b; font-size:0.9rem; line-height:1.5; margin-bottom:25px;">Pesanan kamu sedang kami siapkan. Terima kasih telah mempercayai Pharmacare.</p>
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
                <a href="{{ route('account.orders.invoice', $justPaidOrder->id) }}" target="_blank" style="display: block; text-align: center; width:100%; padding:14px; background:white; color:#0076D6; border:2px solid #0076D6; border-radius:16px; font-weight:800; font-size:0.95rem; cursor:pointer; text-decoration: none;">
                    <i class="fas fa-file-invoice mr-1"></i> Cetak Invoice
                </a>
                <button onclick="document.getElementById('successModal').style.display='none'" style="width:100%; padding:12px; background:white; color:#64748b; border:none; border-radius:16px; font-weight:700; font-size:0.9rem; cursor:pointer;">{{ $justPaidOrder->payment_status === 'paid' ? 'Tutup' : 'Selesai' }}</button>
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
            
            const trackingEl = document.getElementById('od-tracking-number');
            if (data.tracking_number) {
                trackingEl.innerText = 'No. Resi: ' + data.tracking_number;
                trackingEl.style.display = 'block';
            } else {
                trackingEl.style.display = 'none';
            }

            document.getElementById('od-addr-label').innerText = data.address_label;
            document.getElementById('od-address').innerText = data.address;
            
            // Invoice Link
            document.getElementById('od-invoice-btn').href = `/account/orders/${orderId}/invoice`;

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
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('pay-confirm-btn');
            const originalText = btn.innerText;
            const form = this;
            const formData = new FormData(form);

            btn.innerText = 'Memproses...';
            btn.disabled = true;

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.snap_token) {
                        window.snap.pay(data.snap_token, {
                            onSuccess: function(result) {
                                window.location.href = "{{ route('account.orders') }}?just_paid=" + data.order_id;
                            },
                            onPending: function(result) {
                                window.location.href = "{{ route('account.orders') }}";
                            },
                            onError: function(result) {
                                Toast.fire({ icon: 'error', title: 'Pembayaran gagal.' });
                                btn.innerText = originalText;
                                btn.disabled = false;
                            },
                            onClose: function() {
                                Toast.fire({ icon: 'info', title: 'Anda menutup popup pembayaran sebelum selesai.' });
                                btn.innerText = originalText;
                                btn.disabled = false;
                            }
                        });
                    } else {
                        window.location.href = data.redirect_url || "{{ route('account.orders') }}";
                    }
                } else {
                    btn.innerText = originalText;
                    btn.disabled = false;
                    Toast.fire({ 
                        icon: 'error', 
                        title: data.error || data.message || 'Terjadi kesalahan.' 
                    });
                }
            })
            .catch(error => {
                btn.innerText = originalText;
                btn.disabled = false;
                Toast.fire({ icon: 'error', title: 'Terjadi kesalahan sistem.' });
            });
        });

        function openPaymentModal(data) {
            currentSubtotal = data.subtotal;
            document.getElementById('pay-order-number').innerText = data.number;
            document.getElementById('pay-subtotal').innerText = 'Rp ' + data.subtotal.toLocaleString('id-ID');
            document.getElementById('paymentForm').action = data.url;
            document.getElementById('dashboard-city-id').value = data.city_id || '';

            // Reset results
            document.getElementById('dashboard-ro-results').innerHTML = '<div style="text-align:center; font-size:0.75rem; color:#94a3b8; padding: 5px 0;">Cek tarif untuk melihat pilihan kurir.</div>';

            // Sync shipping method from database
            const extraContainer = document.getElementById('order-shipping-option');
            extraContainer.innerHTML = '';

            if (data.shipping_method) {
                // Create dynamic option for courier from checkout as current selection
                extraContainer.innerHTML = `
                    <div style="font-size: 0.7rem; color: #94a3b8; margin-bottom: 5px; font-weight: 700;">METODE SAAT INI:</div>
                    <label><input type="radio" name="shipping_method" value="${data.shipping_method}" data-cost="${data.shipping_cost}" checked onchange="calcTotal()">
                        <div class="pay-method-card" style="border-color:var(--primary-blue); background:#E6F3FF;">
                            <strong>${data.shipping_method}</strong><br>
                            <span style="font-size:0.8rem; color:#666;">Pilihan Checkout (Rp ${data.shipping_cost.toLocaleString('id-ID')})</span>
                        </div>
                    </label>
                `;
            }

            // Prescription logic
            const warnBox = document.getElementById('pres-warning-box');
            const successBox = document.getElementById('pres-success-box');
            const btn = document.getElementById('pay-confirm-btn');

            warnBox.style.display = 'none';
            successBox.style.display = 'none';

            if (data.reqPres) {
                if (data.hasPres) {
                    successBox.style.display = 'block';
                } else {
                    warnBox.style.display = 'block';
                    btn.disabled = true;
                    btn.style.opacity = '0.5';
                    btn.innerText = 'Resep Diperlukan';
                }
            } else {
                btn.innerText = 'Konfirmasi & Bayar';
            }

            calcTotal();
            toggleModal('paymentModal');
        }

        function calcTotal() {
            const selectedRadio = document.querySelector('input[name="shipping_method"]:checked');
            let cost = 0;
            
            if (selectedRadio) {
                if (selectedRadio.dataset.cost) {
                    cost = parseInt(selectedRadio.dataset.cost);
                } else {
                    cost = selectedRadio.value === 'instant' ? 15000 : 10000;
                }
            }

            const total = currentSubtotal + cost;
            const costInput = document.getElementById('input-shipping-cost');
            if (costInput) costInput.value = cost;
            
            const shippingEl = document.getElementById('pay-shipping');
            if (shippingEl) shippingEl.innerText = 'Rp ' + cost.toLocaleString('id-ID');
            
            const totalEl = document.getElementById('pay-total');
            if (totalEl) totalEl.innerText = 'Rp ' + total.toLocaleString('id-ID');

            // Enable/disable confirm button based on shipping selection
            const btn = document.getElementById('pay-confirm-btn');
            if (!selectedRadio && !btn.innerText.includes('Memproses')) {
                btn.disabled = true;
                btn.style.opacity = '0.5';
                btn.innerText = 'Pilih Pengiriman Dahulu';
            } else if (selectedRadio && btn.innerText.includes('Pilih Pengiriman')) {
                btn.disabled = false;
                btn.style.opacity = '1';
                btn.innerText = 'Konfirmasi & Bayar';
            }
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

        function checkMidtransStatus(orderId, btn) {
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengecek...';

            fetch(`/account/orders/${orderId}/check-status`)
                .then(response => response.json())
                .then(data => {
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;

                    if (data.success) {
                        if (data.status === 'paid') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Pembayaran Berhasil!',
                                text: data.message,
                                confirmButtonColor: '#0076D6'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Toast.fire({
                                icon: 'info',
                                title: data.message
                            });
                        }
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Status Belum Berubah',
                            text: data.message,
                            confirmButtonColor: '#0076D6'
                        });
                    }
                })
                .catch(error => {
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                    Toast.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan saat mengecek status.'
                    });
                });
        }
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
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>
</body>
</html>
