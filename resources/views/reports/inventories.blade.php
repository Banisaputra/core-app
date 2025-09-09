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
    <h3>Laporan Adjustmen Stok</h3>
    <p>Periode: {{ date('d-m-Y', strtotime($dateStart)) ." - ". date('d-m-Y', strtotime($dateEnd))}}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Tanggal</th>
                <th>Type</th>
                <th>Keterangan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @if (count($data) > 0)
                @foreach($data as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row['inv_code'] }}</td>
                    <td>{{ date('d M Y', strtotime($row['inv_date']))}}</td>
                    <td>{{ $row['inv_type'] }}</td>
                    <td>{{ $row['inv_remark'] }}</td>
                    <td>@if ($row['inv_state'] == 1)
                        Diajukan
                    @elseif ($row['inv_state'] == 2)
                        Disetujui 
                    @elseif ($row['inv_state'] == 99)
                        Ditolak
                    @endif</td>
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
