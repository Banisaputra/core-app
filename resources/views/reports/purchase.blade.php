<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #eee; }
    </style>
</head>
<body>
    <h3>Laporan Pembelian</h3>
    <p>Filter: <br><ul>
        @foreach ($filter as $key => $ft)
        <li>{{$key}} : {{ $ft }}</li>
        @endforeach
    </ul></p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Tanggal</th>
                <th>Ref Doc</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @if (count($data) > 0)
                <?php $subtotal = 0; ?>
                @foreach($data as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row['pr_code'] }}</td>
                    <td>{{ date('d M Y', strtotime($row['pr_date']))}}</td>
                    <td>{{ $row['pr_ref_doc'] }}</td>
                    <td style="text-align: right">{{ number_format($row['pr_value'], 0, ',', '.') }}</td>
                </tr>
                <?php $subtotal += $row['pr_value'] ?>
                @endforeach
                <tr>
                    <td colspan="4" style="text-align: right"><b>Total</b></td>
                    <td style="text-align: right"><b>{{ number_format($subtotal,0,',','.')}}</b></td>
                </tr>
            @else
                <tr>
                    <td colspan=5 style="text-align: center">Tidak ada data</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
