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
            margin-bottom: 4px;
        }

        p {
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

    <h3>Laporan Summary Simpanan</h3>
    <p>Filter: <br><ul>
        @foreach ($filter as $key => $ft)
        <li>{{$key}} : {{ $ft }}</li>
        @endforeach
    </ul></p>
    <table>
        <tr>
            <td><strong>Total Simpanan Pokok</strong></td>
            <td>: Rp {{ number_format($header->total_pokok ?? 0, 0) }}</td>

            <td><strong>Total Simpanan Wajib</strong></td>
            <td>: Rp {{ number_format($header->total_wajib ?? 0, 0) }}</td>
        </tr>
        <tr>
            <td><strong>Total Simpanan SHT</strong></td>
            <td>: Rp {{ number_format($header->total_sht ?? 0, 0) }}</td>
            
            <td><strong>Total Dana Cadangan</strong></td>
            <td>: Rp {{ number_format($header->total_cadangan ?? 0, 0) }}</td>
        </tr>
        <tr>
            <td><strong>Grand Total</strong></td>
            <td>: Rp {{ number_format($header->grand_total ?? 0, 0) }}</td>

            <td></td>
            <td></td>
        </tr>
    </table>
    <br>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIK</th>
                <th>Nama Anggota</th>
                <th>Jabatan</th>
                <th>Dana Cadangan</th>
                <th>Simpanan Pokok</th>
                <th>Simpanan Wajib</th>
                <th>Simpanan SHT</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $i => $row)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $row['nip'] }}</td>
                <td>{{ $row['name'] }}</td>
                <td>{{ $row['position'] }}</td>
                <td class="angka">{{ number_format($row['simpanan_cadangan'], 0, ',', '.') }}</td>
                <td class="angka">{{ number_format($row['simpanan_pokok'], 0, ',', '.') }}</td>
                <td class="angka">{{ number_format($row['simpanan_wajib'], 0, ',', '.') }}</td>
                <td class="angka">{{ number_format($row['simpanan_sht'], 0, ',', '.') }}</td>
                <td class="angka">{{ number_format($row['total'], 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align:center;">Tidak ada data</td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>


