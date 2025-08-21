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

    <div class="date">No. Transaksi: <br>{{ $sale->sa_code }}<br>
       Tanggal: {{ \Carbon\Carbon::parse($sale->created_at)->format('d-m-Y H:i') }}</div>

    <div class="line"></div>
    @foreach($sale->saDetail as $d)
    <div class="item">
        {{ $d->item->item_name }} <br>
        {{ $d->amount }} x {{ number_format($d->price) }} 
        <span class="right" style="float: right;">{{ number_format($d->total) }}</span>
    </div>
    @endforeach
    <div class="line"></div>

    <p class="right"><b>Total: Rp {{ number_format($sale->sub_total) }}</b></p>

    <div class="center">
        <p>-- Terima Kasih --</p>
    </div>
</body>
</html>
