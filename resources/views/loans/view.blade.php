@extends('layouts.main')

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
                            <img src="{{ asset('storage/'.$loan->member->image) }}" alt="profile" width="300px">
                        </div>
                    </div>
                </div>
                <div class="col-8">
                    <div class="row">
                        <p for="nip" class="col-sm-3 text-right">NIP</p>
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
                    <a href="{{ route('loanPayments.create') }}" class="btn mb-2 btn-primary">
                        <span class="fe fe-plus fe-16 mr-1"></span> Tambah Angsuran
                    </a>
                </div>
                <div class="row my-4">
          <!-- Small table -->
          <div class="col-md-12">
            <div class="card shadow">
              <div class="card-body">
                <!-- table -->
                <table class="table datatables" id="loans">
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
                    
                  </tbody>
                </table>
              </div>
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