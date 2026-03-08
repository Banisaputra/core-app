<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background: #eee; }
        ul, li { margin: 0px}
    </style>
</head>
<body>
    <h3>Laporan Daftar Pinjaman Anggota</h3>
    <p>Filter:</p>
    <ul>
    @foreach ($filter as $key => $ft)
    <li>{{$key}} : {{ $ft }}</li>
    @endforeach
    </ul>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Tanggal</th>
                <th>UANG</th>
                <th>BARANG</th>
                <th>Total</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @if (count($data) > 0)
                <?php 
                $subtotal = 0;
                $subtotal_ug = 0;
                $subtotal_br = 0;
                ?>
                @foreach($data as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}.</td>
                    <td>{{ $row['ln_code'] }}</td>
                    <td>{{ date('d M Y', strtotime($row['ln_date']))}}</td>
                    <td style="text-align: right">{{ $row['ln_type'] == "UANG" ? number_format($row['ln_value'], 0, ',', '.') : "" }}</td>
                    <td style="text-align: right">{{ $row['ln_type'] == "BARANG" ? number_format($row['ln_value'], 0, ',', '.') : "" }}</td>
                    <td style="text-align: right">{{ number_format($row['ln_value'], 0, ',', '.') }}</td>
                    <td>@if ($row['status'] == 1)
                        Diajukan
                    @elseif ($row['status'] == 2)
                        Disetujui
                    @elseif ($row['status'] == 3)
                        Selesai
                    @elseif ($row['status'] == 99)
                        Ditolak
                    @endif</td>
                </tr>
                    @foreach ($row['payments'] as $pay)
                    <tr>
                        <td colspan="2" style="text-align: center">{{  $pay['lp_code'] . " (x".$pay['tenor_month'].")" }}</td>
                        <td style="text-align: center">{{ date('d M Y', strtotime($pay['lp_date'])) }}</td>
                        <td style="text-align: right">{{ $row['ln_type'] == "UANG" ? number_format($pay['lp_total'],0, ',','.') : ""}}</td>
                        <td style="text-align: right">{{ $row['ln_type'] == "BARANG" ? number_format($pay['lp_total'],0, ',','.') : ""}}</td>
                        <td style="text-align: right">{{ number_format($pay['lp_total'],0, ',','.') }}</td>
                        <td>@if ($pay['lp_state'] == 1)
                            Pending
                        @elseif ($pay['lp_state'] == 2)
                            Lunas
                        @endif</td>
                    </tr>
                    <?php 
                        switch ($row['ln_type']) {
                            case 'BARANG':
                                $subtotal_br += $pay['lp_total'];
                                break;
                            case 'UANG':
                                $subtotal_ug += $pay['lp_total'];
                                break;
                            default:
                                // lp type not found
                                break;
                        }
                        $subtotal += $pay['lp_total'];
                    ?>

                    @endforeach
                   
                @endforeach
                <tr>
                    <td colspan="3" style="text-align: right"><b>Total</b></td>
                    <td colspan="" style="text-align: right"><b>{{ number_format($subtotal_ug,0,',','.')}}</td>
                    <td colspan="" style="text-align: right"><b>{{ number_format($subtotal_br,0,',','.')}}</td>
                    <td colspan="" style="text-align: right"><b>{{ number_format($subtotal,0,',','.')}}</b></td>
                    <td></td>
                </tr>
            @else
                <tr>
                    <td colspan=7 style="text-align: center">Tidak ada data</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
