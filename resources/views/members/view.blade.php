@extends('layouts.main')

@section('page_css')

@endsection

@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12 col-xl-10">
      <div class="row align-items-center my-4">
        <div class="col">
          <h2 class="h3 mb-0 page-title">Detail Anggota</h2>
        </div>
      </div>
      <hr class="my-4">
      <h5 class="mb-2 mt-4">Personal</h5>
      <p class="mb-4">Data personal</p>
       
    <div class="row">
        <div class="col-4">
            <div class="card shadow mb-4">
                <div class="card-body text-center">
                    <img src="{{ asset('storage/'.$member->image) }}" alt="..." width="300px">
                </div>
            </div>
        </div>
        <div class="col-8">
            <div class="row">
                <p for="nip" class="col-sm-3 text-right">NIP</p>
                <div class="col-sm-9">
                    <h5>{{ $member->nip }}</h5>
                </div>
            </div>
            <div class="row">
                <p class="col-sm-3 text-right">Nama</p>
                <div class="col-sm-9">
                    <h5>{{ ucwords($member->name) }}</h5>
                </div>
            </div>
            <div class="row">
                <p class="col-sm-3 text-right">Email</p>
                <div class="col-sm-9">
                    <h5>{{ $member->user['email'] }}</h5>
                </div>
            </div>
            <div class="row">
                <p class="col-sm-3 text-right">No.Tlpn</p>
                <div class="col-sm-9">
                    <h5>{{ $member->telphone }}</h5>
                </div>
            </div>
            <div class="row">
                <p class="col-sm-3 text-right">Alamat</p>
                <div class="col-sm-9">
                    <h5>{{ $member->address }}</h5>
                </div>
            </div>
            <div class="row">
                <p class="col-sm-3 text-right">Tanggal Bergabung</p>
                <div class="col-sm-9">
                    <h5>{{ date('d - M - Y', strtotime($member->date_joined)) }}</h5>
                </div>
            </div>
            <div class="row">
                <p class="col-sm-3 text-right">Saldo</p>
                <div class="col-sm-9">
                    <h5>Rp{{ number_format($member->balance, 0) }}</h5>
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