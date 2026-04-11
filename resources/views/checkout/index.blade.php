<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacare - Checkout</title>
    <style>
        /* Desktop-First Design - Checkout */
        :root {
            --primary-blue: #0076D6; 
            --bg-color: #F8F9FB;
            --text-main: #333333;
            --text-muted: #888888;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Arial, sans-serif; }
        body { background-color: var(--bg-color); color: var(--text-main); }
        
        .container { max-width: 1200px; margin: 0 auto; padding: 40px; }
        
        .top-bar { display: flex; align-items: center; margin-bottom: 40px; }
        .top-bar a { text-decoration: none; color: var(--text-main); font-size: 1.2rem; font-weight: 600; display: flex; align-items: center; gap: 10px; }
        .top-bar h1 { font-size: 2rem; margin-left: auto; margin-right: auto; }

        .checkout-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 40px;
        }

        .section { background: white; border-radius: 16px; padding: 30px; margin-bottom: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
        .section-title { font-size: 1.2rem; font-weight: 700; margin-bottom: 20px; border-bottom: 2px solid #F0F0F0; padding-bottom: 15px; }

        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 0.95rem; font-weight: 600; margin-bottom: 10px; }
        .form-control { width: 100%; padding: 15px; border: 2px solid #E0E0E0; border-radius: 12px; font-size: 1rem; transition: border-color 0.2s; }
        .form-control:focus { border-color: var(--primary-blue); outline: none; }

        .method-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .method-card { border: 2px solid #E0E0E0; border-radius: 12px; padding: 20px; display: flex; align-items: center; gap: 15px; cursor: pointer; transition: all 0.2s; }
        .method-card:hover { border-color: var(--primary-blue); }
        .method-card.active { border-color: var(--primary-blue); background: #E6F3FF; }
        .method-card input { transform: scale(1.3); }

        /* Summary Sidebar */
        .summary-box { background: white; border-radius: 16px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); position: sticky; top: 40px; }
        .summary-box h2 { font-size: 1.4rem; font-weight: 700; margin-bottom: 25px; }
        
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 1.05rem; }
        .summary-row.total { font-weight: 700; font-size: 1.4rem; border-top: 2px solid #F0F0F0; padding-top: 20px; margin-top: 10px; color: var(--primary-blue); margin-bottom: 30px; }

        .btn-pay { width: 100%; background: var(--primary-blue); color: white; padding: 18px; border-radius: 12px; border: none; font-size: 1.2rem; font-weight: 700; text-align: center; cursor: pointer; transition: background 0.2s; }
        .btn-pay:hover { background: #005FA3; }

        @media (max-width: 900px) { .checkout-grid { grid-template-columns: 1fr; } .method-grid { grid-template-columns: 1fr; } }

        /* Payment Modal Specific Styles */
        .pay-modal-grid { display: grid; grid-template-columns: 1.2fr 1fr; gap: 20px; }
        .pay-section { padding: 20px; border-radius: 12px; border: 1px solid #f0f0f0; margin-bottom: 20px; text-align: left; }
        .pay-title { font-weight: 800; font-size: 1rem; margin-bottom: 15px; color: var(--primary-blue); }
        .pay-row { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 0.9rem; }
        .pay-method-card { border: 2px solid #eee; border-radius: 12px; padding: 12px; cursor: pointer; transition: 0.2s; margin-bottom: 10px; }
        .pay-method-card:hover { border-color: var(--primary-blue); background: #f8fbff; }
        input[type="radio"]:checked + .pay-method-card { border-color: var(--primary-blue); background: #E6F3FF; }
        input[type="radio"] { display: none; }
        @media (max-width: 600px) { .pay-modal-grid { grid-template-columns: 1fr; } }
        @keyframes slideUp { from { transform: translateY(20px); opacity:0; } to { transform: translateY(0); opacity:1; } }
    </style>
</head>
<body>

<div class="container">
    <div class="top-bar">
        <a href="javascript:history.back()">❮ Kembali</a>
        <h1>Checkout Aman</h1>
        <div style="width: 100px;"></div> <!-- Spacer -->
    </div>

    <form id="checkoutForm" action="{{ route('checkout.store') }}" method="POST" enctype="multipart/form-data" class="checkout-grid">
        @csrf
        
        <div class="left-col">
            <div class="section">
                <h2 class="section-title">Informasi Pengiriman</h2>
                @if($addresses->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        @foreach($addresses as $addr)
                        <label style="display: block; cursor: pointer;">
                            <input type="radio" name="address_id" value="{{ $addr->id }}" {{ $addr->is_primary ? 'checked' : '' }} required style="display:none;" onchange="updateAddressStyle(this)">
                            <div class="address-card {{ $addr->is_primary ? 'active' : '' }}" style="padding: 20px; background: {{ $addr->is_primary ? '#E6F3FF' : '#FAFAFA' }}; border-radius: 12px; border: 2px solid {{ $addr->is_primary ? 'var(--primary-blue)' : '#eee' }}; transition: all 0.2s;">
                                <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                                    <strong>{{ $addr->label }}</strong>
                                    @if($addr->is_primary) <span style="color: var(--primary-blue); font-size: 0.75rem; font-weight: 800;">UTAMA</span> @endif
                                </div>
                                <p style="font-size: 0.95rem; color: var(--text-muted); line-height: 1.5;">{{ $addr->full_address }}</p>
                            </div>
                        </label>
                        @endforeach
                        <a href="{{ route('account.dashboard') }}" style="text-align: center; color: var(--primary-blue); font-size: 0.9rem; font-weight: 600; text-decoration: none; margin-top: 10px;">+ Kelola Alamat Lainnya</a>
                    </div>
                @else
                    <div style="text-align: center; padding: 20px; background: #FFF9DB; border-radius: 12px; border: 1px solid #FFD43B;">
                        <p style="margin-bottom: 15px;">Anda belum menambahkan alamat pengiriman.</p>
                        <a href="{{ route('account.dashboard') }}" class="btn-banner" style="display: inline-block; padding: 10px 20px; font-size: 0.9rem;">Tambah Alamat Sekarang</a>
                    </div>
                @endif
            </div>

            <script>
                function updateAddressStyle(radio) {
                    // Reset all cards
                    document.querySelectorAll('.address-card').forEach(card => {
                        card.style.borderColor = '#eee';
                        card.style.background = '#FAFAFA';
                    });
                    // Highlight selected
                    const card = radio.nextElementSibling;
                    card.style.borderColor = 'var(--primary-blue)';
                    card.style.background = '#E6F3FF';
                }
            </script>

            {{-- Pindah ke menu pembayaran selanjutnya --}}

            <div class="section">
                <h2 class="section-title" style="color: #d32f2f;">Validasi Resep Manual (Opsional)</h2>
                <div class="form-group">
                    <label class="form-label">Upload Surat Dokter Fisik (PDF/JPG/PNG)</label>
                    <input type="file" id="prescriptionInput" name="prescription" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 5px;">* Wajib jika Anda membeli obat berlogo merah (K) dan belum divalidasi dokter.</p>
                </div>
            </div>

            @php
                $hasPrescriptionItem = false;
                foreach($cartItems as $ci) {
                    if(isset($ci['requires_prescription']) && $ci['requires_prescription']) {
                        $hasPrescriptionItem = true;
                        break;
                    }
                }
            @endphp

            {{-- Pindah ke menu pembayaran selanjutnya --}}
        </div>

        <!-- Right Side Summary Box -->
        <div class="right-col">
            <div class="summary-box">
                <h2>Ringkasan Order</h2>
                <div style="margin-bottom: 25px; border-bottom: 1px dashed #E0E0E0; padding-bottom: 20px;">
                    @foreach($cartItems as $ci)
                    <div style="display: flex; gap: 15px; margin-bottom: 15px; align-items: center;">
                        <div style="width: 50px; height: 50px; background: #F0F4F8; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0;">
                            {{ $ci['requires_prescription'] ? '⚕️' : '💊' }}
                        </div>
                        <div style="flex:1;">
                            <div style="font-weight: 600; font-size: 0.95rem;">{{ $ci['name'] }}</div>
                            <div style="color: var(--text-muted); font-size: 0.85rem;">{{ $ci['qty'] }} x Rp {{ number_format($ci['price'], 0, ',', '.') }}</div>
                        </div>
                        <div style="font-weight: 700; font-size: 0.95rem;">Rp {{ number_format($ci['subtotal'], 0, ',', '.') }}</div>
                    </div>
                    @endforeach
                </div>

                <div class="summary-row">
                    <span>Subtotal Produk</span>
                    <span>Rp {{ number_format($subTotal, 0, ',', '.') }}</span>
                </div>
                <div class="summary-row" style="color: var(--text-muted); font-style: italic; font-size: 0.85rem; margin-top: -10px; margin-bottom: 20px;">
                    <span>* Ongkos kirim dihitung di halaman selanjutnya</span>
                </div>
                
                <div class="summary-row total">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($subTotal, 0, ',', '.') }}</span>
                </div>

                @if($user)
                <div style="background: #E6F3FF; border-radius: 12px; padding: 15px; margin-bottom: 20px; font-size: 0.95rem;">
                    <div style="font-weight: 700;">👤 {{ $user->name }}</div>
                    <div style="color: #555; margin-top: 5px;">💳 Limit Paylater: <strong>Rp {{ number_format($user->paylater_limit ?? 0, 0, ',', '.') }}</strong></div>
                </div>
                @endif

                <button type="submit" class="btn-pay">Lanjut ke Pembayaran</button>
                <div style="text-align: center; margin-top: 20px; color: var(--text-muted); font-size: 0.85rem;">
                    🔒 Pembayaran Anda dienkripsi dan aman.
                </div>
            </div>
        </div>
    </form>
</div>

    <!-- SweetAlert2 -->
    <script src="{{ asset('sweetalert/sweetalert2.all.min.js') }}"></script>

    <!-- PAYMENT MODAL -->
    <div id="paymentModal" style="display:none; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); z-index:99999; align-items:flex-start; justify-content:center; overflow-y:auto; padding: 40px 20px;">
        <div style="background:white; border-radius:24px; width:100%; max-width:800px; position:relative; animation: slideUp 0.3s ease-out;">
            <button onclick="toggleModal('paymentModal')" style="position: absolute; right: 20px; top: 20px; border: none; background: #f0f0f0; width: 32px; height: 32px; border-radius: 50%; cursor: pointer;">✕</button>
            <div style="padding: 30px;">
                <h3 style="margin-bottom:25px; font-size: 1.4rem;">💳 Konfirmasi Pembayaran</h3>
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 16px; padding: 20px; margin-bottom: 25px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <span style="color: #64748b; font-size: 0.9rem;">Status Pesanan</span>
                        <span style="background: #fef3c7; color: #92400e; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 800; border: 1px solid #fde68a;">MENUNGGU KONFIRMASI</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="color: #64748b; font-size: 0.9rem;">No Pesanan</span>
                        <span id="order-number" style="color: #0f172a; font-weight: 800; font-size: 1rem;">-</span>
                    </div>
                </div>

                <div id="pres-success-box" style="display:none; background:#E8F5E9; border:1px solid #C8E6C9; padding:15px; border-radius:12px; margin-bottom:20px; color:#2E7D32; font-size:0.9rem;">
                </div>
                <div id="pres-warning-box" style="display:none; background:#FFF3E0; border:1px solid #FFE0B2; padding:15px; border-radius:12px; margin-bottom:20px; color:#E65100; font-size:0.9rem;">
                </div>

                <form id="paymentForm" method="POST">
                    @csrf
                    <div class="pay-modal-grid">
                        <div class="left-col">
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
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                    <label><input type="radio" name="payment_method" value="qris" checked onchange="calcTotal()">
                                        <div class="pay-method-card">📱 <strong>QRIS</strong></div>
                                    </label>
                                    <label><input type="radio" name="payment_method" value="paylater" onchange="calcTotal()">
                                        <div class="pay-method-card">💳 <strong>Paylater</strong></div>
                                    </label>
                                    <label><input type="radio" name="payment_method" value="bank" onchange="calcTotal()">
                                        <div class="pay-method-card">🏦 <strong>Transfer Bank</strong></div>
                                    </label>
                                    <label><input type="radio" name="payment_method" value="ewallet" onchange="calcTotal()">
                                        <div class="pay-method-card">👛 <strong>E-Wallet</strong></div>
                                    </label>
                                    <label style="grid-column: span 2;"><input type="radio" name="payment_method" value="cod" onchange="calcTotal()">
                                        <div class="pay-method-card">🏠 <strong>Bayar di Tempat (COD)</strong></div>
                                    </label>
                                </div>

                                <div style="margin-top:20px; padding-top:15px; border-top:1px dashed #ddd;">
                                    <div class="pay-row"><span>Subtotal</span><span id="payment-subtotal">Rp 0</span></div>
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

    <!-- MODAL PEMBAYARAN (STEP 2) -->
    <div id="simPaymentModal" style="display:none; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.8); z-index:999999; align-items:center; justify-content:center; padding: 20px;">
        <div style="background:white; border-radius:24px; width:100%; max-width:480px; overflow:hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); position: relative;">
            <button onclick="backToSelection()" style="position: absolute; right: 15px; top: 15px; border: none; background: rgba(255,255,255,0.2); color: white; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; z-index: 10; font-weight: bold;">✕</button>
            <div id="sim-header" style="padding: 20px; text-align:center; color:white; font-weight:800; font-size:1.1rem;">
                KONFIRMASI PEMBAYARAN
            </div>
            
            <div id="sim-content" style="padding: 30px; text-align:center;">
                <!-- Content injected by JS -->
            </div>
            
            <div style="padding: 0 30px 30px;">
                <button onclick="finalizePayment()" style="width:100%; padding:15px; background:#2F9E44; color:white; border:none; border-radius:12px; font-weight:800; cursor:pointer; font-size:1rem; transition: background 0.2s;">
                    Selesaikan & Bayar Sekarang
                </button>
            </div>
        </div>
    </div>

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

        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const btn = form.querySelector('.btn-pay');
            const originalText = btn.innerText;

            const hasPresItem = {{ $hasPrescriptionItem ? 'true' : 'false' }};
            const fileInput = document.getElementById('prescriptionInput');

            if (hasPresItem && (!fileInput.files || fileInput.files.length === 0)) {
                Toast.fire({
                    icon: 'warning',
                    title: 'Pesanan mengandung obat keras. Mohon unggah foto resep dokter terlebih dahulu.'
                });
                return false;
            }

            btn.innerText = 'Memproses...';
            btn.disabled = true;

            const formData = new FormData(form);
            
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
                btn.innerText = originalText;
                btn.disabled = false;

                if (data.success) {
                    openPaymentModal(data.order);
                } else {
                    Toast.fire({ icon: 'error', title: data.error || 'Terjadi kesalahan.' });
                }
            })
            .catch(error => {
                btn.innerText = originalText;
                btn.disabled = false;
                Toast.fire({ icon: 'error', title: 'Terjadi kesalahan sistem.' });
            });
        });

        let currentSubtotal = 0;
        function openPaymentModal(data) {
            currentSubtotal = data.subtotal;
            document.getElementById('order-number').innerText = data.number;
            document.getElementById('payment-subtotal').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.subtotal);
            document.getElementById('paymentForm').action = data.url;
            calcTotal();

            const warnBox = document.getElementById('pres-warning-box');
            const successBox = document.getElementById('pres-success-box');
            const btn = document.getElementById('pay-confirm-btn');

            warnBox.style.display = 'none';
            successBox.style.display = 'none';
            btn.disabled = false;
            btn.style.opacity = '1';

            if (data.reqPres) {
                if (data.hasFile) {
                    successBox.style.display = 'block';
                    successBox.innerHTML = '✅ <strong>Resep Terverifikasi:</strong> Resep manual telah terlampir.';
                    btn.innerText = 'Konfirmasi & Bayar';
                } else {
                    warnBox.style.display = 'block';
                    btn.disabled = true;
                    btn.style.opacity = '0.5';
                    btn.innerText = 'Resep Diperlukan';
                }
            } else {
                btn.innerText = 'Konfirmasi & Bayar';
            }

            toggleModal('paymentModal');
        }

        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Just transition to Step 2 (Simulation/Details)
            const formData = new FormData(this);
            const data = {
                payment_method: formData.get('payment_method'),
                shipping_method: formData.get('shipping_method'),
                order_number: document.getElementById('order-number').innerText,
                grand_total: parseInt(document.getElementById('pay-total').innerText.replace(/[^0-9]/g, '')),
                paylater_limit: {{ Auth::user()->paylater_limit ?? 0 }}
            };

            toggleModal('paymentModal');
            openSimModal(data);
        });

        function openSimModal(data) {
            const content = document.getElementById('sim-content');
            const header = document.getElementById('sim-header');
            const method = data.payment_method;
            const totalStr = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.grand_total);
            
            let html = `
                <div style="margin-bottom: 20px; border-bottom: 1px dashed #eee; padding-bottom: 15px;">
                    <div style="font-size: 0.8rem; color: #666;">No Pesanan: <strong>${data.order_number}</strong></div>
                    <div style="font-size: 1.5rem; font-weight: 800; color: #111; margin-top: 5px;">${totalStr}</div>
                </div>
            `;

            if (method === 'qris') {
                header.style.background = '#e91e63';
                header.innerText = 'PEMBAYARAN QRIS';
                html += `
                    <div style="background: #f8fafc; padding: 20px; border-radius: 16px; margin-bottom: 20px;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=SIMULASI-PEMBAYARAN-${data.order_number}" style="width: 200px; height: 200px; margin: 0 auto; display: block; border: 4px solid white; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);">
                        <div style="margin-top: 15px; font-weight: 700; color: #475569;">Pindai kode QR untuk membayar</div>
                    </div>
                `;
            } else if (method === 'paylater') {
                header.style.background = '#0ea5e9';
                header.innerText = 'PEMBAYARAN PAYLATER';
                const nextLimit = data.paylater_limit - data.grand_total;
                html += `
                    <div style="background: #f0f9ff; padding: 20px; border-radius: 16px; margin-bottom: 20px; text-align: left;">
                        <div style="margin-bottom: 10px; color: #0369a1; font-weight: 600;">Ringkasan Limit:</div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <span style="color: #64748b;">Tagihan Pesanan</span>
                            <span style="font-weight: 700; color: #ef4444;">- ${totalStr}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; border-top: 1px solid #bae6fd; padding-top: 8px;">
                            <span style="color: #64748b;">Estimasi Sisa Limit</span>
                            <span style="font-weight: 700; color: #10b981;">Rp ${new Intl.NumberFormat('id-ID').format(nextLimit)}</span>
                        </div>
                    </div>
                `;
            } else if (method === 'bank') {
                header.style.background = '#4338ca';
                header.innerText = 'TRANSFER BANK';
                html += `
                    <p style="color: #64748b; margin-bottom: 15px;">Pilih Bank Tujuan:</p>
                    <div class="sim-options-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 20px;">
                        <button type="button" onclick="selectSimOption(this, 'bank', 'BCA')" style="padding: 12px; border: 1px solid #ddd; background: white; border-radius: 8px; font-weight: 700; font-size: 0.8rem; cursor: pointer;">BCA</button>
                        <button type="button" onclick="selectSimOption(this, 'bank', 'MANDIRI')" style="padding: 12px; border: 1px solid #ddd; background: white; border-radius: 8px; font-weight: 700; font-size: 0.8rem; cursor: pointer;">MANDIRI</button>
                        <button type="button" onclick="selectSimOption(this, 'bank', 'BNI')" style="padding: 12px; border: 1px solid #ddd; background: white; border-radius: 8px; font-weight: 700; font-size: 0.8rem; cursor: pointer;">BNI</button>
                        <button type="button" onclick="selectSimOption(this, 'bank', 'BRI')" style="padding: 12px; border: 1px solid #ddd; background: white; border-radius: 8px; font-weight: 700; font-size: 0.8rem; cursor: pointer;">BRI</button>
                    </div>
                    <div id="sim-detail-box" style="display:none; background: #f8fafc; padding: 20px; border-radius: 16px; margin-bottom: 20px; border: 2px solid #e2e8f0; animation: fadeIn 0.3s ease-out;">
                    </div>
                `;
            } else if (method === 'ewallet') {
                header.style.background = '#6366f1';
                header.innerText = 'PEMBAYARAN E-WALLET';
                html += `
                    <p style="color: #64748b; margin-bottom: 15px;">Pilih Dompet Digital:</p>
                    <div class="sim-options-grid" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; margin-bottom: 20px;">
                        <button type="button" onclick="selectSimOption(this, 'ewallet', 'GOPAY')" style="padding: 10px; border: 1px solid #ddd; background: white; border-radius: 8px; font-weight: 700; font-size: 0.7rem; cursor: pointer;">GOPAY</button>
                        <button type="button" onclick="selectSimOption(this, 'ewallet', 'OVO')" style="padding: 10px; border: 1px solid #ddd; background: white; border-radius: 8px; font-weight: 700; font-size: 0.7rem; cursor: pointer;">OVO</button>
                        <button type="button" onclick="selectSimOption(this, 'ewallet', 'DANA')" style="padding: 10px; border: 1px solid #ddd; background: white; border-radius: 8px; font-weight: 700; font-size: 0.7rem; cursor: pointer;">DANA</button>
                    </div>
                    <div id="sim-detail-box" style="display:none; background: #f5f3ff; padding: 20px; border-radius: 16px; margin-bottom: 20px; border: 2px dashed #818cf8; animation: fadeIn 0.3s ease-out;">
                    </div>
                `;
            } else {
                header.style.background = '#64748b';
                header.innerText = 'BAYAR DI TEMPAT (COD)';
                html += `
                    <div style="background: #f1f5f9; padding: 20px; border-radius: 16px; margin-bottom: 20px;">
                        <p style="font-weight: 700; color: #334155;">Pesanan akan segera dikirim</p>
                        <p style="font-size: 0.85rem; color: #64748b;">Mohon siapkan uang pas saat kurir tiba.</p>
                    </div>
                `;
            }

            content.innerHTML = html;
            document.getElementById('simPaymentModal').style.display = 'flex';
        }

        function backToSelection() {
            document.getElementById('simPaymentModal').style.display = 'none';
            toggleModal('paymentModal');
        }

        function selectSimOption(btn, type, name) {
            // Reset visual buttons
            const grid = btn.parentElement;
            grid.querySelectorAll('button').forEach(b => {
                b.style.border = '1px solid #ddd';
                b.style.background = 'white';
                b.style.color = '#334155';
            });

            // Set active button
            btn.style.border = '2px solid #4338ca';
            btn.style.background = '#f0f4ff';
            btn.style.color = '#4338ca';

            // Show and populate detail box
            const detailBox = document.getElementById('sim-detail-box');
            detailBox.style.display = 'block';
            
            if (type === 'bank') {
                const va = '889' + Math.floor(Math.random() * 90000000) + 10000000;
                detailBox.innerHTML = `
                    <div style="font-size: 0.8rem; color: #64748b; margin-bottom: 10px;">Virtual Account <strong>${name}</strong>:</div>
                    <div style="font-size: 1.6rem; font-weight: 800; color: #111; letter-spacing: 2px; font-family: monospace;">${va}</div>
                    <div style="color: #2F9E44; font-weight: 700; font-size: 0.8rem; margin-top: 10px; cursor: pointer;">
                        Salin No. VA <i class="far fa-copy"></i>
                    </div>
                `;
            } else {
                const id = '08' + Math.floor(Math.random() * 90) + ' **** ' + Math.floor(Math.random() * 8999 + 1000);
                detailBox.innerHTML = `
                    <div style="font-size: 0.8rem; color: #64748b; margin-bottom: 10px;">Konfirmasi di Apps <strong>${name}</strong>:</div>
                    <div style="font-size: 1.3rem; font-weight: 700; color: #111;">${id}</div>
                    <p style="font-size: 0.75rem; color: #64748b; margin-top: 8px;">Silakan buka aplikasi ${name} Anda untuk konfirmasi.</p>
                `;
            }
        }

        function finalizePayment() {
            const btn = event.target;
            const originalText = btn.innerText;
            btn.innerText = 'Memproses Pesanan...';
            btn.disabled = true;

            const form = document.getElementById('paymentForm');
            const formData = new FormData(form);

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
                    window.location.href = data.redirect_url;
                } else {
                    btn.innerText = originalText;
                    btn.disabled = false;
                    Toast.fire({ icon: 'error', title: data.error || 'Terjadi kesalahan.' });
                }
            })
            .catch(error => {
                btn.innerText = originalText;
                btn.disabled = false;
                Toast.fire({ icon: 'error', title: 'Terjadi kesalahan sistem.' });
            });
        }

        function calcTotal() {
            const ship = document.querySelector('input[name="shipping_method"]:checked').value;
            const cost = ship === 'instant' ? 15000 : 10000;
            const total = currentSubtotal + cost;
            document.getElementById('pay-shipping').innerText = 'Rp ' + cost.toLocaleString('id-ID');
            document.getElementById('pay-total').innerText = 'Rp ' + total.toLocaleString('id-ID');
        }

        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.style.display = (modal.style.display === 'none' || modal.style.display === '') ? 'flex' : 'none';
        }

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

        @if ($errors->any())
            Toast.fire({
                icon: 'error',
                title: '{{ $errors->first() }}'
            });
        @endif
    </script>
</body>
</html>
