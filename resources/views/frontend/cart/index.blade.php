<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacare - Keranjang Belanja</title>
    <style>
        :root { --primary-blue: #0076D6; --bg: #F8F9FB; --muted: #888; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
        body { background: var(--bg); color: #333; }

        .container { max-width: 1200px; margin: 40px auto; padding: 0 40px; }
        .top-bar { display: flex; align-items: center; gap: 15px; margin-bottom: 30px; }
        .top-bar a { text-decoration: none; color: var(--primary-blue); font-weight: 600; font-size: 1.1rem; }
        .top-bar h1 { font-size: 2rem; font-weight: 700; }

        .alert-success { background: #E8F5E9; color: #2E7D32; border-left: 4px solid #4CAF50; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; }
        .alert-error { background: #FFEBEE; color: #C62828; border-left: 4px solid #EF5350; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; }

        .cart-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 40px; }

        /* Cart Table */
        .cart-table-wrap { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
        .cart-table { width: 100%; border-collapse: collapse; }
        .cart-table thead { background: #F0F4F8; }
        .cart-table th { padding: 18px 20px; text-align: left; font-size: 0.9rem; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .cart-table td { padding: 20px; border-bottom: 1px solid #F0F0F0; vertical-align: middle; }
        .cart-table tr:last-child td { border-bottom: none; }

        .item-info { display: flex; align-items: center; gap: 15px; }
        .item-icon { width: 60px; height: 60px; background: #F0F4F8; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; }
        .item-name { font-weight: 600; font-size: 1rem; margin-bottom: 4px; }
        .item-badge { background: #ffebeb; color: #c62828; font-size: 0.75rem; padding: 3px 8px; border-radius: 10px; }

        /* Qty Input */
        .qty-control { display: flex; align-items: center; border: 2px solid #E0E0E0; border-radius: 10px; overflow: hidden; width: fit-content; }
        .qty-btn { width: 36px; height: 36px; background: #F5F5F5; border: none; cursor: pointer; font-size: 1.2rem; font-weight: bold; transition: background 0.2s; }
        .qty-btn:hover { background: #E6F3FF; color: var(--primary-blue); }
        .qty-input { width: 50px; height: 36px; border: none; text-align: center; font-size: 1rem; font-weight: 600; outline: none; }

        .btn-remove { background: none; border: none; color: #EF5350; cursor: pointer; font-size: 0.9rem; font-weight: 600; padding: 5px 10px; border-radius: 8px; transition: background 0.2s; }
        .btn-remove:hover { background: #FFEBEE; }

        .price-col { font-weight: 700; font-size: 1.1rem; }
        .subtotal-col { font-weight: 700; font-size: 1.1rem; color: var(--primary-blue); }

        /* Empty Cart */
        .empty-cart { text-align: center; padding: 80px 40px; }
        .empty-cart div { font-size: 5rem; margin-bottom: 20px; }
        .empty-cart h2 { font-size: 1.5rem; color: var(--muted); margin-bottom: 15px; }
        .empty-cart a { background: var(--primary-blue); color: white; padding: 14px 30px; border-radius: 12px; text-decoration: none; font-weight: 600; }

        /* Summary Box */
        .summary-box { background: white; border-radius: 20px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); height: fit-content; position: sticky; top: 40px; }
        .summary-box h2 { font-size: 1.4rem; font-weight: 700; margin-bottom: 25px; border-bottom: 2px solid #F0F0F0; padding-bottom: 15px; }
        .summary-row { display: flex; justify-content: space-between; font-size: 1rem; margin-bottom: 15px; color: #555; }
        .summary-row.total { font-weight: 700; font-size: 1.4rem; color: var(--primary-blue); border-top: 2px solid #F0F0F0; padding-top: 20px; margin-top: 10px; margin-bottom: 25px; }

        /* User Info Card */
        .user-card { background: #E6F3FF; border-radius: 14px; padding: 18px; margin-bottom: 20px; }
        .user-card h3 { font-size: 1rem; font-weight: 700; margin-bottom: 5px; }
        .user-card p { font-size: 0.9rem; color: #555; }
        .paylater-limit { margin-top: 10px; background: white; border-radius: 8px; padding: 8px 12px; font-size: 0.9rem; font-weight: 600; color: var(--primary-blue); }

        .btn-checkout { width: 100%; background: var(--primary-blue); color: white; padding: 18px; border-radius: 12px; border: none; font-size: 1.15rem; font-weight: 700; cursor: pointer; text-decoration: none; display: block; text-align: center; transition: background 0.2s; }
        .btn-checkout:hover { background: #005FA3; }

        .btn-continue { display: block; text-align: center; margin-top: 15px; color: var(--primary-blue); text-decoration: none; font-weight: 600; }

        @media (max-width: 900px) { .cart-layout { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<div class="container">
    <div class="top-bar">
        <a href="{{ route('store.index') }}">←</a>
        <h1>Keranjang Belanja</h1>
    </div>



    @if(count($items) === 0)
        <div class="cart-table-wrap">
            <div class="empty-cart">
                <div style="font-size:3rem; color:#ddd;">∅</div>
                <h2>Keranjang Anda masih kosong</h2>
                <a href="{{ route('store.index') }}">Mulai Belanja Obat</a>
            </div>
        </div>
    @else
        <div class="cart-layout">
            <!-- Cart Table -->
            <div class="cart-table-wrap">
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
                        @foreach($items as $cartItem)
                        <tr>
                            <td>
                                <div class="item-info">
                                    <div class="item-icon" style="overflow: hidden;">
                                        @if(!empty($cartItem['image_path']))
                                            <img src="{{ asset($cartItem['image_path']) }}" alt="{{ $cartItem['name'] }}" style="width:100%; height:100%; object-fit:contain;">
                                        @else
                                            <span style="font-size:1.5rem;">{{ $cartItem['requires_prescription'] ? '⚕️' : '💊' }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="item-name">{{ $cartItem['name'] }}</div>
                                        @if($cartItem['requires_prescription'])
                                            <span class="item-badge">Wajib Resep</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="price-col">Rp {{ number_format($cartItem['price'], 0, ',', '.') }}</td>
                            <td>
                                <form action="{{ route('cart.update', $cartItem['id']) }}" method="POST" class="qty-form" style="display:flex; align-items: center;">
                                    @csrf
                                    @method('PATCH')
                                    <div class="qty-control">
                                        <button type="button" class="qty-btn" onclick="decrementQty(this)">−</button>
                                        <input type="number" name="qty" class="qty-input" value="{{ $cartItem['qty'] }}" min="1" max="99">
                                        <button type="button" class="qty-btn" onclick="incrementQty(this)">+</button>
                                    </div>
                                </form>
                            </td>
                            <td class="subtotal-col">Rp {{ number_format($cartItem['subtotal'], 0, ',', '.') }}</td>
                            <td>
                                <form action="{{ route('cart.remove', $cartItem['id']) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-remove" type="submit">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Summary Sidebar -->
            <div>
                <!-- User Info (mengikuti data user yang login) -->
                @auth
                <div class="user-card">
                    <h3>{{ Auth::user()->name }}</h3>
                    <p>{{ Auth::user()->email }}</p>
                    <div class="paylater-limit">
                        Limit Paylater: Rp {{ number_format(Auth::user()->paylater_limit ?? 0, 0, ',', '.') }}
                    </div>
                </div>
                @endauth

                <div class="summary-box">
                    <h2>Ringkasan</h2>
                    <div class="summary-row">
                        <span>Subtotal ({{ count($items) }} produk)</span>
                        <span>Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Estimasi Ongkir</span>
                        <span>Rp 15.000</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span>Rp {{ number_format($grandTotal + 15000, 0, ',', '.') }}</span>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="btn-checkout">
                        Lanjut ke Pembayaran →
                    </a>
                    <a href="{{ route('store.index') }}" class="btn-continue">← Lanjut Belanja</a>

                    <form action="{{ route('cart.clear') }}" method="POST" style="margin-top: 15px; text-align: center;">
                        @csrf
                        <button type="submit" style="background: none; border: none; color: var(--muted); font-size: 0.9rem; cursor: pointer;">Kosongkan keranjang</button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    let debounceTimers = {};

    function decrementQty(btn) {
        const input = btn.nextElementSibling;
        const val = parseInt(input.value);
        if (val > 1) {
            input.value = val - 1;
            autoSubmit(input);
        }
    }

    function incrementQty(btn) {
        const input = btn.previousElementSibling;
        const val = parseInt(input.value);
        if (val < 99) {
            input.value = val + 1;
            autoSubmit(input);
        }
    }

    function autoSubmit(input) {
        const form = input.closest('form');
        const formId = form.action; // unique per form
        clearTimeout(debounceTimers[formId]);
        debounceTimers[formId] = setTimeout(() => {
            form.submit();
        }, 400); // submit after 400ms pause
    }
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
