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
    <h3>Laporan Stok Barang</h3>
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
                <th>Nama</th>
                <th>HPP</th>
                <th>Stok</th>
            </tr>
        </thead>
        <tbody>
            @if (count($data) > 0)
                @foreach($data as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row['item_code'] }}</td>
                    <td>{{ $row['item_name'] }}</td>
                    <td style="text-align: right">{{ number_format($row['item_hpp'], 0, ',', '.') }}</td>
                    <td>{{ $row['item_stock'] }}</td>
                </tr>
                @endforeach 
            @else
                <tr>
                    <td colspan=5 style="text-align: center">Tidak ada data</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
