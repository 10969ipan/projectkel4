<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->order_number }} - Pharmacare</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; line-height: 1.6; margin: 0; padding: 0; background: #f9fafb; }
        .invoice-box { width: 95%; max-width: 850px; margin: 40px auto; padding: 30px; background: white; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #f1f5f9; padding-bottom: 25px; margin-bottom: 30px; }
        .logo { font-size: 1.8rem; font-weight: 800; color: #0076D6; letter-spacing: -1px; }
        .invoice-title { text-align: right; }
        .invoice-title h1 { margin: 0; font-size: 2rem; color: #1e293b; }
        .invoice-title p { margin: 5px 0 0; color: #64748b; font-weight: 600; }
        
        .info-grid { display: grid; grid-template-columns: 1fr 1.2fr; gap: 20px; margin-bottom: 40px; }
        .info-block h3 { font-size: 0.8rem; text-transform: uppercase; color: #94a3b8; letter-spacing: 1px; margin-bottom: 8px; }
        .info-block p { margin: 0; font-weight: 600; color: #1e293b; font-size: 0.95rem; }
        .address-text { font-weight: 400 !important; color: #475569 !important; font-size: 0.85rem; margin-top: 3px !important; }

        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; table-layout: fixed; }
        .items-table th { background: #f8fafc; text-align: left; padding: 12px; border-bottom: 2px solid #e2e8f0; color: #64748b; font-size: 0.8rem; text-transform: uppercase; }
        .items-table td { padding: 12px; border-bottom: 1px solid #f1f5f9; overflow: hidden; word-wrap: break-word; }
        .item-name { font-weight: 700; color: #1e293b; font-size: 0.9rem; }
        .item-meta { font-size: 0.75rem; color: #94a3b8; }

        .summary { display: flex; justify-content: flex-end; }
        .summary-box { width: 100%; max-width: 320px; }
        .summary-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #f1f5f9; font-size: 0.9rem; }
        .summary-row.total { border-bottom: none; padding-top: 15px; color: #0076D6; font-size: 1.2rem; font-weight: 800; }

        .status-badge { display: inline-block; padding: 5px 12px; border-radius: 50px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; }
        .paid { background: #ecfdf5; color: #059669; }
        .pending { background: #fffbeb; color: #d97706; }
        .cancelled { background: #fef2f2; color: #dc2626; }

        .footer { margin-top: 40px; text-align: center; color: #94a3b8; font-size: 0.8rem; padding-top: 25px; border-top: 1px solid #f1f5f9; }

        @media print {
            @page { margin: 1cm; }
            body { background: white; margin: 0; padding: 0; }
            .invoice-box { box-shadow: none; margin: 0; padding: 0; width: 100%; max-width: 100%; }
            .no-print { display: none; }
            .header, .info-grid, .items-table, .summary { width: 100% !important; }
        }
    </style>
</head>
<body>

<div class="invoice-box">
    <div class="header">
        <div class="logo">PHARMACARE.</div>
        <div class="invoice-title">
            <h1>INVOICE</h1>
            <p>#{{ $order->order_number }}</p>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-block">
            <h3>Diterbitkan Untuk</h3>
            <p>{{ $order->user->name }}</p>
            <p class="address-text">{{ $order->user->email }}</p>
            <div style="margin-top: 15px;">
                <h3>Alamat Pengiriman</h3>
                <p>{{ $order->address->label ?? 'Alamat Utama' }}</p>
                <p class="address-text">{{ $order->address->full_address ?? '-' }}</p>
            </div>
        </div>
        <div class="info-block" style="text-align: right;">
            <h3>Tanggal Transaksi</h3>
            <p>{{ $order->created_at->format('d M Y, H:i') }}</p>
            <div style="margin-top: 15px;">
                <h3>Metode Pembayaran</h3>
                <p>{{ strtoupper($order->payment_method) }}</p>
            </div>
            @if($order->tracking_number)
            <div style="margin-top: 15px;">
                <h3>Nomor Resi (Shipment)</h3>
                <p style="color: #059669;">{{ $order->tracking_number }}</p>
            </div>
            @endif
            <div style="margin-top: 15px;">
                <h3>Status Pembayaran</h3>
                <span class="status-badge {{ $order->payment_status === 'paid' ? 'paid' : ($order->payment_status === 'cancelled' ? 'cancelled' : 'pending') }}">
                    {{ $order->payment_status === 'paid' ? 'LUNAS' : ($order->payment_status === 'cancelled' ? 'DIBATALKAN' : 'MENUNGGU PEMBAYARAN') }}
                </span>
            </div>
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Produk</th>
                <th style="text-align: center;">Qty</th>
                <th style="text-align: right;">Harga</th>
                <th style="text-align: right;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>
                    <div class="item-name">{{ $item->item->name ?? 'Produk Dihapus' }}</div>
                    <div class="item-meta">{{ $item->item->category->name ?? 'Kategori' }}</div>
                </td>
                <td style="text-align: center;">{{ $item->quantity }}</td>
                <td style="text-align: right;">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td style="text-align: right; font-weight: 700;">Rp {{ number_format($item->sub_total, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <div class="summary-box">
            <div class="summary-row">
                <span>Subtotal Produk</span>
                <span>Rp {{ number_format($order->sub_total, 0, ',', '.') }}</span>
            </div>
            <div class="summary-row">
                <span>Ongkos Kirim ({{ $order->shipping_method }})</span>
                <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
            </div>
            <div class="summary-row total">
                <span>Total Bayar</span>
                <span>Rp {{ number_format($order->grand_total, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Terima kasih telah berbelanja di Pharmacare. Simpan invoice ini sebagai bukti pembelian yang sah.</p>
        <p style="margin-top: 10px; font-weight: 600;">Pharmacare Indonesia &bull; www.pharmacare.com</p>
    </div>

    <div class="no-print" style="margin-top: 40px; text-align: center;">
        <button onclick="window.print()" style="padding: 12px 30px; background: #0076D6; color: white; border: none; border-radius: 12px; font-weight: 700; cursor: pointer; box-shadow: 0 4px 15px rgba(0,118,214,0.3);">
            Cetak Invoice (PDF)
        </button>
    </div>
</div>

</body>
</html>
