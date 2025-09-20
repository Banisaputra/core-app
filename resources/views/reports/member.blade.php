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
    <h3>Laporan Daftar Anggota</h3>
    <p>Filter: <br><ul>
        @foreach ($filter as $key => $ft)
        <li>{{$key}} : {{ $ft }}</li>
        @endforeach
    </ul></p>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIK</th>
                <th>Nama</th>
                <th>Jabatan</th>
                <th>Bagian</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @if (count($data) > 0)
                @foreach($data as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row['nip'] }}</td>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['position'] }}</td>
                    <td>{{ $row['devision'] }}</td>
                    <td>{{ $row['status'] == 1 ? "Active" : "Nonactive" }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan=6 style="text-align: center">Tidak ada data</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
