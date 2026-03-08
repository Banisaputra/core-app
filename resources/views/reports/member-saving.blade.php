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
                <th>Tanggal</th>
                <th>Pokok</th>
                <th>Wajib</th>
                <th>SHT</th>
                <th>Cadangan</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @if (count($data) > 0)
                <?php 
                $subtotal = 0;
                $subtotal_pk = 0;
                $subtotal_wj = 0;
                $subtotal_sht = 0;
                $subtotal_dc = 0;
                ?>
                @foreach($data as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row['sv_code'] }}</td>
                    <td>{{ date('d M Y', strtotime($row['sv_date']))}}</td>
                    <td style="text-align: right">{{ strtoupper($row['sv_type']) == "POKOK" ? number_format($row['sv_value'], 0, ',', '.') : "0"  }}</td>
                    <td style="text-align: right">{{ strtoupper($row['sv_type']) == "WAJIB" ? number_format($row['sv_value'], 0, ',', '.') : "0"  }}</td>
                    <td style="text-align: right">{{ strtoupper($row['sv_type']) == "SHT" ? number_format($row['sv_value'], 0, ',', '.') : "0"  }}</td>
                    <td style="text-align: right">{{ strtoupper($row['sv_type']) == "DANA CADANGAN" ? number_format($row['sv_value'], 0, ',', '.') : "0"  }}</td>
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
                    switch (strtoupper($row['sv_type'])) {
                        case 'POKOK':
                            $subtotal_pk += $row['sv_value'];
                            break;
                        case 'WAJIB':
                            $subtotal_wj += $row['sv_value'];
                            break;
                        case 'SHT':
                            $subtotal_sht += $row['sv_value'];
                            break;
                        case 'DANA CADANGAN':
                            $subtotal_dc += $row['sv_value'];
                            break;
                        
                        default:
                            // type not found
                            break;
                    }
                    
                }
                ?>
                @endforeach
                <tr>
                    <td colspan="3" style="text-align: right"><b>Subtotal</b></td>
                    <td colspan="" style="text-align: right"><b>{{ number_format($subtotal_pk,0,',','.')}}</b></td>
                    <td colspan="" style="text-align: right"><b>{{ number_format($subtotal_wj,0,',','.')}}</b></td>
                    <td colspan="" style="text-align: right"><b>{{ number_format($subtotal_sht,0,',','.')}}</b></td>
                    <td colspan="" style="text-align: right"><b>{{ number_format($subtotal_dc,0,',','.')}}</b></td>
                    <td colspan="" style="text-align: right"><b>{{ number_format($subtotal,0,',','.')}}</b></td>
                    <td></td>
                </tr>
            @else
                <tr>
                    <td colspan=9 style="text-align: center">Tidak ada data</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
