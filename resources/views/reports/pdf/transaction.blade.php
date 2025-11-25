<!DOCTYPE html>
<html>

<head>
    <title>Transaction Report</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #3490dc;
            padding-bottom: 10px;
        }

        .header h1 {
            color: #2c3e50;
            font-size: 20px;
            margin-bottom: 5px;
        }

        .header p {
            color: #7f8c8d;
            font-size: 12px;
            margin: 0;
        }

        .company-info {
            margin-bottom: 15px;
            text-align: center;
        }

        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
        }

        .filter-info {
            background-color: #f8f9fa;
            padding: 8px 12px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 11px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th {
            background-color: #3490dc;
            color: white;
            text-align: left;
            padding: 8px;
            font-size: 11px;
        }

        td {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 11px;
        }

        tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .badge {
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
            min-width: 40px;
            text-align: center;
        }

        .badge-in {
            background-color: #c6f6d5;
            color: #22543d;
        }

        .badge-out {
            background-color: #fed7d7;
            color: #822727;
        }

        .footer {
            text-align: right;
            margin-top: 20px;
            font-size: 10px;
            color: #7f8c8d;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }

        .summary {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 11px;
        }

        .summary-item {
            display: inline-block;
            margin-right: 20px;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="company-info">
        <div class="company-name">PT. SIMASTOK</div>
        <div>Jl. Banten No.1 Karang Pawitan, Karawang</div>
        <div>Telp: 0812-2002-2851</div>
    </div>

    <div class="header">
        <h1>INVENTORY TRANSACTION REPORT</h1>
        <p>Generated on: {{ now()->format('d F Y H:i') }}</p>
    </div>

    @if (request('date_from') || request('date_to'))
        <div class="filter-info">
            <strong>FILTER APPLIED:</strong>
            @if (request('date_from'))
                <span style="margin: 0 10px;">From: {{ request('date_from') }}</span>
            @endif
            @if (request('date_to'))
                <span style="margin: 0 10px;">To: {{ request('date_to') }}</span>
            @endif
        </div>
    @endif

    <div class="summary">
        <div class="summary-item">
            <strong>Total Transactions:</strong> {{ $transactions->count() }}
        </div>
        <div class="summary-item">
            <strong>Stock In:</strong> {{ $transactions->where('type', 'in')->count() }}
        </div>
        <div class="summary-item">
            <strong>Stock Out:</strong> {{ $transactions->where('type', 'out')->count() }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 10%;">Date</th>
                <th style="width: 20%;">Item</th>
                <th style="width: 10%;">Type</th>
                <th style="width: 10%;">Qty</th>
                <th style="width: 15%;">User</th>
                <th style="width: 25%;">Note</th>
                <th style="width: 10%;">Ref No</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $transaction)
                <tr>
                    <td>{{ $transaction->date->format('d M Y') }}</td>
                    <td>{{ $transaction->item->name }}</td>
                    <td>
                        <span class="badge {{ $transaction->type === 'in' ? 'badge-in' : 'badge-out' }}">
                            {{ $transaction->type === 'in' ? 'IN' : 'OUT' }}
                        </span>
                    </td>
                    <td>{{ $transaction->quantity }} {{ $transaction->item->unit->symbol }}</td>
                    <td>{{ $transaction->user->name }}</td>
                    <td>{{ $transaction->note ?? '-' }}</td>
                    <td>TRX-{{ $transaction->id }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Printed by: {{ auth()->user()->name }} &bull; Page 1 of 1
    </div>
</body>

</html>
