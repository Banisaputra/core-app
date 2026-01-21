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
    <h3>Laporan Laba Rugi</h3>
     <p>Filter: <br><ul>
        @foreach ($filter as $key => $ft)
        <li>{{$key}} : {{ $ft }}</li>
        @endforeach
    </ul></p>
    <table>
        <thead>
            <tr>
                <th>Keterangan</th>
                <th>Total</th>
                <th>Laba/Rugi</th>
            </tr>
        </thead>
        <tbody>
           <tr>
                <td>Pembelian</td>
                <td style="text-align: right">{{ number_format($data['totalPr'], 0, ',', '.') }}</td>
                <td></td>
           </tr>
           <tr>
                <td>Penjualan</td>
                <td style="text-align: right">{{ number_format($data['totalSl'], 0, ',', '.') }}</td>
                <td></td>
           </tr>
           <tr>
            <?php 
                $subtotal = $data['totalSl'] - $data['totalPr'];
                $profitNlose = 0;
                if ($subtotal < 0) {
                    $profitNlose = "(".number_format(($subtotal*-1), 0, ',', '.').")";
                } else {
                    $profitNlose = number_format($subtotal, 0, ',', '.');
                }
            ?>
                <td colspan="2" style="text-align: right"><b>Total</b></td>
                <td style="text-align: right"><b>{{ $profitNlose }}</b></td>
           </tr>
        </tbody>
    </table>
</body>
</html>
