@extends('layouts.main')

@section('title')
    <title>View Simpanan - Sistem Informasi Koperasi dan Usaha</title>
@endsection
@section('page_css')

@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
        <div class="row align-items-center my-4">
            <div class="col">
            <h2 class="h3 mb-0 page-title">Detail Simpanan</h2>
            </div>
        </div>
        <hr class="my-4"> 
        
            <div class="row">
                <div class="col-4">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <img src="{{ $saving->proof_of_payment != '' && file_exists(asset('storage/'.$saving->proof_of_payment)) 
                            ? asset('storage/'.$saving->proof_of_payment) 
                            : asset('images/default.png') }}" alt="profile" width="300px">
                        </div>
                    </div>
                </div>
                <div class="col-8">
                    <div class="row">
                        <p for="nip" class="col-sm-3 text-right">NIP</p>
                        <div class="col-sm-9">
                            <h5>{{ $saving->member->nip }}</h5>
                        </div>
                    </div>
                    <div class="row">
                        <p class="col-sm-3 text-right">Nama</p>
                        <div class="col-sm-9">
                            <h5>{{ ucwords($saving->member->name) }}</h5>
                        </div>
                    </div> 
                    <div class="row">
                        <p class="col-sm-3 text-right">No.Tlpn</p>
                        <div class="col-sm-9">
                            <h5>{{ $saving->member->telphone }}</h5>
                        </div>
                    </div>
                    <div class="row">
                        <p class="col-sm-3 text-right">Alamat</p>
                        <div class="col-sm-9">
                            <h5>{{ $saving->member->address }}</h5>
                        </div>
                    </div>
                    <hr class="my-4">
                    <div class="row">
                        <p class="col-sm-3 text-right">Tgl. Simpanan</p>
                        <div class="col-sm-9">
                            <h5>{{ date('d M Y', strtotime($saving->sv_date)) }}</h5>
                        </div>
                    </div>
                    <div class="row">
                        <p class="col-sm-3 text-right">Jenis Simpanan</p>
                        <div class="col-sm-9">
                            <h5>{{ $saving->svType->name }}</h5>
                        </div>
                    </div>
                    <div class="row">
                        <p class="col-sm-3 text-right">Jumlah Simpanan</p>
                        <div class="col-sm-9">
                            <h5>Rp {{ number_format($saving->sv_value, 0) }},-</h5>
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