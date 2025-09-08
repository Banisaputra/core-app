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
    <h3>Laporan Potongan Gaji Anggota</h3>
    <p>Periode: {{ date('d-m-Y', strtotime($periode_start)) ." s/d ". date('d-m-Y', strtotime($periode_end)) }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Anggota</th>
                <th>Jabatan</th>
                <th>Potongan Simpanan<br>(Wajib + SHT)</th>
                <th>Potongan Pinjaman<br>(Uang + Barang)</th>
                <th>Total Potongan</th>
            </tr>
        </thead>
        <tbody>
            @if (count($data) > 0)
                @foreach($data as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['position'] }}</td>
                    <td>{{ number_format($row['potongan_simpanan'], 0, ',', '.') }}</td>
                    <td>{{ number_format($row['potongan_pinjaman'], 0, ',', '.') }}</td>
                    <td>{{ number_format($row['total'], 0, ',', '.') }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td span=4>Tidak ada data</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
