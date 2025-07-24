@extends('layouts.main')

@section('page_css')

@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
        <div class="row align-items-center my-4">
            <div class="col">
            <h2 class="h3 mb-0 page-title">Detail Penarikan</h2>
            </div>
        </div>
        <hr class="my-4"> 
        
            <div class="row">
                <div class="col-4">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <img src="{{ file_exists(asset('storage/'.$withdrawal->proof_of_withdrawal)) 
                            ? asset('storage/'.$withdrawal->proof_of_withdrawal) 
                            : asset('images/default.png') }}" alt="profile" width="300px">
                        </div>
                    </div>
                </div>
                <div class="col-8">
                    <div class="row">
                        <p for="nip" class="col-sm-3 text-right">NIP</p>
                        <div class="col-sm-9">
                            <h5>{{ $withdrawal->member->nip }}</h5>
                        </div>
                    </div>
                    <div class="row">
                        <p class="col-sm-3 text-right">Nama</p>
                        <div class="col-sm-9">
                            <h5>{{ ucwords($withdrawal->member->name) }}</h5>
                        </div>
                    </div> 
                    <div class="row">
                        <p class="col-sm-3 text-right">No.Tlpn</p>
                        <div class="col-sm-9">
                            <h5>{{ $withdrawal->member->telphone }}</h5>
                        </div>
                    </div>
                    <div class="row">
                        <p class="col-sm-3 text-right">Alamat</p>
                        <div class="col-sm-9">
                            <h5>{{ $withdrawal->member->address }}</h5>
                        </div>
                    </div>
                    <hr class="my-4">
                    <div class="row">
                        <p class="col-sm-3 text-right">Tgl. Penarikan</p>
                        <div class="col-sm-9">
                            <h5>{{ date('d M Y', strtotime($withdrawal->wd_date)) }}</h5>
                        </div>
                    </div>
                    <div class="row">
                        <p class="col-sm-3 text-right">Jumlah Penarikan</p>
                        <div class="col-sm-9">
                            <h5>Rp {{ number_format($withdrawal->wd_value, 0) }},-</h5>
                        </div>
                    </div>
                    <div class="row">
                        <p class="col-sm-3 text-right">Catatan</p>
                        <div class="col-sm-9">
                            <p>{!! $withdrawal->remark !!}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('page_script')
 
@endsection