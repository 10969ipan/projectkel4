<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacare - {{ $item->name ?? 'Detail Obat' }}</title>
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

        /* Top Nav */
        .top-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .top-bar a { text-decoration: none; color: var(--text-main); font-size: 1.2rem; font-weight: 600; display: flex; align-items: center; gap: 10px; }
        .top-bar a:hover { color: var(--primary-blue); }

        .cart-link {
            background: #E6F3FF;
            color: var(--primary-blue);
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: 600;
            position: relative;
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

        @media (max-width: 900px) {
            .product-wrapper { grid-template-columns: 1fr; }
            .image-section { min-height: 300px; }
            .action-container { flex-direction: column; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="top-bar">
        <a href="{{ route('store.index') }}">❮ Kembali ke Katalog</a>
        <a href="{{ route('cart.index') }}" class="cart-link">
            🛒 Keranjang
            @php $cartCount = array_sum(session()->get('cart', [])); @endphp
            @if($cartCount > 0)
                <span class="cart-badge">{{ $cartCount }}</span>
            @endif
        </a>
    </div>

    <div class="product-wrapper">
        <div class="image-section">
            {{ isset($item) && $item->requires_prescription ? '⚕️' : '💊' }}
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

            <h3 class="desc-title">Deskripsi Medis</h3>
            <p class="desc-text">
                {{ $item->description ?? 'Obat ini ditujukan untuk mengobati gejala gangguan kesehatan. Digunakan melalui pertimbangan matang atau atas petunjuk Dokter sesuai dengan dosisnya. Simpan di tempat sejuk.' }}
            </p>

            <!-- Action Buttons -->
            <div class="action-container">
                <form action="{{ route('cart.add', $item->id ?? 1) }}" method="POST" style="display: flex; gap: 20px; flex: 1;">
                    @csrf
                    <div class="qty-selector">
                        <button type="button" class="btn-qty" onclick="document.getElementById('qty-input').stepDown()">−</button>
                        <input type="number" name="qty" id="qty-input" class="qty-number" value="1" min="1" max="99" style="width:60px; border:none; text-align:center;">
                        <button type="button" class="btn-qty" onclick="document.getElementById('qty-input').stepUp()">+</button>
                    </div>

                    @if(isset($item) && $item->requires_prescription && !Auth::user()?->is_prescription_approved)
                        <a href="{{ route('telemedicine.index') }}" class="btn-buy" style="background:#EF5350;">
                            🔒 Butuh Resep Dokter
                        </a>
                    @else
                        <button type="submit" class="btn-buy">+ Tambah ke Keranjang</button>
                    @endif
                </form>
            </div>

            <div style="margin-top: 15px;">
                <a href="{{ route('cart.index') }}" style="color: var(--primary-blue); font-weight: 600; text-decoration: none;">🛒 Lihat Keranjang →</a>
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
    </script>
</body>
</html>
