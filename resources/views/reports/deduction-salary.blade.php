<!DOCTYPE html>
<html>
<head>
    <style>
        @page {
            margin: 15mm 10mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif; /* lebih aman untuk DOMPDF */
            font-size: 12px;
            letter-spacing: 0.2px;
        }

        h3 {
            text-align: center;
            margin-bottom: 4px;
        }

        p {
            text-align: center;
            margin-top: 0;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            page-break-inside: auto;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px 6px;
        }

        th {
            background: #eeeeee;
            font-weight: bold;
            text-align: center;
        }

        td {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .angka {
            text-align: right;
            white-space: nowrap;
        }

        thead {
            display: table-header-group; /* header muncul setiap halaman */
        }

        tfoot {
            display: table-row-group;
        }
    </style>
</head>
<body>

    <h3>Laporan Potongan Gaji Anggota</h3>
    <p>Periode: {{ date('d-m-Y', strtotime($periode_start)) ." s/d ". date('d-m-Y', strtotime($periode_end)) }}</p>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIK</th>
                <th>Nama Anggota</th>
                <th>Jabatan</th>
                <th>Potongan Simpanan<br>(Pokok + Wajib + SHT)</th>
                <th>Potongan Pinjaman<br>(Uang + Barang)</th>
                <th>Total Potongan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $row['nip'] }}</td>
                <td>{{ $row['name'] }}</td>
                <td>{{ $row['position'] }}</td>
                <td class="angka">{{ number_format($row['potongan_simpanan'], 0, ',', '.') }}</td>
                <td class="angka">{{ number_format($row['potongan_pinjaman'], 0, ',', '.') }}</td>
                <td class="angka">{{ number_format($row['total'], 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align:center;">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
