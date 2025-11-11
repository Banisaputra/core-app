@extends('layouts.main')

@section('title')
    <title>View Pinjaman - Sistem Informasi Koperasi dan Usaha</title>
@endsection

@section('page_css')
    <link rel="stylesheet" href="{{ asset('fedash/css/dataTables.bootstrap4.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="row align-items-center my-4">
                <div class="col">
                    <h2 class="h3 mb-0 page-title">Detail Pinjaman</h2>
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
                            <img src="{{ $loan->member->image != '' && file_exists(asset('storage/'.$loan->member->image)) 
                            ? asset('storage/'.$loan->member->image) 
                            : asset('images/default.png') }}" alt="profile" width="300px">
                        </div>
                    </div>
                    <div class="text-center">
                        <form action={{ route('reports.loanInfo') }} method="POST" id="form-report" enctype="multipart/form-data" target="_blank">
                            @csrf
                            <input type="hidden" name="loan_id" value="{{$loan->id}}">
                            <button type="submit" class="btn mb-2 btn-primary">
                                <span class="fe fe-plus fe-16 mr-1"></span> Cetak Info
                            </button>
                        </form>
                    </div>
                </div>
                <div class="col-8">
                    <div class="row">
                        <p class="col-sm-3 text-right">Kode</p>
                        <div class="col-sm-9">
                            <h5>{{ $loan->loan_code }}</h5>
                        </div>
                    </div>
                    <div class="row">
                        <p class="col-sm-3 text-right">NIK</p>
                        <div class="col-sm-9">
                            <h5>{{ $loan->member->nip }}</h5>
                        </div>
                    </div>
                    <div class="row">
                        <p class="col-sm-3 text-right">Nama</p>
                        <div class="col-sm-9">
                            <h5>{{ ucwords($loan->member->name) }}</h5>
                        </div>
                    </div> 
                    <div class="row">
                        <p class="col-sm-3 text-right">No.Tlpn</p>
                        <div class="col-sm-9">
                            <h5>{{ $loan->member->telphone }}</h5>
                        </div>
                    </div>
                    <div class="row">
                        <p class="col-sm-3 text-right">Alamat</p>
                        <div class="col-sm-9">
                            <h5>{{ $loan->member->address }}</h5>
                        </div>
                    </div>
                    <hr class="my-4">
                    <div class="row">
                        <p class="col-sm-3 text-right">Jenis Pinjaman</p>
                        <div class="col-sm-9">
                            <h5>{{ ucwords(strtolower($loan->loan_type)) }}</h5>
                        </div>
                    </div>
                    <div class="row">
                        <p class="col-sm-3 text-right">Tgl. Pinjaman</p>
                        <div class="col-sm-9">
                            <h5>{{ date('d M Y', strtotime($loan->loan_date)) }}</h5>
                        </div>
                    </div>
                    <div class="row">
                        <p class="col-sm-3 text-right">Jumlah Pinjaman</p>
                        <div class="col-sm-9">
                            <h5>Rp {{ number_format($loan->loan_value, 0) }},-</h5>
                        </div>
                    </div>
                    <div class="row">
                        <p class="col-sm-3 text-right">Tenor Pinjaman</p>
                        <div class="col-sm-9">
                            <h5>{{ $loan->loan_tenor }} Bulan</h5>
                        </div>
                    </div>
                    <div class="row">
                        <p class="col-sm-3 text-right">Angsuran Perbulan</p>
                        <div class="col-sm-9">
                            <h5>Rp {{ number_format($loan->payments[0]->lp_total, 0) }},-</h5>
                        </div>
                    </div>
                    <div class="row">
                        <p class="col-sm-3 text-right">Tgl. Jatuh Tempo</p>
                        <div class="col-sm-9">
                            <h5>{{ date('d M Y', strtotime($loan->due_date)) }}</h5>
                        </div>
                    </div>
                    <div class="row">
                        <p class="col-sm-3 text-right">Status Pinjaman</p>
                        <div class="col-sm-9">
                            <h5>@switch($loan->loan_state)
                                @case(99)
                                    Ditolak
                                    @break
                                @case(2)
                                    Disetujui
                                    @break
                                @default
                                    Pengajuan
                            @endswitch</h5>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="row align-items-center my-4">
                <div class="col">
                    <h2 class="h3 mb-2 page-title">Detail Angsuran</h2>
                </div>
            </div>
            <div class="row my-4">
                <div class="col-md-12">
                    <div class="card shadow">
                        <div class="card-body">
                            <table class="table datatables" id="loanPayment">
                            <thead>
                                <tr>
                                    <th width="5%">No.</th>
                                    <th width="10%">Kode</th>
                                    <th width="15%">Tgl. Angsuran</th>
                                    <th width="15%">Jumlah</th>
                                    <th width="15%">Sisa Pinjaman</th>
                                    <th width="15%">Status</th>
                                    <th width="5%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($loan->payments as $pay)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $pay->lp_code }}</td>
                                    <td>{{ date('d M Y', strtotime($pay->lp_date)) }}</td>
                                    <td>Rp {{ number_format($pay->lp_total, 2) }}</td>
                                    <td>Rp {{ number_format($pay->loan_remaining, 2) }}</td>
                                    <td>
                                    @switch($pay->lp_state)
                                        @case(99)
                                            Ditutup
                                            @break
                                        @case(2)
                                            Dibayarkan
                                            @break
                                        @default
                                            Belum dibayar
                                    @endswitch
                                    </td>
                                    <td><button class="btn btn-sm dropdown-toggle more-horizontal" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <span class="text-muted sr-only">Action</span>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right"> 
                                        <form action="{{ route('loanPayments.settle') }}" method="POST" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="lp_id" value="{{$pay->id}}">
                                            <button type="submit" id="btnSettle" class="dropdown-item">Pelunasan</button>
                                        </form>
                                    </div>
                                    </td> 
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
    $('#loanPayment').DataTable({
        autoWidth: true,
        "lengthMenu": [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ]
    });
})
</script>
@endsection