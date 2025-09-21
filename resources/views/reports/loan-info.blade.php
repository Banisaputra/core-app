<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk</title>
    <style>
        @page {
            margin: 5px;
        }
        body {
            margin: 5px;
            padding: 5px;
            font-family: monospace;
            font-size: 12px;
        }
        h1,h2,h3,h4,h5,h6 { margin: 0px;}
        .center { text-align: center; }
        .right { text-align: right; }
        .line { border-top: 1px dashed #000; margin: 5px 0; }
        .item { margin: 1px 0; }
        .date { margin:15px 0; }
    </style>
</head>
<body>
    <div class="center">
        <h3>Koperasi Karyawan</h3>
        <h4>Hardo Soloplast</h4>
        <small>Jl. Raya No. 123</small>
    </div>

    <div class="date">No. Transaksi: <br>{{ $loan->loan_code }}<br>
       Tanggal: {{ \Carbon\Carbon::parse($loan->created_at)->format('d-m-Y H:i') }}</div>

    <div class="line"></div>
   <table>
        <tr>
            <td>Janis Pinjaman</td>
            <td>{{$loan->loan_type}}</td>
        </tr> 
        <tr>
            <td>NIK</td>
            <td>{{$loan->member->nip}}</td>
        </tr> 
        <tr>
            <td>Nama</td>
            <td>{{$loan->member->name}}</td>
        </tr> 
        <tr>
            <td>Telphone</td>
            <td>{{$loan->member->telphone}}</td>
        </tr>
        <tr>
            <td>Jumlah Pinjaman</td>
            <td>Rp {{number_format($loan->loan_value) }}</td>
        </tr>
        <tr>
            <td>Tenor</td>
            <td>{{$loan->loan_tenor}}</td>
        </tr>
        <tr>
            <td>Angsuran</td>
            <td>Rp {{ number_format($loan->payments[0]['lp_value']) }}</td>
        </tr>       
    </table>
    <div class="line"></div>

    {{-- <p class="right"><b>Total: Rp {{ number_format($sale->sub_total) }}</b></p> --}}

    <div class="center">
        <p>-- Terima Kasih --</p>
    </div>
</body>
</html>
