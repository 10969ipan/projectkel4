<!DOCTYPE html>
<html>

<head>
    <title>Stock Report</title>
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

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-red {
            color: #e3342f;
        }

        .font-bold {
            font-weight: bold;
        }

        .stock-low {
            background-color: #fff5f5;
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
    </style>
</head>

<body>
    <div class="company-info">
        <div class="company-name">PT. SIMASTOK</div>
        <div>Jl. Banten No.1 Karang Pawitan, Karawang</div>
        <div>Telp: 0812-2002-2851</div>
    </div>

    <div class="header">
        <h1>INVENTORY STOCK REPORT</h1>
        <p>Generated on: {{ now()->format('d F Y H:i') }}</p>
    </div>

    <div class="summary">
        <div class="summary-item">
            <strong>Total Items:</strong> {{ $items->count() }}
        </div>
        <div class="summary-item">
            <strong>Low Stock Items:</strong> {{ $items->where('stock', '<', 10)->count() }}
        </div>
        <div class="summary-item">
            <strong>Out of Stock Items:</strong> {{ $items->where('stock', '<=', 0)->count() }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 10%;">Code</th>
                <th style="width: 25%;">Item Name</th>
                <th style="width: 15%;">Category</th>
                <th style="width: 10%;">Stock</th>
                <th style="width: 10%;">Unit</th>
                <th style="width: 15%; text-align: right;">Price</th>
                <th style="width: 15%; text-align: right;">Total Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr class="{{ $item->stock < 10 ? 'stock-low' : '' }}">
                    <td>{{ $item->code }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->category->name }}</td>
                    <td class="{{ $item->stock < 10 ? 'text-red font-bold' : '' }}">{{ $item->stock }}</td>
                    <td>{{ $item->unit->symbol }}</td>
                    <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->price * $item->stock, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Printed by: {{ auth()->user()->name }} &bull; Page 1 of 1
    </div>
</body>

</html>
