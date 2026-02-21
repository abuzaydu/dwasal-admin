<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        h2   { text-align: center; color: #1A73E8; margin-bottom: 4px; }
        p.meta { text-align: center; color: #666; margin-bottom: 16px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #1A73E8; color: #fff; padding: 7px 6px; text-align: left; }
        td { padding: 6px; border-bottom: 1px solid #e0e0e0; }
        tr:nth-child(even) td { background: #f5f8ff; }
    </style>
</head>
<body>
    <h2>Visitor Report — {{ ucfirst($type) }} ({{ ucfirst($period) }})</h2>
    <p class="meta">Generated: {{ $generatedAt }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Host</th>
                <th>Purpose</th>
                <th>Status</th>
                <th>Check-in</th>
                <th>Check-out</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($visitors as $i => $v)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $v->name }}</td>
                <td>{{ $v->phone ?? 'N/A' }}</td>
                <td>{{ $v->host_name ?? 'N/A' }}</td>
                <td>{{ $v->purpose ?? 'N/A' }}</td>
                <td>{{ $v->status }}</td>
                <td>{{ $v->check_in_time  ? \Carbon\Carbon::parse($v->check_in_time)->format('H:i')  : 'N/A' }}</td>
                <td>{{ $v->check_out_time ? \Carbon\Carbon::parse($v->check_out_time)->format('H:i') : 'N/A' }}</td>
                <td>{{ \Carbon\Carbon::parse($v->created_at)->format('d M Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="9" style="text-align:center; color:#999;">No records found.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>