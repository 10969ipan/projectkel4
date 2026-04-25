<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacare - {{ $item->name ?? 'Detail Obat' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/branding/pharmacare-logo-opt.png') }}">
    <style>
        /* Desktop-First Detail Page */
        :root {
            --primary-blue: #0076D6; 
            --bg-color: #F8F9FB;
            --text-main: #333333;
            --text-muted: #888888;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
        body { background-color: var(--bg-color); color: var(--text-main); }

        .container {
            max-width: 1200px; 
            margin: 0 auto;
            padding: 40px;
        }

        /* Top Nav - RESTORED TO MINIMALIST */
        .top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .top-bar a { 
            text-decoration: none; 
            color: var(--text-main); 
            font-size: 1.2rem; 
            font-weight: 600; 
            display: flex; 
            align-items: center; 
            gap: 10px; 
        }
        
        .top-bar a:hover { color: var(--primary-blue); }

        .cart-link {
            background: #E6F3FF;
            color: var(--primary-blue);
            padding: 10px 20px;
            border-radius: 30px;
            font-weight: 600;
            position: relative;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .cart-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            background: #EF5350;
            color: white;
            font-size: 0.7rem;
            font-weight: 700;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Main Content Grid */
        .product-wrapper {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            display: grid;
            grid-template-columns: 1fr 1fr;
            overflow: hidden;
        }

        /* Left Side: Image */
        .image-section {
            background: #F0F4F8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8rem;
            min-height: 500px;
        }

        /* Right Side: Details */
        .details-section {
            padding: 50px;
            display: flex;
            flex-direction: column;
        }

        .category-badge {
            background: #E6F3FF;
            color: var(--primary-blue);
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 20px;
            width: fit-content;
        }
        .category-badge.keras { background: #ffebeb; color: #d32f2f; }

        .product-title { font-size: 2.2rem; font-weight: 700; margin-bottom: 10px; line-height: 1.2; }
        .product-variant { font-size: 1.1rem; color: var(--text-muted); margin-bottom: 20px; }

        .rating { color: #f59e0b; font-size: 1.1rem; margin-bottom: 30px; display: flex; gap: 5px; }
        .rating span { color: var(--text-main); font-weight: 600; margin-left: 10px; }

        .price { font-size: 2.5rem; font-weight: 700; color: var(--primary-blue); margin-bottom: 40px; }

        .desc-title { font-size: 1.2rem; font-weight: 600; margin-bottom: 15px; }
        .desc-text { font-size: 1.05rem; color: var(--text-muted); line-height: 1.6; margin-bottom: 40px; }

        /* Action Buttons */
        .action-container {
            display: flex;
            gap: 20px;
            margin-top: auto;
        }

        .qty-selector {
            display: flex;
            align-items: center;
            border: 2px solid #E0E0E0;
            border-radius: 12px;
            overflow: hidden;
            height: 60px;
        }

        .btn-qty {
            width: 50px;
            height: 100%;
            background: white;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-qty:hover { background: #F5F5F5; }
        
        .qty-number { width: 60px; text-align: center; font-size: 1.2rem; font-weight: 700; }

        .btn-buy {
            flex: 1;
            background: var(--primary-blue);
            color: white;
            border-radius: 12px;
            border: none;
            font-size: 1.2rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 60px;
            transition: background 0.2s;
        }

        .btn-buy:hover { background: #005FA3; }

        .btn-disabled { background: #B0BEC5; cursor: not-allowed; }

        /* Transitions & Animations */
        @keyframes slideUpFade {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .product-wrapper {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            display: grid;
            grid-template-columns: 1fr 1fr;
            overflow: hidden;
            animation: slideUpFade 0.6s cubic-bezier(0.23, 1, 0.32, 1) forwards;
        }

        .image-section img {
            animation: fadeIn 0.8s ease-out 0.2s both;
        }

        .details-section > * {
            opacity: 0;
            animation: slideUpFade 0.5s cubic-bezier(0.23, 1, 0.32, 1) forwards;
        }

        .details-section > *:nth-child(1) { animation-delay: 0.1s; }
        .details-section > *:nth-child(2) { animation-delay: 0.15s; }
        .details-section > *:nth-child(3) { animation-delay: 0.2s; }
        .details-section > *:nth-child(4) { animation-delay: 0.25s; }
        .details-section > *:nth-child(5) { animation-delay: 0.3s; }
        .details-section > *:nth-child(6) { animation-delay: 0.35s; }
        .details-section > .action-container { animation-delay: 0.4s; }

        .top-bar {
            animation: fadeIn 0.5s ease-out;
        }

        .cart-link {
            transition: all 0.3s ease;
        }
        .cart-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,118,214,0.2);
        }

        @media (max-width: 900px) {
            .container { padding: 15px !important; }
            .top-bar { margin-bottom: 15px; }
            .top-bar a { font-size: 0.95rem; }
            .product-wrapper { grid-template-columns: 1fr; border-radius: 24px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); }
            .image-section { min-height: 250px !important; padding: 20px !important; }
            .image-section img { max-height: 220px !important; }
            .details-section { padding: 25px 20px !important; }
            .category-badge { font-size: 0.75rem !important; margin-bottom: 10px !important; padding: 4px 12px !important; }
            .product-title { font-size: 1.4rem !important; font-weight: 800 !important; margin-bottom: 4px !important; }
            .product-variant { font-size: 0.9rem !important; margin-bottom: 12px !important; }
            .rating { font-size: 0.85rem !important; margin-bottom: 15px !important; }
            .price { font-size: 1.6rem !important; font-weight: 800 !important; margin-bottom: 25px !important; }
            .desc-title { font-size: 0.95rem !important; margin-bottom: 8px !important; }
            .desc-text { font-size: 0.88rem !important; line-height: 1.6 !important; margin-bottom: 30px !important; color: #64748b !important; }
            .action-container { flex-direction: row !important; gap: 15px !important; margin-top: 15px !important; }
            .qty-selector { width: 130px !important; height: 60px !important; border-radius: 16px !important; background: #f1f5f9 !important; border: none !important; padding: 4px !important; justify-content: space-between !important; flex-shrink: 0 !important; }
            .qty-selector .btn-qty { width: 44px !important; height: 52px !important; border-radius: 12px !important; background: white !important; font-size: 1.3rem !important; display: flex !important; align-items: center !important; justify-content: center !important; box-shadow: 0 2px 6px rgba(0,0,0,0.06) !important; color: #1e293b !important; }
            .qty-number { font-size: 1.1rem !important; font-weight: 800 !important; width: 40px !important; background: transparent !important; color: #1e293b !important; }
            .btn-buy { flex: 1 !important; height: 60px !important; font-size: 1rem !important; border-radius: 16px !important; font-weight: 800 !important; background: var(--primary-blue) !important; color: white !important; box-shadow: 0 8px 20px rgba(0,118,214,0.15) !important; text-transform: none !important; border: none !important; margin-top: 0 !important; }
        }

        /* SweetAlert2 Toast Adjustment */
        .swal2-container.swal2-top-end, .swal2-container.swal2-top-right {
            top: 70px !important;
            right: 20px !important;
        }
    </style>
</head>
<body>

<div class="container" x-data="productPage">
    <div class="top-bar">
        <a href="{{ route('store.index') }}" style="text-decoration: none; color: var(--text-main); font-weight: 700; display: flex; align-items: center; gap: 8px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Kembali
        </a>
        <a href="{{ route('cart.index') }}" class="cart-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
            </svg>
            <span x-text="cartCount" x-show="cartCount > 0" style="font-size: 0.9rem; font-weight: 800;"></span>
        </a>
    </div>

    <div class="product-wrapper">
        <div class="image-section" style="background: white; padding: 40px;">
            @if($item->image_path)
                <img src="{{ asset($item->image_path) }}" alt="{{ $item->name }}" width="400" height="400" style="max-height: 400px; width: auto; object-fit: contain;">
            @else
                {{ isset($item) && $item->requires_prescription ? '⚕️' : '💊' }}
            @endif
        </div>

        <div class="details-section">
            <div class="category-badge {{ isset($item) && $item->requires_prescription ? 'keras' : '' }}">
                {{ isset($item) && $item->requires_prescription ? 'Obat Keras (Wajib Resep)' : 'Obat Bebas' }}
            </div>

            <h1 class="product-title">{{ $item->name ?? 'Dummy Product' }}</h1>
            <p class="product-variant">{{ $item->unit->name ?? 'Dus Kapsul' }}</p>

            <div class="rating">
                ★★★★☆ <span>4.8 (120 Ulasan)</span>
            </div>

            <div class="price">Rp {{ isset($item) ? number_format($item->price, 0, ',', '.') : '50.000' }}</div>

            @if(isset($item) && $item->requires_prescription)
            <div style="background: #FFF5F5; border: 1px solid #FEB2B2; border-radius: 12px; padding: 20px; margin-bottom: 30px; display: flex; align-items: flex-start; gap: 15px;">
                <div style="background: #F56565; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-weight: 800;">!</div>
                <div>
                    <h4 style="color: #C53030; font-size: 1rem; margin-bottom: 5px;">Wajib Resep Dokter</h4>
                    <p style="color: #742A2A; font-size: 0.9rem; line-height: 1.5;">Obat ini termasuk golongan obat keras. Anda <strong>wajib melampirkan foto resep dokter asli</strong> pada saat melakukan pembayaran/checkout.</p>
                </div>
            </div>
            @endif

            <h3 class="desc-title">Deskripsi Medis</h3>
            <p class="desc-text">
                {{ $item->description ?? 'Obat ini ditujukan untuk mengobati gejala gangguan kesehatan. Digunakan melalui pertimbangan matang atau atas petunjuk Dokter sesuai dengan dosisnya. Simpan di tempat sejuk.' }}
            </p>

            <!-- Action Buttons -->
            <div class="action-container">
                <div class="qty-selector">
                    <button type="button" class="btn-qty" @click="qty > 1 ? qty-- : null">−</button>
                    <input type="number" name="qty" x-model.number="qty" class="qty-number" min="1" max="99" style="width:60px; border:none; text-align:center; background: transparent;">
                    <button type="button" class="btn-qty" @click="qty < 99 ? qty++ : null">+</button>
                </div>

                <button type="button" @click="addToCart({{ $item->id }})" class="btn-buy">
                    <i class="fas fa-shopping-cart" style="margin-right: 10px; font-size: 0.9rem;"></i> Tambah ke Keranjang
                </button>
            </div>

        </div>
    </div>
</div>

    <!-- SweetAlert2 & Alpine.js -->
    <script src="{{ asset('sweetalert/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/alpinejs/alpine.min.js') }}" defer></script>
    
    @php
        $initialCart = session()->get('cart', []);
        $initialCount = 0;
        foreach($initialCart as $details) {
            $initialCount += $details['qty'];
        }
    @endphp

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('productPage', () => ({
                cartCount: {{ $initialCount }},
                qty: 1,
                isLoading: false,
                isAuthenticated: {{ Auth::check() ? 'true' : 'false' }},

                async addToCart(itemId) {
                    if (!this.isAuthenticated) {
                        Swal.fire({
                            title: 'Silakan Masuk',
                            text: 'Anda harus masuk akun untuk berbelanja.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#0076D6',
                            cancelButtonColor: '#64748b',
                            confirmButtonText: 'Masuk Sekarang',
                            cancelButtonText: 'Nanti Saja'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "{{ route('store.index') }}?auth=login";
                            }
                        });
                        return;
                    }

                    console.log('ProductPage Action (Optimistic): Adding', this.qty, 'pcs');
                    
                    const addedQty = parseInt(this.qty);
                    // 1. Optimistic UI Update
                    this.cartCount = parseInt(this.cartCount) + addedQty;
                    
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        iconColor: '#0076D6'
                    });

                    Toast.fire({
                        icon: 'success',
                        title: 'Ditambahkan ke keranjang'
                    });

                    const url = `{{ url('/cart/add-ajax') }}/${itemId}?qty=${addedQty}&type=once&interval=30`;
                    try {
                        const res = await fetch(url, {
                            method: 'POST',
                            headers: { 
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        const data = await res.json();
                        if (data.success) {
                            // Sync real count from server
                            this.cartCount = data.cart_count;
                        } else {
                            // Revert on error
                            this.cartCount -= addedQty;
                            Toast.fire({ icon: 'error', title: data.message });
                        }
                    } catch (e) {
                        this.cartCount -= addedQty;
                        console.error('Cart Error:', e);
                    }
                }
            }));
        });
    </script>
</body>
</html>
