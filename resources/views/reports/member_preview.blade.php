<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Preview Laporan Member</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 12px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .warning-box {
            background-color: #fafafa;
            border: 1px solid #ffc107;
            color: #b80000;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
        }
        .info-box {
            background-color: #fafafa;
            border: 1px solid #c3e6cb;
            color: #000000;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th {
            background-color: #bbd5f1;
            color: rgb(0, 0, 0);
            font-weight: bold;
            padding: 8px;
            border: 1px solid #a5b9ce;
            font-size: 10px;
        }
        td {
            padding: 5px;
            border: 1px solid #ddd;
            font-size: 8px;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .footer-note {
            margin-top: 30px;
            text-align: center;
            font-style: italic;
            color: #666;
        }
    </style>
</head>
<body>
    <h2>PREVIEW LAPORAN DATA MEMBER</h2>
    
    <div class="warning-box">
        INI HANYA PREVIEW DENGAN {{ $previewCount }} DATA DARI TOTAL {{ $totalData }} DATA
        <br><br>
        <span style="font-size: 14px;">UNTUK MENDAPATKAN DATA LENGKAP, SILAKAN KLIK TOMBOL DOWNLOAD</span>
    </div>
    
    @if(!empty($filter))
    <div class="info-box">
        <strong>Filter yang diterapkan:</strong><br>
        @foreach($filter as $key => $value)
            {{ $key }}: <strong>{{ $value }}</strong>@if(!$loop->last) | @endif
        @endforeach
    </div>
    @endif
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIP</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Jabatan</th>
                <th>Divisi</th>
                <th>No KTP</th>
                <th>No KK</th>
                <th>Telepon</th>
                <th>Alamat</th>
                <th>Tgl Gabung</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @php $no = 1; @endphp
            @foreach($data as $item)
            <tr>
                <td style="text-align: center;">{{ $no++ }}</td>
                <td>{{ $item['nip'] }}</td>
                <td>{{ $item['name'] }}</td>
                <td>{{ $item['email'] }}</td>
                <td>{{ $item['position'] }}</td>
                <td>{{ $item['devision'] }}</td>
                <td>{{ $item['no_ktp'] }}</td>
                <td>{{ $item['no_kk'] }}</td>
                <td>{{ $item['phone'] }}</td>
                <td>{{ Str::limit($item['address'], 20) }}</td>
                <td style="text-align: center;">{{ $item['date_joined'] }}</td>
                <td style="text-align: center;">{{ $item['status'] == 1 ? 'Aktif' : 'Nonaktif' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer-note">
        * Halaman ini adalah preview dengan {{ $previewCount }} data pertama<br>
        Klik tombol Download untuk mendapatkan laporan lengkap ({{ $totalData }} data)
    </div>
</body>
</html>