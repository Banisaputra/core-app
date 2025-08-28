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
    <h3>Laporan Daftar Simpanan Anggota</h3>
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
                <th>Tgl. Simpanan</th>
                <th>Jenis</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @if (count($data) > 0)
                <?php $subtotal = 0; ?>
                @foreach($data as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row['sv_code'] }}</td>
                    <td>{{ date('d M Y', strtotime($row['sv_date']))}}</td>
                    <td>{{ $row['sv_type'] }}</td>
                    <td style="text-align: right">{{ number_format($row['sv_value'], 0, ',', '.') }}</td>
                    <td>@if ($row['status'] == 1)
                        Diajukan
                    @elseif ($row['status'] == 2)
                        Disetujui
                    @elseif ($row['status'] == 99)
                        Ditolak
                    @endif</td>
                </tr>
                <?php 
                if ($row['status'] != 99) {
                    $subtotal += $row['sv_value'];
                } else {
                    $subtotal -= $row['sv_value'];
                }
                ?>
                @endforeach
                <tr>
                    <td colspan="4" style="text-align: right"><b>Total</b></td>
                    <td colspan="" style="text-align: right"><b>{{ number_format($subtotal,0,',','.')}}</b></td>
                    <td></td>
                </tr>
            @else
                <tr>
                    <td colspan=6 style="text-align: center">Tidak ada data</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
