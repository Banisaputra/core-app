<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page {
            margin: 15mm 10mm;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
        }

        h3 {
            margin: 0 0 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px;
            vertical-align: middle;
        }

        th {
            background: #eee;
            text-align: center;
        }

        td.angka {
            text-align: right;
            white-space: nowrap;
        }

        tr {
            page-break-inside: avoid;
        }

        thead {
            display: table-header-group;
        }
    </style>
</head>
<body>

<h3>Laporan Summary Pinjaman</h3>

<p>
    <strong>Filter:</strong>
</p>
<ul>
@foreach ($filter as $key => $value)
    <li>{{ $key }} : {{ $value }}</li>
@endforeach
</ul>

<table style="margin-bottom:10px;">
    <tr>
        <td><strong>Total Pinjaman Uang</strong></td>
        <td class="angka">Rp {{ number_format($header->total_pinjaman_uang ?? 0, 0, ',', '.') }}</td>

        <td><strong>Total Pinjaman Barang</strong></td>
        <td class="angka">Rp {{ number_format($header->total_pinjaman_barang ?? 0, 0, ',', '.') }}</td>

        <td><strong>Grand Total</strong></td>
        <td class="angka">Rp {{ number_format($header->grand_total ?? 0, 0, ',', '.') }}</td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>NIK</th>
            <th>Nama Anggota</th>
            <th>Jabatan</th>
            <th>Jenis</th>
            <th>Jumlah Pinjaman</th>
            <th>Tenor</th>
            <th>Pokok</th>
            <th>Bunga</th>
            <th>Angsuran Ke</th>
            <th>Total Tagihan</th>
            <th>Sisa Pinjaman</th>
        </tr>
    </thead>
    <tbody>

@forelse ($data as $row)
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $row->nip }}</td>
    <td>{{ $row->name }}</td>
    <td>{{ $row->position }}</td>

    <td>{{ strtoupper($row->jenis_pinjaman) }}</td>
    <td class="angka">{{ number_format($row->jumlah_pinjaman ?? 0, 0, ',', '.') }}</td>
    <td>{{ $row->loan_tenor ?? 0 }}</td>
    <td class="angka">{{ number_format($row->pokok ?? 0, 0, ',', '.') }}</td>
    <td class="angka">{{ number_format($row->bunga ?? 0, 0, ',', '.') }}</td>
    <td>{{ $row->angsuran_ke ?? '-' }}</td>
    <td class="angka">{{ number_format($row->total_tagihan ?? 0, 0, ',', '.') }}</td>
    <td class="angka">{{ number_format($row->sisa_pinjaman ?? 0, 0, ',', '.') }}</td>
</tr>
@empty
<tr>
    <td colspan="12" align="center">Tidak ada data</td>
</tr>
@endforelse

    </tbody>
</table>

</body>
</html>
