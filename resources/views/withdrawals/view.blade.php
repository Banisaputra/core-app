@extends('layouts.main')

@section('name')
    <title>View Penarikan - Sistem Informasi Koperasi dan Usaha</title>
@endsection
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
                            <img src="{{ $withdrawal->proof_of_withdrawal != '' && file_exists(asset('storage/'.$withdrawal->proof_of_withdrawal)) 
                            ? asset('storage/'.$withdrawal->proof_of_withdrawal) 
                            : asset('images/default.png') }}" alt="profile" width="300px">
                        </div>
                    </div>
                </div>
                <div class="col-8">
                    <div class="row">
                        <p class="col-sm-3 text-right">NIK</p>
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

                    <hr class="my-4">
                    <div class="form-inline">
                        <div class="col-md-4 text-center">
                            <form action="{{ route('withdrawals.confirm')}}" method="POST">
                                @csrf
                                <input type="hidden" value="{{ $withdrawal->id }}" name="wd_id">
                                <button type="submit" class="btn btn-primary" {{ $withdrawal->wd_state > 1 ? "disabled" : "" }}><span class="fe fe-16 mr-2 fe-check-circle"></span>Konfirmasi Simpanan</button>
                            </form>
                        </div>
                        <div class="col-md-4 text-center">
                            <form action="{{ route('withdrawals.destroy', $withdrawal->id)}}" method="POST">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" value="{{ $withdrawal->id }}" name="wd_id">
                                <button type="submit" class="btn btn-danger" {{ $withdrawal->wd_state > 1 ? "disabled" : "" }}><span class="fe fe-16 mr-2 fe-slash"></span>Batalkan Simpanan</button>
                            </form>
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