<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacare - Keranjang Belanja</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/images/branding/pharmacare-logo-opt.png') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome/all.min.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="{{ asset('assets/vendor/alpinejs/alpine.min.js') }}" defer></script>
    <style>
        :root { 
            --primary-blue: #0076D6; 
            --bg: #F8FAFC; 
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background: var(--bg); color: var(--text-main); line-height: 1.5; animation: fadeIn 0.8s ease-out; }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes pageSlideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .container { max-width: 1200px; margin: 0 auto; padding: 40px; }
        
        /* Navigation Area */
        .top-bar { 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            margin-bottom: 30px; 
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }
        .btn-back-link { 
            display: flex; 
            align-items: center; 
            gap: 8px; 
            font-weight: 700; 
            color: var(--text-main); 
            text-decoration: none; 
            font-size: 1rem; 
            transition: 0.2s;
        }
        .btn-back-link:hover { color: var(--primary-blue); transform: translateX(-3px); }
        .page-title { font-size: 1.25rem; font-weight: 800; color: var(--text-muted); }

        .cart-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 40px; align-items: start; }

        /* Modern Cart Table */
        .cart-card { 
            background: white; 
            border-radius: 24px; 
            overflow: hidden; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.03); 
            border: 1px solid var(--border-color);
            animation: pageSlideUp 0.6s cubic-bezier(0.23, 1, 0.32, 1) both;
        }
        .cart-table { width: 100%; border-collapse: collapse; }
        .cart-table thead { background: #f8fafc; }
        .cart-table th { 
            padding: 20px 15px; 
            text-align: left; 
            font-size: 0.75rem; 
            font-weight: 800; 
            color: #94a3b8; 
            text-transform: uppercase; 
            letter-spacing: 1.5px;
            border-bottom: 2px solid #f1f5f9;
        }
        .cart-table th:nth-child(1) { width: 40%; }
        .cart-table th:nth-child(2) { width: 15%; }
        .cart-table th:nth-child(3) { width: 20%; }
        .cart-table th:nth-child(4) { width: 20%; }
        .cart-table th:nth-child(5) { width: 5%; }

        .cart-table td { padding: 30px 15px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        .cart-table tr:last-child td { border-bottom: none; }

        .item-info { display: flex; align-items: center; gap: 20px; }
        .item-image { 
            width: 80px; 
            height: 80px; 
            background: #f8fafc; 
            border-radius: 16px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 10px;
            border: 1px solid #f1f5f9;
        }
        .item-name { font-weight: 700; font-size: 1.1rem; color: var(--text-main); margin-bottom: 4px; }
        .item-badge { 
            background: #ffebeb; 
            color: #ef4444; 
            font-size: 0.7rem; 
            padding: 4px 10px; 
            border-radius: 30px; 
            font-weight: 700;
            display: inline-block;
        }

        /* Qty Controls */
        .qty-box { 
            display: flex; 
            align-items: center; 
            background: #f1f5f9; 
            border-radius: 12px; 
            padding: 4px;
            width: fit-content;
        }
        .qty-btn { 
            width: 32px; 
            height: 32px; 
            background: transparent; 
            border: none; 
            cursor: pointer; 
            font-size: 1.1rem; 
            font-weight: 800; 
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            transition: 0.2s;
        }
        .qty-btn:hover { background: white; color: var(--primary-blue); box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .qty-input { 
            width: 44px; 
            border: none; 
            background: transparent; 
            text-align: center; 
            font-size: 1rem; 
            font-weight: 800; 
            color: var(--text-main);
            outline: none;
        }

        .price-text { font-weight: 700; font-size: 1.05rem; color: var(--text-main); white-space: nowrap; }
        .subtotal-text { font-weight: 800; font-size: 1.15rem; color: var(--primary-blue); white-space: nowrap; }
        
        .btn-delete { 
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: #fff5f5; 
            border: 1.5px solid #ffebeb; 
            color: #ef4444; 
            cursor: pointer; 
            font-size: 1rem; 
            border-radius: 12px; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-delete:hover { 
            background: #ef4444; 
            color: white; 
            border-color: #ef4444;
            transform: scale(1.1) rotate(5deg);
        }

        /* Sidebar UI */
        .summary-card { 
            background: white; 
            border-radius: 24px; 
            padding: 30px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.03); 
            border: 1px solid var(--border-color);
            position: sticky; 
            top: 40px; 
            animation: pageSlideUp 0.6s cubic-bezier(0.23, 1, 0.32, 1) 0.1s both;
        }
        .summary-title { font-size: 1.5rem; font-weight: 800; margin-bottom: 25px; color: var(--text-main); }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 15px; color: var(--text-muted); font-weight: 500; }
        .summary-row.total { 
            border-top: 2px dashed var(--border-color); 
            padding-top: 20px; 
            margin-top: 20px; 
            font-weight: 800; 
            font-size: 1.5rem; 
            color: var(--primary-blue); 
        }

        .user-mini-card { 
            background: #f0f7ff; 
            border-radius: 20px; 
            padding: 20px; 
            margin-bottom: 25px; 
            border: 1px solid #d0e7ff;
        }
        .user-name { font-weight: 800; color: #1e3a8a; font-size: 1rem; margin-bottom: 2px; }
        .user-email { color: #60a5fa; font-size: 0.85rem; margin-bottom: 12px; }
        .limit-pill { background: white; border-radius: 12px; padding: 10px 15px; font-weight: 800; font-size: 0.85rem; color: var(--primary-blue); display: flex; justify-content: space-between; }

        .btn-primary-action { 
            width: 100%; 
            background: var(--primary-blue); 
            color: white; 
            padding: 20px; 
            border-radius: 16px; 
            border: none; 
            font-size: 1.1rem; 
            font-weight: 800; 
            cursor: pointer; 
            text-decoration: none; 
            display: block; 
            text-align: center; 
            transition: 0.3s;
            box-shadow: 0 10px 20px rgba(0,118,214,0.15);
        }
        .btn-primary-action:hover { transform: translateY(-2px); box-shadow: 0 15px 30px rgba(0,118,214,0.25); filter: brightness(1.1); }
        
        .btn-secondary-action { 
            display: block; 
            text-align: center; 
            margin-top: 20px; 
            color: var(--text-muted); 
            text-decoration: none; 
            font-weight: 700; 
            font-size: 0.9rem;
        }
        .btn-secondary-action:hover { color: var(--primary-blue); }

        /* Notification Adjustment */
        .swal2-container.swal2-top-end { top: 70px !important; }

        @media (max-width: 1024px) { .cart-layout { grid-template-columns: 1fr; } }

        /* Mobile Specific Optimization */
        @media (max-width: 768px) {
            .container { padding: 15px; }
            .top-bar { margin-bottom: 15px; padding-bottom: 15px; }
            .page-title { font-size: 0.95rem; }
            
            .cart-card { border-radius: 20px; overflow: hidden; }
            
            /* Keep table layout but make it scrollable */
            .cart-table { min-width: 600px; }
            .cart-table th, .cart-table td { padding: 15px 10px; }
            
            .item-image { width: 50px; height: 50px; border-radius: 10px; }
            .item-name { font-size: 0.9rem; }
            .item-badge { font-size: 0.6rem; padding: 2px 6px; }
            
            .qty-box { scale: 0.85; transform-origin: left; }
            .price-text { font-size: 0.9rem; }
            .subtotal-text { font-size: 0.95rem; }
            
            .btn-delete { width: 32px; height: 32px; font-size: 0.85rem; }
            
            .summary-card { padding: 20px; border-radius: 20px; }
        }
    </style>
</head>
<body>

<div class="container" x-data="cartPage">
    <!-- Top Bar Navigation -->
    <div class="top-bar">
        <a href="{{ route('store.index') }}" class="btn-back-link">
            <i class="fas fa-chevron-left"></i> Kembali Belanja
        </a>
        <div class="page-title">Ringkasan Keranjang</div>
    </div>

    <template x-if="items.length === 0">
        <div class="cart-card">
            <div style="text-align: center; padding: 100px 40px;">
                <div style="font-size: 4rem; color: #e2e8f0; margin-bottom: 20px;"><i class="fas fa-shopping-basket"></i></div>
                <h2 style="font-weight: 800; color: var(--text-muted); margin-bottom: 25px;">Keranjang belanja Anda kosong</h2>
                <a href="{{ route('store.index') }}" class="btn-primary-action" style="display:inline-block; width:auto; padding: 15px 40px;">Buka Katalog Obat</a>
            </div>
        </div>
    </template>

    <template x-if="items.length > 0">
        <div class="cart-layout">
            <!-- Items Section -->
            <div class="cart-card">
                <div style="overflow-x: auto;">
                    <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="item in items" :key="item.id">
                        <tr x-show="true" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                            <td>
                                <div class="item-info">
                                    <div class="item-image">
                                        <template x-if="item.image_path">
                                            <img :src="'{{ asset('') }}' + item.image_path" :alt="item.name" style="width:100%; height:100%; object-fit:contain;">
                                        </template>
                                        <template x-if="!item.image_path">
                                            <span style="font-size:2rem;" x-text="item.requires_prescription ? '⚕️' : '💊'"></span>
                                        </template>
                                    </div>
                                    <div>
                                        <div class="item-name" x-text="item.name"></div>
                                        <template x-if="item.requires_prescription">
                                            <span class="item-badge">Wajib Resep</span>
                                        </template>
                                        <!-- Stok tersedia -->
                                        <div style="margin-top: 6px; font-size: 0.75rem; font-weight: 600;" 
                                             :style="item.stock <= 0 ? 'color: #ef4444;' : item.stock <= 5 ? 'color: #f59e0b;' : 'color: #10b981;'">
                                            <i style="margin-right: 4px;"></i>
                                            <span x-text="item.stock <= 0 ? 'Stok habis' : 'Stok: ' + item.stock + ' tersedia'"></span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="price-text" x-text="formatIDR(item.price)"></div>
                            </td>
                            <td :data-subtotal="formatIDR(item.price * item.qty)">
                                <div class="qty-box">
                                    <button type="button" class="qty-btn" @click="updateQty(item.id, -1)" :disabled="item.qty <= 1">−</button>
                                    <input type="number" class="qty-input" x-model.number="item.qty" readonly>
                                    <button type="button" class="qty-btn" @click="updateQty(item.id, 1)" 
                                            :disabled="item.qty >= item.stock"
                                            :style="item.qty >= item.stock ? 'opacity: 0.4; cursor: not-allowed;' : ''">+</button>
                                </div>
                            </td>
                            <td>
                                <div class="subtotal-text" x-text="formatIDR(item.price * item.qty)"></div>
                            </td>
                            <td style="text-align: right;">
                                <button class="btn-delete" @click="removeItem(item.id)" title="Hapus Produk">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                        </template>
                    </tbody>
                </table>
                </div>
            </div>

            <!-- Summary Section -->
            <aside>
                @auth
                <div class="user-mini-card">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-email">{{ Auth::user()->email }}</div>
                    <div class="limit-pill">
                        <span>Limit Paylater</span>
                        <span>Rp {{ number_format(Auth::user()->paylater_limit ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>
                @endauth

                <div class="summary-card">
                    <h2 class="summary-title">Ringkasan Belanja</h2>
                    
                    <div class="summary-row">
                        <span>Subtotal (<span x-text="items.length"></span> produk)</span>
                        <span style="color: var(--text-main); font-weight: 700;" x-text="formatIDR(grandTotal)"></span>
                    </div>
                    <div class="summary-row">
                        <span>Estimasi Pengiriman</span>
                        <span style="color: #059669; font-weight: 700;">GRATIS</span>
                    </div>
                    
                    <div class="summary-row total">
                        <span>Total</span>
                        <span x-text="formatIDR(grandTotal)"></span>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="btn-primary-action">
                        Lanjut ke Pembayaran <i class="fas fa-arrow-right" style="margin-left:8px; font-size:0.9rem;"></i>
                    </a>

                    <a href="{{ route('store.index') }}" class="btn-secondary-action">
                        <i class="fas fa-arrow-left"></i> Tambah Produk Lain
                    </a>

                    <form action="{{ route('cart.clear') }}" method="POST" style="margin-top: 25px; border-top: 1px solid #f1f5f9; padding-top: 20px; text-align: center;">
                        @csrf
                        <button type="submit" style="background: none; border: none; color: #cbd5e1; font-weight: 600; font-size: 0.8rem; cursor: pointer; transition: 0.2s;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#cbd5e1'">Kosongkan keranjang</button>
                    </form>
                </div>
            </aside>
        </div>
    </template>
</div>

<!-- Scripts -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('cartPage', () => ({
            items: @json($items),
            grandTotal: {{ $grandTotal }},
            cartCount: {{ count($items) }},
            isLoading: false,

            async updateQty(itemId, change) {
                let item = this.items.find(i => i.id === itemId);
                if (!item) return;

                let newQty = item.qty + change;
                // Jangan melebihi stok tersedia
                if (newQty < 1 || newQty > 99 || newQty > item.stock) return;

                // Optimistic UI Update
                const oldQty = item.qty;
                item.qty = newQty;
                this.recalculateTotal();

                try {
                    const res = await fetch(`{{ url('/cart/update') }}/${itemId}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ qty: newQty })
                    });
                    
                    const data = await res.json();
                    if (!data.success) {
                        item.qty = oldQty;
                        this.recalculateTotal();
                        window.showToast('error', data.message || 'Gagal memperbarui kuantitas');
                    } else {
                        // Sync EVERYTHING with server data
                        this.items = data.items;
                        this.grandTotal = data.grand_total;
                        this.cartCount = data.cart_count;
                        if (window.StoreUI) {
                            window.StoreUI.cartItems = data.items || [];
                            window.StoreUI.cartTotal = data.grand_total || 0;
                            window.StoreUI.cartCount = data.cart_count || 0;
                        }
                    }
                } catch (e) {
                    item.qty = oldQty;
                    this.recalculateTotal();
                    window.showToast('error', 'Koneksi bermasalah.');
                }
            },

            async removeItem(itemId) {
                // Langsung hapus (Optimistic UI)
                const originalItems = [...this.items];
                this.items = this.items.filter(i => i.id !== itemId);
                this.recalculateTotal();

                try {
                    const timestamp = new Date().getTime();
                    const res = await fetch(`{{ url('/cart/remove') }}/${itemId}?t=${timestamp}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    if (!res.ok) throw new Error('Server returned ' + res.status);

                    const data = await res.json();
                    if (data.success) {
                        // Sync final state from server
                        this.items = data.items || [];
                        this.grandTotal = data.grand_total || 0;
                        this.cartCount = data.cart_count || 0;

                        if (window.StoreUI) {
                            window.StoreUI.cartItems = data.items || [];
                            window.StoreUI.cartTotal = data.grand_total || 0;
                            window.StoreUI.cartCount = data.cart_count || 0;
                        }
                    } else {
                        throw new Error(data.message || 'Gagal menghapus item');
                    }
                } catch (e) {
                    console.error('Remove Error:', e);
                    // Kembalikan jika error
                    this.items = originalItems;
                    this.recalculateTotal();
                    if (window.showToast) window.showToast('error', 'Gagal: ' + e.message);
                }
            },

            recalculateTotal() {
                this.grandTotal = this.items.reduce((sum, i) => sum + (i.price * i.qty), 0);
            },

            formatIDR(num) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(num).replace('Rp', 'Rp ');
            }
        }));
    });
</script>

<!-- SweetAlert2 -->
<script src="{{ asset('sweetalert/sweetalert2.all.min.js') }}"></script>
<script>
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        iconColor: '#0076D6',
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
</script>
</body>
</html>
