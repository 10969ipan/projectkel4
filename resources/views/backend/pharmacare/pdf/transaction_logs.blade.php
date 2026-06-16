<!DOCTYPE html>
<html>

<head>
    <title>PHARMACARE TRANSACTION LOGS REPORT</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #0d9488;
            padding-bottom: 10px;
        }

        .text-teal {
            color: #0d9488;
        }

        .company-tagline {
            font-size: 9px;
            font-weight: bold;
            color: #14b8a6;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .header h1 {
            color: #2c3e50;
            font-size: 18px;
            margin-bottom: 5px;
            margin-top: 5px;
        }

        .header p {
            color: #7f8c8d;
            font-size: 11px;
            margin: 0;
        }

        .company-info {
            margin-bottom: 15px;
            text-align: center;
        }

        .company-name {
            font-size: 15px;
            font-weight: bold;
            color: #2c3e50;
        }

        .filter-info {
            background-color: #f8f9fa;
            padding: 8px 12px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 10px;
            border: 1px solid #e5e7eb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th {
            background-color: #0d9488;
            color: white;
            text-align: left;
            padding: 8px 6px;
            font-size: 10px;
        }

        td {
            padding: 8px 6px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
            vertical-align: top;
        }

        tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .badge {
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
            display: inline-block;
            text-align: center;
        }

        .badge-completed {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-cancelled {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .footer {
            text-align: right;
            margin-top: 25px;
            font-size: 9px;
            color: #7f8c8d;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        .summary {
            background-color: #f9fafb;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 10px;
            border: 1px solid #e5e7eb;
        }

        .summary-item {
            display: inline-block;
            margin-right: 20px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="company-info">
        <div class="company-name text-teal">SIMA-APOTEK</div>
        <div class="company-tagline">Pharmacare E-Commerce System</div>
        <div>Jl. Banten No.1 Karang Pawitan, Karawang</div>
        <div>Telp: 0812-2002-2851</div>
    </div>

    <div class="header">
        <h1>LAPORAN LOG TRANSAKSI E-COMMERCE</h1>
        <p>Dicetak pada: {{ now()->format('d F Y H:i') }}</p>
    </div>

    @if (request('date_start') || request('date_end') || request('status'))
        <div class="filter-info">
            <strong>FILTER DITERAPKAN:</strong>
            @if (request('date_start'))
                <span style="margin: 0 10px;">Dari Tanggal: {{ request('date_start') }}</span>
            @endif
            @if (request('date_end'))
                <span style="margin: 0 10px;">Sampai Tanggal: {{ request('date_end') }}</span>
            @endif
            @if (request('status'))
                <span style="margin: 0 10px;">Status: {{ request('status') === 'completed' ? 'Selesai' : 'Dibatalkan' }}</span>
            @endif
        </div>
    @endif

    <div class="summary">
        <div class="summary-item">
            <strong>Total Pesanan:</strong> {{ $orders->count() }}
        </div>
        <div class="summary-item">
            <strong>Pesanan Selesai:</strong> {{ $orders->where('order_status', 'completed')->count() }}
        </div>
        <div class="summary-item">
            <strong>Pesanan Dibatalkan:</strong> {{ $orders->where('order_status', 'cancelled')->count() }}
        </div>
        <div class="summary-item">
            <strong>Total Nilai Transaksi:</strong> Rp {{ number_format($orders->where('order_status', 'completed')->sum('grand_total'), 0, ',', '.') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 15%;">No. Order</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 20%;">Pelanggan</th>
                <th style="width: 25%;">Alamat</th>
                <th style="width: 13%; text-align: right;">Total</th>
                <th style="width: 12%; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td><strong>{{ $order->order_number }}</strong></td>
                    <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                    <td>
                        {{ $order->user->name ?? 'User Terhapus' }}
                        <div style="font-size: 8px; color: #666; text-transform: uppercase;">
                            {{ $order->payment_method }}
                        </div>
                    </td>
                    <td>{{ $order->address->full_address ?? '-' }}</td>
                    <td class="text-right"><strong>Rp {{ number_format($order->grand_total, 0, ',', '.') }}</strong></td>
                    <td class="text-center">
                        <span class="badge {{ $order->order_status === 'completed' ? 'badge-completed' : 'badge-cancelled' }}">
                            {{ $order->order_status === 'completed' ? 'Selesai' : 'Batal' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px; color: #777; font-style: italic;">
                        Tidak ada data histori transaksi ditemukan
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh: {{ auth()->user()->name }} &bull; Halaman 1 dari 1
    </div>
</body>

</html>
