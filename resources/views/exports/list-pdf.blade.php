<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1E293B;
            padding: 28px 32px;
        }
        .header {
            margin-bottom: 22px;
            padding-bottom: 14px;
            border-bottom: 2px solid #6366F1;
        }
        .header h1 {
            font-size: 20px;
            color: #4338CA;
            margin-bottom: 4px;
        }
        .header p {
            font-size: 10px;
            color: #64748B;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        thead th {
            background: #EEF2FF;
            color: #4338CA;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 10px 12px;
            text-align: left;
            border: 1px solid #C7D2FE;
        }
        tbody td {
            padding: 9px 12px;
            border: 1px solid #E2E8F0;
            vertical-align: top;
        }
        tbody tr:nth-child(even) td {
            background: #F8FAFC;
        }
        .footer {
            margin-top: 18px;
            font-size: 9px;
            color: #94A3B8;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title }}</h1>
        @if(!empty($subtitle))
            <p>{{ $subtitle }}</p>
        @endif
        <p>Generated on {{ $generatedAt }}</p>
    </div>

    <table>
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    @foreach($row as $cell)
                        <td>{{ $cell ?? '—' }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) }}">No records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">SalonPro Management System</div>
</body>
</html>
