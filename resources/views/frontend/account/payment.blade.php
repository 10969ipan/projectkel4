<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Pesanan - Pharmacare</title>
    <style>
        :root { --primary-blue: #0076D6; --bg: #F4F7FA; --muted: #636E72; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
        body { background: var(--bg); color: #2D3436; }

        .container { max-width: 900px; margin: 40px auto; padding: 0 20px; }

        .header { display: flex; align-items: center; gap: 20px; margin-bottom: 40px; }
        .header a { text-decoration: none; color: var(--muted); font-weight: 600; font-size: 1.1rem; }
        .header h1 { font-size: 1.8rem; font-weight: 800; }

        .grid { display: grid; grid-template-columns: 1fr 380px; gap: 30px; }

        .card { background: white; border-radius: 20px; padding: 30px; box-shadow: 0 8px 25px rgba(0,0,0,0.05); }

        .section-title { font-size: 1.1rem; font-weight: 800; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #f0f0f0; }

        /* Order Summary */
        .order-meta { background: #f8fafe; border-radius: 12px; padding: 20px; margin-bottom: 25px; }
        .order-meta strong { color: var(--primary-blue); font-size: 1.1rem; }
        .order-meta p { color: var(--muted); font-size: 0.9rem; margin-top: 5px; }

        .items-list { display: flex; flex-direction: column; gap: 12px; margin-bottom: 25px; }
        .item-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f5f5f5; }
        .item-row:last-child { border-bottom: none; }
        .item-name { font-weight: 600; }
        .item-qty { font-size: 0.85rem; color: var(--muted); margin-top: 3px; }
        .item-price { font-weight: 700; color: var(--primary-blue); }

        .total-row { display: flex; justify-content: space-between; padding: 15px 0; border-top: 2px solid #f0f0f0; font-size: 1.1rem; }
        .total-row.final { font-size: 1.35rem; font-weight: 800; color: var(--primary-blue); padding-top: 15px; }

        /* Payment Methods */
        .method-list { display: flex; flex-direction: column; gap: 12px; margin-bottom: 25px; }
        .method-label { cursor: pointer; }
        .method-card { display: flex; align-items: center; gap: 15px; padding: 18px 20px; border: 2px solid #eee; border-radius: 14px; transition: all 0.2s; }
        .method-card:hover { border-color: var(--primary-blue); background: #f8fbff; }
        input[type="radio"]:checked + .method-card { border-color: var(--primary-blue); background: #E6F3FF; }
        input[type="radio"] { display: none; }
        .method-icon { font-size: 1.8rem; width: 40px; text-align: center; }
        .method-name { font-weight: 700; font-size: 1rem; }
        .method-desc { font-size: 0.85rem; color: var(--muted); margin-top: 3px; }

        .paylater-limit { margin-left: auto; background: #E6F3FF; color: var(--primary-blue); padding: 5px 12px; border-radius: 20px; font-size: 0.8rem; font-weight: 800; }

        .btn-pay { width: 100%; background: #2F9E44; color: white; padding: 18px; border-radius: 14px; border: none; font-size: 1.1rem; font-weight: 800; cursor: pointer; transition: background 0.2s; margin-top: 10px; }
        .btn-pay:hover { background: #237032; }
        .btn-pay:disabled { background: #ccc; cursor: not-allowed; }

        .security-note { text-align: center; margin-top: 15px; color: var(--muted); font-size: 0.85rem; }

        @media (max-width: 700px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <a href="{{ route('account.orders') }}">❮</a>
        <h1>💳 Konfirmasi Pembayaran</h1>
    </div>

    @php
        $requiresPrescription = false;
        foreach ($order->items as $oi) {
            if ($oi->item && $oi->item->requires_prescription) {
                $requiresPrescription = true;
                break;
            }
        }
        $hasPrescription = !empty($order->prescription_path) || Auth::user()->is_prescription_approved;
        $isBlocked = $requiresPrescription && !$hasPrescription;
    @endphp

    <form action="{{ route('account.orders.pay.post', $order->id) }}" method="POST" id="paymentForm">
        @csrf
        <div class="grid">
            <!-- LEFT: Order Summary & Shipping -->
            <div class="card">
                <div class="order-meta">
                    <strong>#{{ $order->order_number }}</strong>
                    <p>{{ $order->created_at->format('d M Y, H:i') }}</p>
                    @if($order->address)
                    <p style="margin-top: 8px;">📍 {{ $order->address->label }}: {{ $order->address->full_address }}</p>
                    @endif
                </div>

                @if($requiresPrescription)
                <div style="background: {{ $hasPrescription ? '#E6FCF5' : '#FFF5F5' }}; border: 1px solid {{ $hasPrescription ? '#20C997' : '#FF8787' }}; padding: 15px; border-radius: 12px; margin-bottom: 25px; display: flex; align-items: flex-start; gap: 12px;">
                    <div style="font-size: 1.5rem;">{{ $hasPrescription ? '✅' : '⚠️' }}</div>
                    <div>
                        <strong style="display: block; color: {{ $hasPrescription ? '#087F5B' : '#C92A2A' }};">Validasi Resep</strong>
                        <p style="font-size: 0.85rem; margin-top: 4px;">
                            @if($hasPrescription)
                                Resep Anda telah terlampir atau akun Anda sudah terverifikasi Dokter. Pembayaran dapat dilanjutkan.
                            @else
                                Pesanan ini mengandung obat keras. Anda belum mengunggah resep atau mendapatkan persetujuan Dokter. <strong>Pembayaran diblokir sementara.</strong>
                            @endif
                        </p>
                    </div>
                </div>
                @endif

                <div class="section-title">📦 Informasi Produk</div>
                <div class="items-list">
                    @foreach($order->items as $oi)
                    <div class="item-row">
                        <div>
                            <div class="item-name">{{ $oi->item->name ?? 'Item dihapus' }}</div>
                            <div class="item-qty">{{ $oi->quantity }}x @ Rp {{ number_format($oi->price, 0, ',', '.') }}</div>
                        </div>
                        <div class="item-price">Rp {{ number_format($oi->sub_total, 0, ',', '.') }}</div>
                    </div>
                    @endforeach
                </div>

                <div class="section-title">🚚 Pilih Durasi Pengiriman</div>
                
                <input type="hidden" name="shipping_cost" id="input-shipping-cost" value="15000">
                <div style="background: #f8fafc; padding: 12px 15px; border-radius: 12px; margin-bottom: 15px; border: 1px solid #f1f5f9; display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 1.1rem;">📍</span>
                    <div style="font-size: 0.8rem; color: #64748b;">Tujuan: <strong style="color: #1e293b;">{{ $order->address->full_address ?? 'Alamat tidak ditemukan' }}</strong></div>
                </div>

                <div style="display: flex; gap: 10px; margin-bottom: 15px;">
                    <select id="ro-courier" class="form-control" style="flex: 1; padding: 12px; font-size: 0.9rem; border: 1.5px solid #e2e8f0; border-radius: 12px; background: #fff; height: auto;">
                        <option value="">-- Pilih Kurir --</option>
                        <option value="jne">JNE (Jalur Nugraha Ekakurir)</option>
                        <option value="pos">POS Indonesia</option>
                        <option value="tiki">TIKI (Citra Van Titipan Kilat)</option>
                    </select>
                    <button type="button" id="btn-cek-ongkir" onclick="cekOngkirPayment()" style="padding: 10px 15px; background: #0076D6; color: white; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; transition: 0.3s; font-size: 0.75rem;">
                        Cek Tarif
                    </button>
                </div>

                <div id="ro-results" class="method-list">
                    <label class="method-label">
                        <input type="radio" name="shipping_method" value="instant" checked onchange="updatePrices()">
                        <div class="method-card">
                            <div class="method-icon">⚡</div>
                            <div>
                                <div class="method-name">Instant Delivery</div>
                                <div class="method-desc">Estimasi Tiba: 2 Jam (Rp 15.000)</div>
                            </div>
                        </div>
                    </label>
                    <div style="text-align:center; font-size:0.8rem; color:#94a3b8; padding: 10px 0;">Atau pilih kurir ekspedisi di atas.</div>
                </div>
            </div>

            <!-- RIGHT: Payment Method & Total -->
            <div>
                <div class="card" style="margin-bottom: 20px;">
                    <div class="section-title">💰 Ringkasan Pembayaran</div>
                    <div class="total-row">
                        <span>Subtotal Produk</span>
                        <span>Rp {{ number_format($order->sub_total, 0, ',', '.') }}</span>
                    </div>
                    <div class="total-row">
                        <span>Ongkos Kirim</span>
                        <span id="shippingDisplay">Rp 15.000</span>
                    </div>
                    <div class="total-row final">
                        <span>Total Tagihan</span>
                        <span id="grandTotalDisplay">Rp {{ number_format($order->sub_total + 15000, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="card">
                    <div class="section-title">🏦 Metode Pembayaran</div>

                    <div class="method-list">
                        <label class="method-label">
                            <input type="radio" name="payment_method" value="qris" checked>
                            <div class="method-card">
                                <div class="method-icon">📱</div>
                                <div>
                                    <div class="method-name">QRIS</div>
                                    <div class="method-desc">Otomatis / Real-time</div>
                                </div>
                            </div>
                        </label>

                        <label class="method-label">
                            <input type="radio" name="payment_method" value="paylater">
                            <div class="method-card">
                                <div class="method-icon">💳</div>
                                <div>
                                    <div class="method-name">Paylater</div>
                                    <div class="method-desc">Pakai Limit Anda</div>
                                </div>
                            </div>
                        </label>
                    </div>

                    <button type="submit" class="btn-pay" {{ $isBlocked ? 'disabled' : '' }}>
                        🎉 Konfirmasi & Bayar
                    </button>
                    @if($isBlocked)
                        <p style="color: #C92A2A; font-size: 0.8rem; text-align: center; margin-top: 10px; font-weight: 700;">
                            Resep dokter diperlukan untuk melanjutkan
                        </p>
                    @endif
                    <p class="security-note">🔒 Transaksi Aman & Terenkripsi</p>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="{{ asset('sweetalert/sweetalert2.all.min.js') }}"></script>
<script>
    const subtotal = {{ $order->sub_total }};
    const shippingDisplay = document.getElementById('shippingDisplay');
    const grandTotalDisplay = document.getElementById('grandTotalDisplay');

    function updatePrices() {
        const checkedInput = document.querySelector('input[name="shipping_method"]:checked');
        if (!checkedInput) return;

        let cost = 15000;
        const method = checkedInput.value;

        // If it's a RajaOngkir result, it should have a data-cost attribute
        if (checkedInput.dataset.cost) {
            cost = parseInt(checkedInput.dataset.cost);
        } else {
            // Fallback for hardcoded
            cost = method === 'instant' ? 15000 : 10000;
        }

        document.getElementById('input-shipping-cost').value = cost;
        shippingDisplay.innerText = 'Rp ' + cost.toLocaleString('id-ID');
        grandTotalDisplay.innerText = 'Rp ' + (subtotal + cost).toLocaleString('id-ID');
    }

    async function cekOngkirPayment() {
        const courier = document.getElementById('ro-courier').value;
        const btn = document.getElementById('btn-cek-ongkir');
        const resultsDiv = document.getElementById('ro-results');
        const destId = "{{ $order->address->city_id ?? 151 }}"; // Fallback to Jakarta Barat if missing

        if (!courier) {
            Swal.fire('Info', 'Silakan pilih kurir terlebih dahulu.', 'info');
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span style="display:inline-block; animation: spin 1s linear infinite;">↻</span>';
        resultsDiv.innerHTML = '<div style="text-align:center; padding: 20px;"><div class="spinner"></div><p style="font-size:0.8rem; color:#64748b; margin-top:10px;">Mencari tarif...</p></div>';

        try {
            const response = await fetch('/cost', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    origin: '151', // Jakarta Barat (Apotek)
                    destination: destId,
                    weight: 1000,
                    courier: courier
                })
            });

            const data = await response.json();
            btn.disabled = false;
            btn.innerHTML = 'Cek Tarif';

            if (data.rajaongkir && data.rajaongkir.status.code === 200) {
                const costs = data.rajaongkir.results[0].costs;
                if (costs.length === 0) {
                    resultsDiv.innerHTML = '<div style="text-align:center; font-size:0.85rem; color:#ef4444; padding: 20px 0;">Layanan tidak tersedia untuk kurir ini ke lokasi Anda.</div>';
                    return;
                }

                let html = '';
                costs.forEach((c, idx) => {
                    const costVal = c.cost[0].value;
                    const etd = c.cost[0].etd ? c.cost[0].etd + ' Hari' : 'Segera';
                    const methodValue = courier.toUpperCase() + ' - ' + c.service;
                    
                    html += `
                        <label class="method-label">
                            <input type="radio" name="shipping_method" value="${methodValue}" data-cost="${costVal}" onchange="updatePrices()">
                            <div class="method-card">
                                <div class="method-icon">📦</div>
                                <div>
                                    <div class="method-name">${courier.toUpperCase()} - ${c.service}</div>
                                    <div class="method-desc">Estimasi: ${etd} (Rp ${costVal.toLocaleString('id-ID')})</div>
                                </div>
                            </div>
                        </label>
                    `;
                });
                
                // Add back Instant as option
                html += `
                    <div style="border-top: 1px dashed #e2e8f0; margin: 10px 0; padding-top: 10px;">
                        <label class="method-label">
                            <input type="radio" name="shipping_method" value="instant" onchange="updatePrices()">
                            <div class="method-card">
                                <div class="method-icon">⚡</div>
                                <div>
                                    <div class="method-name">Instant Delivery</div>
                                    <div class="method-desc">Estimasi Tiba: 2 Jam (Rp 15.000)</div>
                                </div>
                            </div>
                        </label>
                    </div>
                `;

                resultsDiv.innerHTML = html;
            } else {
                throw new Error('Gagal mengambil data');
            }
        } catch (error) {
            btn.disabled = false;
            btn.innerHTML = 'Cek Tarif';
            resultsDiv.innerHTML = '<div style="text-align:center; font-size:0.85rem; color:#ef4444; padding: 20px 0;">Gagal memuat tarif. Silakan coba kurir lain.</div>';
        }
    }

    const Toast = Swal.mixin({
        toast: true, position: 'top-end',
        showConfirmButton: false, timer: 3000, timerProgressBar: true
    });
    @if(session('error'))
        Toast.fire({ icon: 'error', title: '{{ session('error') }}' });
    @endif
    @if($errors->any())
        Toast.fire({ icon: 'error', title: '{{ $errors->first() }}' });
    @endif
</script>
</body>
</html>
