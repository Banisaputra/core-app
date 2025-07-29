@extends('layouts.main')

@section('title')
    <title>View Inventory - Sistem Informasi Koperasi dan Usaha</title>
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
                    <h2 class="h3 mb-0 page-title">Detail Koreksi</h2>
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
                <div class="col-12">
                    <div class="row">
                        <p for="nip" class="col-sm-3 text-right">Kode</p>
                        <div class="col-sm-9">
                            <h5>{{ $inventory->code }}</h5>
                        </div>
                    </div>
                     <div class="row">
                        <p class="col-sm-3 text-right">Tgl. Koreksi</p>
                        <div class="col-sm-9">
                            <h5>{{ date('d M Y', strtotime($inventory->inv_date)) }}</h5>
                        </div>
                    </div>
                    <div class="row">
                        <p class="col-sm-3 text-right">Jenis</p>
                        <div class="col-sm-9">
                            <h5>{{ $inventory->type }}</h5>
                        </div>
                    </div>
                    <div class="row">
                        <p class="col-sm-3 text-right">Keterangan</p>
                        <div class="col-sm-9">
                            <h5>{!! $inventory->remark !!}</h5>
                        </div>
                    </div>
                    
                    <div class="row">
                        <p class="col-sm-3 text-right">Status</p>
                        <div class="col-sm-9">
                            <h5>
                                @switch($inventory->inv_state)
                                    @case(99)
                                        Dibatalkan
                                        @break
                                    @case(2)
                                        Sudah Dikonfirmasi
                                        @break
                                    @default
                                        Belum Dikonfirmasi
                                @endswitch
                            </h5>
                        </div>
                    </div>
                     
                    <hr class="my-4">
                    <div class="form-inline">
                        <div class="col-md-4 text-center">
                            <form action="{{ route('inv.confirm')}}" method="POST">
                                @csrf
                                <input type="hidden" value="{{ $inventory->id }}" name="inv_id">
                                <button type="submit" class="btn btn-primary" {{ $inventory->inv_state > 1 ? "disabled" : "" }}><span class="fe fe-16 mr-2 fe-check-circle"></span>Konfirmasi Pembelian</button>
                            </form>
                        </div>
                        <div class="col-md-4 text-center">
                            <form action="{{ route('inv.destroy', $inventory->id)}}" method="POST">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" value="{{ $inventory->id }}" name="inv_id">
                                <button type="submit" class="btn btn-danger" {{ $inventory->inv_state > 1 ? "disabled" : "" }}><span class="fe fe-16 mr-2 fe-slash"></span>Batalkan Pembelian</button>
                            </form>
                        </div>
                    </div>
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
                            <table class="table datatables" id="invDetail">
                            <thead>
                                <tr>
                                    <th width="5%">No.</th>
                                    <th width="10%">Kode</th>
                                    <th width="15%">Nama</th>
                                    <th width="15%">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invDetails as $invd)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $invd['item_code'] }}</td>
                                    <td>{{ $invd['item_name'] }}</td>
                                    <td>{{ $invd['total_qty'] }}</td>
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
    $('#invDetail').DataTable({
        autoWidth: true,
        "lengthMenu": [
            [10, 25, 50, -1],
            [10, 25, 50, "All"]
        ]
    });
})
</script>
@endsection