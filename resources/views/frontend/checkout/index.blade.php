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
        [x-cloak] { display: none !important; }
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

    <!-- Essential Dependencies -->
    <script src="{{ asset('assets/vendor/alpinejs/alpine.min.js') }}" defer></script>
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/sweetalert2.min.css') }}">
    <script src="{{ asset('assets/vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
</head>
<body>

    <div class="container" x-data='{
        shippingCost: {{ $shippingCost }},
        subTotalGross: {{ $subTotalGross }},
        selectedIds: [
            @foreach($cartItems as $ci)
                @if(($ci["type"] ?? "once") === "subscription") "{{ $ci["id"] }}", @endif
            @endforeach
        ],
        items: @json(array_values($cartItems)),
        
        get totalDiscount() {
            let discount = 0;
            this.selectedIds.forEach(id => {
                const item = this.items.find(i => i.id == id);
                if (item) {
                    // Logic: Discount 10% based on original item price
                    let originalPrice = item.type === "subscription" ? (item.price / 0.9) : item.price;
                    discount += (originalPrice * item.qty * 0.1);
                }
            });
            return discount;
        },

        get grandTotal() {
            return this.subTotalGross - this.totalDiscount + this.shippingCost;
        },

        isSubscribed(id) {
            return this.selectedIds.includes(id.toString());
        }
    }'>
    <div class="top-bar">
        <a href="javascript:history.back()" style="display: flex; align-items: center; gap: 5px; font-weight: 700; color: var(--text-main); text-decoration: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Kembali
        </a>
        <h1 style="font-size: 1.2rem; font-weight: 700; color: var(--text-muted); opacity: 0.8;">Checkout Pesanan</h1>
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
                        <a href="{{ route('account.profile', ['tab' => 'addresses', 'add_address' => 1]) }}" style="text-align: center; color: var(--primary-blue); font-size: 0.9rem; font-weight: 600; text-decoration: none; margin-top: 10px;">+ Kelola Alamat Lainnya</a>
                    </div>
                @else
                    <div style="text-align: center; padding: 20px; background: #FFF9DB; border-radius: 12px; border: 1px solid #FFD43B;">
                        <p style="margin-bottom: 15px;">Anda belum menambahkan alamat pengiriman.</p>
                        <a href="{{ route('account.profile', ['tab' => 'addresses', 'add_address' => 1]) }}" class="btn-banner" style="display: inline-block; padding: 10px 20px; font-size: 0.9rem;">Tambah Alamat Sekarang</a>
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

            <div class="section">
                <h2 class="section-title">Ringkasan Order</h2>
                <div style="margin-bottom: 25px;">
                    @foreach($cartItems as $ci)
                    <div style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #f8fafc;">
                        <div style="display: flex; gap: 20px; align-items: center; margin-bottom: 15px;">
                            <div style="width: 70px; height: 70px; background: #F0F4F8; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden; border: 1px solid #edf2f7;">
                                @if(!empty($ci['image_path']))
                                    <img src="{{ asset($ci['image_path']) }}" alt="{{ $ci['name'] }}" style="width:100%; height:100%; object-fit:contain;">
                                @else
                                    <span style="font-size: 1.5rem; opacity: 0.5;">💊</span>
                                @endif
                            </div>
                            <div style="flex:1;">
                                <div style="font-weight: 700; font-size: 1.1rem; color: var(--text-main);">{{ $ci['name'] }}</div>
                                <div style="color: var(--text-muted); font-size: 0.95rem; margin-top: 4px;">
                                    <span id="item-qty-{{ $ci['id'] }}" style="font-weight: 700; color: var(--text-main);">{{ $ci['qty'] }}</span> x 
                                    <span id="item-sub-{{ $ci['id'] }}" data-price="{{ $ci['price'] }}">Rp {{ number_format($ci['price'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div :style="isSubscribed({{ $ci['id'] }}) ? 'text-decoration: line-through; color: #94a3b8; font-size: 0.85rem;' : 'font-weight: 800; font-size: 1.1rem; color: var(--text-main);'">
                                    Rp {{ number_format($ci['subtotal'], 0, ',', '.') }}
                                </div>
                                <div x-show="isSubscribed({{ $ci['id'] }})" style="color: #059669; font-weight: 800; font-size: 1.1rem;" x-cloak>
                                    Rp <span x-text="new Intl.NumberFormat('id-ID').format({{ $ci['subtotal'] }} * 0.9)"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Subscription Toggle in Checkout -->
                        <div style="background: #f8fafc; border-radius: 12px; padding: 15px; border: 1px solid #edf2f7;">
                            <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 0.9rem; font-weight: 700; color: #475569;">
                                <input type="checkbox" name="is_subscription[{{ $ci['id'] }}]" x-model="selectedIds" value="{{ $ci['id'] }}" style="width: 18px; height: 18px; accent-color: var(--primary-blue);">
                                📦 Jadikan Langganan & Hemat 10%
                            </label>
                            <div x-show="isSubscribed({{ $ci['id'] }})" style="margin-top: 12px; padding-top: 12px; border-top: 1px dashed #cbd5e1; display: flex; align-items: center; gap: 15px;" x-cloak>
                                <span style="font-size: 0.8rem; color: #64748b; font-weight: 500;">Frekuensi Pengiriman:</span>
                                <select name="subscription_interval[{{ $ci['id'] }}]" style="padding: 6px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 0.85rem; font-weight: 700; background: white; cursor: pointer;">
                                    <option value="7">Setiap 7 Hari</option>
                                    <option value="14">Setiap 14 Hari</option>
                                    <option value="30" selected>Setiap 30 Hari</option>
                                    <option value="60">Setiap 60 Hari</option>
                                    <option value="90">Setiap 90 Hari</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Pindah ke menu pembayaran selanjutnya --}}

            @if($hasPrescriptionItem)
            <div class="section" style="border: 2px solid #EF5350; background: #FFF5F5;">
                <h2 class="section-title" style="color: #D32F2F; border-bottom-color: #FEB2B2;">🚨 Verifikasi Resep Dokter (Wajib)</h2>
                <div class="form-group">
                    <p style="margin-bottom: 15px; color: #742A2A; font-size: 0.95rem; line-height: 1.5;">
                        Pesanan Anda mengandung <strong>Obat Keras</strong>. Berdasarkan regulasi farmasi, Anda wajib mengunggah foto resep dokter yang asli untuk melanjutkan ke tahap pembayaran.
                    </p>
                    <label class="form-label" style="color: #D32F2F;">Upload Foto Resep (PDF/JPG/PNG)</label>
                    <input type="file" id="prescriptionInput" name="prescription" class="form-control" accept=".pdf,.jpg,.jpeg,.png" style="border-color: #FEB2B2;">
                    <div style="margin-top: 10px; font-size: 0.85rem; color: #C53030; font-weight: 600;">
                        <i class="fas fa-info-circle"></i> Pastikan nama pasien dan nama obat terlihat jelas.
                    </div>
                </div>
            </div>
            @else
            <div class="section">
                <h2 class="section-title">Validasi Resep Manual (Opsional)</h2>
                <div class="form-group">
                    <label class="form-label">Upload Surat Dokter Fisik (Jika Ada)</label>
                    <input type="file" id="prescriptionInput" name="prescription" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 5px;">Unggah jika Anda memiliki resep untuk obat yang Anda beli.</p>
                </div>
            </div>
            @endif


            {{-- Pindah ke menu pembayaran selanjutnya --}}
        </div>

        <!-- Right Side Summary Box -->
        <div class="right-col">
            <div class="summary-box">
                <h2>Ringkasan Pembayaran</h2>

                <div class="summary-row">
                    <span>Subtotal Produk</span>
                    <span x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(subTotalGross)">Rp {{ number_format($subTotalGross, 0, ',', '.') }}</span>
                </div>
                <div x-show="totalDiscount > 0" class="summary-row" style="color: #059669; font-weight: 700;" x-cloak>
                    <span>Diskon Langganan (10%)</span>
                    <span x-text="'- Rp ' + new Intl.NumberFormat('id-ID').format(totalDiscount)">- Rp 0</span>
                </div>
                <div class="summary-row" style="color: var(--text-muted); font-style: italic; font-size: 0.85rem; margin-top: 5px; margin-bottom: 20px;">
                    <span>* Ongkos kirim dihitung di tahap selanjutnya</span>
                </div>
                
                <div class="summary-row total">
                    <span>Total Bayar</span>
                    <span x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(subTotalGross - totalDiscount)">Rp {{ number_format($subTotal + $shippingCost, 0, ',', '.') }}</span>
                    <input type="hidden" id="pay-total-hidden" :value="subTotalGross - totalDiscount">
                </div>

                @if($user)
                <div style="background: #E6F3FF; border-radius: 12px; padding: 15px; margin-bottom: 20px; font-size: 0.95rem;">
                    <div style="font-weight: 700; color: var(--primary-blue); margin-bottom: 8px;"><i class="fas fa-user-circle"></i> {{ $user->name }}</div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px; color: #475569;">
                        <span>Limit Paylater:</span>
                        <strong style="color: #1e293b;">Rp {{ number_format($user->paylater_limit ?? 0, 0, ',', '.') }}</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between; color: #475569;">
                        <span>Saldo Dompet:</span>
                        <strong style="color: #059669;">Rp {{ number_format($user->wallet_balance ?? 0, 0, ',', '.') }}</strong>
                    </div>
                </div>
                @endif

                <button type="submit" class="btn-pay">Lanjut ke Pembayaran</button>
                <div style="text-align: center; margin-top: 20px; color: var(--text-muted); font-size: 0.85rem;">
                    Pembayaran Anda dienkripsi dan aman.
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
                <h3 style="margin-bottom:25px; font-size: 1.4rem;">Konfirmasi Pembayaran</h3>
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
                                    <div class="pay-method-card"><strong>Instant Delivery</strong><br><span style="font-size:0.8rem; color:#666;">2 Jam Sampai (Rp 15.000)</span></div>
                                </label>
                                <label><input type="radio" name="shipping_method" value="regular" onchange="calcTotal()">
                                    <div class="pay-method-card"><strong>JNE / J&T Reguler</strong><br><span style="font-size:0.8rem; color:#666;">2-3 Hari Kerja (Rp 10.000)</span></div>
                                </label>
                            </div>
                        </div>

                        <div class="right-col">
                            <div class="pay-section" style="background:#f8f9fa;">
                                <div class="pay-title">🏦 Metode Pembayaran</div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                    <label><input type="radio" name="payment_method" value="qris" checked onchange="calcTotal()">
                                        <div class="pay-method-card"><strong><i class="fas fa-qrcode"></i> QRIS</strong></div>
                                    </label>
                                    <label><input type="radio" name="payment_method" value="wallet" onchange="calcTotal()">
                                        <div class="pay-method-card">
                                            <strong><i class="fas fa-wallet"></i> Saldo Dompet</strong>
                                        </div>
                                    </label>
                                    <label><input type="radio" name="payment_method" value="paylater" onchange="calcTotal()">
                                        <div class="pay-method-card"><strong><i class="fas fa-clock"></i> Paylater</strong></div>
                                    </label>
                                    <label><input type="radio" name="payment_method" value="bank" onchange="calcTotal()">
                                        <div class="pay-method-card"><strong><i class="fas fa-university"></i> Transfer Bank</strong></div>
                                    </label>
                                    <label><input type="radio" name="payment_method" value="ewallet" onchange="calcTotal()">
                                        <div class="pay-method-card"><strong><i class="fas fa-mobile-alt"></i> E-Wallet</strong></div>
                                    </label>
                                    <label style="grid-column: span 2;"><input type="radio" name="payment_method" value="cod" onchange="calcTotal()">
                                        <div class="pay-method-card"><strong><i class="fas fa-hand-holding-usd"></i> Bayar di Tempat (COD)</strong></div>
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
            <button onclick="backToSelection()" style="position: absolute; right: 20px; top: 20px; border: none; background: #f1f5f9; color: #64748b; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; z-index: 10; font-weight: bold; transition: all 0.2s;">✕</button>
            <div id="sim-header" style="padding: 25px 30px; text-align:center; color:#1e293b; font-weight:800; font-size:1.2rem; border-bottom: 1px solid #f1f5f9;">
                KONFIRMASI PEMBAYARAN
            </div>
            
            <div id="sim-content" style="padding: 30px; text-align:center;">
                <!-- Content injected by JS -->
            </div>
            
            <div style="padding: 0 30px 30px;">
                <button id="sim-footer-btn" onclick="finalizePayment()" style="width:100%; padding:15px; background:#2F9E44; color:white; border:none; border-radius:12px; font-weight:800; cursor:pointer; font-size:1rem; transition: background 0.2s;">
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
            iconColor: '#0076D6',
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

            // OPTIMISTIC LOADING: Open modal immediately with "Preparing" state
            const modalData = {
                number: 'ORD-PROSES...',
                subtotal: parseFloat(document.getElementById('pay-total-hidden')?.value || 0), // Use subtotal from Alpine if possible
                loading: true
            };
            openPaymentModal(modalData);

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
                    // Update the already open modal with real data
                    openPaymentModal(data.order);
                } else {
                    toggleModal('paymentModal'); // Hide modal on error
                    Toast.fire({ icon: 'error', title: data.error || 'Terjadi kesalahan.' });
                }
            })
            .catch(error => {
                btn.innerText = originalText;
                btn.disabled = false;
                toggleModal('paymentModal');
                Toast.fire({ icon: 'error', title: 'Terjadi kesalahan sistem.' });
            });
        });

        let currentSubtotal = 0;
        function openPaymentModal(data) {
            const modal = document.getElementById('paymentModal');
            const confirmBtn = document.getElementById('pay-confirm-btn');
            const warnBox = document.getElementById('pres-warning-box');
            const successBox = document.getElementById('pres-success-box');
            
            // Show modal if not already visible
            if (modal.style.display !== 'flex') {
                toggleModal('paymentModal');
            }

            if (data.loading) {
                // Skeleton/Loading State
                confirmBtn.innerText = 'Menyiapkan Data...';
                confirmBtn.disabled = true;
                confirmBtn.style.opacity = '0.7';
                document.getElementById('order-number').innerText = 'DIPROSES...';
                document.getElementById('payment-subtotal').innerText = '...';
                return;
            }

            // Real Data State
            currentSubtotal = data.subtotal; 
            document.getElementById('order-number').innerText = data.number;
            document.getElementById('payment-subtotal').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.subtotal);
            document.getElementById('paymentForm').action = data.url;
            calcTotal();

            warnBox.style.display = 'none';
            successBox.style.display = 'none';
            confirmBtn.disabled = false;
            confirmBtn.style.opacity = '1';

            if (data.reqPres) {
                if (data.hasFile) {
                    successBox.style.display = 'block';
                    successBox.innerHTML = '✅ <strong>Resep Terverifikasi:</strong> Resep manual telah terlampir.';
                    confirmBtn.innerText = 'Konfirmasi & Bayar';
                } else {
                    warnBox.style.display = 'block';
                    confirmBtn.disabled = true;
                    confirmBtn.style.opacity = '0.5';
                    confirmBtn.innerText = 'Resep Diperlukan';
                }
            } else {
                confirmBtn.innerText = 'Konfirmasi & Bayar';
            }
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
                header.innerText = 'PEMBAYARAN QRIS';
                html += `
                    <div style="background: white; padding: 20px; border-radius: 16px; margin-bottom: 20px; border: 1px solid #eee;">
                        <img src="{{ asset('assets/images/payments/qris_prototype.png') }}" 
                             style="width: 250px; height: auto; margin: 0 auto; display: block; border-radius: 8px;">
                        <div style="margin-top: 15px; font-weight: 700; color: #475569;">Pindai kode QR untuk membayar</div>
                    </div>
                `;
            } else if (method === 'wallet') {
                header.innerText = 'BAYAR DENGAN SALDO DOMPET';
                const nextWallet = {{ Auth::user()->wallet_balance ?? 0 }} - data.grand_total;
                const isEnough = nextWallet >= 0;
                
                html += `
                    <div style="background: #ecfdf5; padding: 20px; border-radius: 16px; margin-bottom: 20px; text-align: left; border: 1px solid #10b981;">
                        <div style="margin-bottom: 10px; color: #065f46; font-weight: 700; font-size: 0.9rem;">Status Dompet:</div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <span style="color: #64748b;">Saldo Saat Ini</span>
                            <span style="font-weight: 700; color: #1e293b;">Rp {{ number_format(Auth::user()->wallet_balance ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <span style="color: #64748b;">Tagihan Pesanan</span>
                            <span style="font-weight: 700; color: #ef4444;">- ${totalStr}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; border-top: 1px solid #a7f3d0; padding-top: 8px; margin-top: 5px;">
                            <span style="color: #64748b;">Sisa Saldo</span>
                            <span style="font-weight: 700; color: ${isEnough ? '#10b981' : '#ef4444'};">Rp ${new Intl.NumberFormat('id-ID').format(nextWallet)}</span>
                        </div>
                    </div>
                `;
                
                if (!isEnough) {
                    html += `
                        <div style="background: #fff1f2; color: #be123c; padding: 12px; border-radius: 8px; font-size: 0.85rem; margin-bottom: 20px; font-weight: 600;">
                            ⚠️ Saldo dompet Anda tidak cukup untuk melakukan transaksi ini.
                        </div>
                    `;
                    document.getElementById('sim-footer-btn').disabled = true;
                    document.getElementById('sim-footer-btn').style.opacity = '0.5';
                    document.getElementById('sim-footer-btn').innerText = 'Saldo Tidak Cukup';
                } else {
                    document.getElementById('sim-footer-btn').disabled = false;
                    document.getElementById('sim-footer-btn').style.opacity = '1';
                    document.getElementById('sim-footer-btn').innerText = 'Selesaikan & Bayar Sekarang';
                }
            } else if (method === 'paylater') {
                header.innerText = 'PEMBAYARAN PAYLATER';
                
                // Add selectTenor helper to the global scope once
                window.selectPaylaterTenor = function(btn, tenor, principal, currentLimit) {
                    // Reset styling
                    const grid = btn.parentElement;
                    grid.querySelectorAll('.tenor-card').forEach(c => {
                        c.style.borderColor = '#e2e8f0';
                        c.style.background = 'white';
                        c.querySelector('.check-mark').style.display = 'none';
                    });

                    // Set active
                    btn.style.borderColor = 'var(--primary-blue)';
                    btn.style.background = '#f0f9ff';
                    btn.querySelector('.check-mark').style.display = 'block';

                    // Math
                    const interestRate = tenor === 1 ? 0 : 0.03;
                    const taxPerMonth = principal * interestRate;
                    const totalTax = taxPerMonth * tenor;
                    const totalBill = principal + totalTax;
                    const monthly = totalBill / tenor;
                    const nextLimit = currentLimit - totalBill;

                    // Update UI
                    document.getElementById('pl-bill').innerText = '- Rp ' + new Intl.NumberFormat('id-ID').format(totalBill);
                    document.getElementById('pl-limit').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(nextLimit);
                    document.getElementById('pl-tax').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalTax);
                    document.getElementById('pl-per-month').innerText = 'Rp ' + new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(monthly) + ' x ' + tenor + ' Bln';
                    
                    // Show/Hide Tax row
                    document.getElementById('pl-tax-row').style.display = tenor > 1 ? 'flex' : 'none';
                };

                html += `
                    <p style="color: #64748b; margin-bottom: 12px; text-align: left; font-size: 0.9rem; font-weight: 600;">Pilih Tenor Cicilan:</p>
                    <div class="tenor-grid" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; margin-bottom: 20px;">
                        <div class="tenor-card" onclick="selectPaylaterTenor(this, 1, ${data.grand_total}, ${data.paylater_limit})" 
                             style="border: 2px solid var(--primary-blue); background: #f0f9ff; padding: 15px; border-radius: 12px; cursor: pointer; position: relative; transition: 0.2s;">
                            <div style="font-weight: 800; font-size: 0.95rem;">1x Bln</div>
                            <div style="font-size: 0.7rem; color: #64748b; margin-top: 4px;">Bunga 0%</div>
                            <div class="check-mark" style="position: absolute; top: 5px; right: 5px; color: var(--primary-blue); font-size: 0.8rem;">●</div>
                        </div>
                        <div class="tenor-card" onclick="selectPaylaterTenor(this, 3, ${data.grand_total}, ${data.paylater_limit})" 
                             style="border: 2px solid #e2e8f0; background: white; padding: 15px; border-radius: 12px; cursor: pointer; position: relative; transition: 0.2s;">
                            <div style="font-weight: 800; font-size: 0.95rem;">3x Bln</div>
                            <div style="font-size: 0.7rem; color: #ef4444; margin-top: 4px;">+3% Pajak</div>
                            <div class="check-mark" style="position: absolute; top: 5px; right: 5px; color: var(--primary-blue); font-size: 0.8rem; display: none;">●</div>
                        </div>
                        <div class="tenor-card" onclick="selectPaylaterTenor(this, 6, ${data.grand_total}, ${data.paylater_limit})" 
                             style="border: 2px solid #e2e8f0; background: white; padding: 15px; border-radius: 12px; cursor: pointer; position: relative; transition: 0.2s;">
                            <div style="font-weight: 800; font-size: 0.95rem;">6x Bln</div>
                            <div style="font-size: 0.7rem; color: #ef4444; margin-top: 4px;">+3% Pajak</div>
                            <div class="check-mark" style="position: absolute; top: 5px; right: 5px; color: var(--primary-blue); font-size: 0.8rem; display: none;">●</div>
                        </div>
                    </div>

                    <div style="background: #f8fafc; padding: 20px; border-radius: 16px; margin-bottom: 20px; text-align: left; border: 1px solid #e2e8f0;">
                        <div style="margin-bottom: 12px; color: #1e293b; font-weight: 700; font-size: 0.9rem;">Ringkasan Tagihan:</div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span style="color: #64748b; font-size: 0.85rem;">Cicilan per Bulan</span>
                            <span id="pl-per-month" style="font-weight: 700; color: #1e293b;">${totalStr} x 1 Bln</span>
                        </div>
                        <div id="pl-tax-row" style="display: none; justify-content: space-between; margin-bottom: 8px;">
                            <span style="color: #64748b; font-size: 0.85rem;">Pajak Layanan (3%/Bln)</span>
                            <span id="pl-tax" style="font-weight: 700; color: #ef4444;">Rp 0</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span style="color: #64748b; font-size: 0.85rem;">Total Tagihan Paylater</span>
                            <span id="pl-bill" style="font-weight: 700; color: #ef4444;">- ${totalStr}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; border-top: 1px solid #e2e8f0; padding-top: 10px; margin-top: 5px;">
                            <span style="color: #64748b; font-size: 0.85rem;">Estimasi Sisa Limit</span>
                            <span id="pl-limit" style="font-weight: 700; color: #10b981;">Rp ${new Intl.NumberFormat('id-ID').format(data.paylater_limit - data.grand_total)}</span>
                        </div>
                    </div>
                `;
            } else if (method === 'bank') {
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
