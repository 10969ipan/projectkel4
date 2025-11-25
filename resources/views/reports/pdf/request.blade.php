<!DOCTYPE html>
<html>

<head>
    <title>Request Report</title>
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
            min-width: 60px;
            text-align: center;
        }

        .badge-pending {
            background-color: #feebc8;
            color: #8a2b0a;
        }

        .badge-approved {
            background-color: #c6f6d5;
            color: #22543d;
        }

        .badge-rejected {
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

        .page-break {
            page-break-after: always;
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
        <h1>ITEM REQUEST REPORT</h1>
        <p>Generated on: {{ now()->format('d F Y H:i') }}</p>
    </div>

    @if (request('status') || request('date_from') || request('date_to'))
        <div class="filter-info">
            <strong>FILTER APPLIED:</strong>
            @if (request('status'))
                <span style="margin: 0 10px;">Status: {{ strtoupper(request('status')) }}</span>
            @endif
            @if (request('date_from'))
                <span style="margin: 0 10px;">From: {{ request('date_from') }}</span>
            @endif
            @if (request('date_to'))
                <span style="margin: 0 10px;">To: {{ request('date_to') }}</span>
            @endif
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 25%;">Item</th>
                <th style="width: 10%;">Qty</th>
                <th style="width: 15%;">Requested By</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 15%;">Request Date</th>
                <th style="width: 15%;">Processed Date</th>
                <th style="width: 10%;">Ref No</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($requests as $request)
                <tr>
                    <td>{{ $request->item->name }}</td>
                    <td>{{ $request->quantity }} {{ $request->item->unit->symbol }}</td>
                    <td>{{ $request->user->name }}</td>
                    <td>
                        <span class="badge badge-{{ $request->status }}">
                            {{ strtoupper($request->status) }}
                        </span>
                    </td>
                    <td>{{ $request->created_at->format('d M Y') }}</td>
                    <td>{{ $request->processed_at ? $request->processed_at->format('d M Y') : '-' }}</td>
                    <td>REQ-{{ $request->id }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Printed by: {{ auth()->user()->name }} &bull; Page 1 of 1
    </div>
</body>

</html>
