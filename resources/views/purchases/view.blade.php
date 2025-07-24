@extends('layouts.main')

@section('page_css')
    <link rel="stylesheet" href="{{ asset('fedash/css/dataTables.bootstrap4.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="row align-items-center my-4">
                <div class="col">
                    <h2 class="h3 mb-0 page-title">Detail Pembelian</h2>
                </div>
            </div>
            <hr class="my-4"> 
            @if ($errors->any())
            <div class="alert alert-danger" role="alert">
            @foreach ($errors->all() as $error)
                <span class="fe fe-minus-circle fe-16 mr-2"></span> {{ $error }} <br>           
            @endforeach
            </div>
            @endif
            @if (session()->has('error'))
            <div class="alert alert-danger" role="alert">
                <span class="fe fe-minus-circle fe-16 mr-2"></span> {{ session('error') }} <br>           
            </div>
            @endif
            @if (session()->has('success'))
            <div class="alert alert-success" role="alert">
                <span class="fe fe-help-circle fe-16 mr-2"></span> {{ session('success') }} <br>           
            </div>
            @endif
            <div class="row">
                <div class="col-4">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <img src="{{ file_exists(asset('storage/'.$purchase->file_path)) 
                            ? asset('storage/'.$purchase->file_path) 
                            : asset('images/default.png') }}" alt="profile" width="300px">
                        </div>
                    </div>
                </div>
                <div class="col-8">
                    <div class="row">
                        <p for="nip" class="col-sm-3 text-right">Kode</p>
                        <div class="col-sm-9">
                            <h5>{{ $purchase->pr_code }}</h5>
                        </div>
                    </div>
                     <div class="row">
                        <p class="col-sm-3 text-right">Tgl. Pembelian</p>
                        <div class="col-sm-9">
                            <h5>{{ date('d M Y', strtotime($purchase->pr_date)) }}</h5>
                        </div>
                    </div>
                    <div class="row">
                        <p class="col-sm-3 text-right">Invoice</p>
                        <div class="col-sm-9">
                            <h5>{{ $purchase->ref_doc }}</h5>
                        </div>
                    </div> 
                    <div class="row">
                        <p class="col-sm-3 text-right">Supplier</p>
                        <div class="col-sm-9">
                            <h5>{{ $purchase->supplier }}</h5>
                        </div>
                    </div>
                    <div class="row">
                        <p class="col-sm-3 text-right">Total Pembelian</p>
                        <div class="col-sm-9">
                            <h5>Rp {{ number_format($purchase->total, 0) }},-</h5>
                        </div>
                    </div>
                     
                    <hr class="my-4">
                   
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center my-4">
                <div class="col">
                    <h2 class="h3 mb-2 page-title">Detail Barang</h2>
                </div>
            </div>
            <div class="row my-4">
                <div class="col-md-12">
                    <div class="card shadow">
                        <div class="card-body">
                            <table class="table datatables" id="prDetail">
                            <thead>
                                <tr>
                                    <th width="5%">No.</th>
                                    <th width="10%">Kode</th>
                                    <th width="15%">Nama</th>
                                    <th width="15%">Jumlah</th>
                                    <th width="15%">Harga</th>
                                    <th width="15%">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($purchase->prDetails as $prd)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $prd->item['item_code'] }}</td>
                                    <td>{{ $prd->item['item_name'] }}</td>
                                    <td>{{ $prd->amount }}</td>
                                    <td>Rp {{ number_format($prd->price, 2) }}</td>
                                    <td>Rp {{ number_format($prd->total, 2) }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_script')
<script src="{{ asset('fedash/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('fedash/js/dataTables.bootstrap4.min.js') }}"></script>
<script>
$(document).ready(function () {
    $('#prDetail').DataTable({
        autoWidth: true,
        "lengthMenu": [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ]
    });
})
</script>
@endsection