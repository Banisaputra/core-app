@extends('layouts.main')

@section('page_css')

@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
        <div class="row align-items-center my-4">
            <div class="col">
            <h2 class="h3 mb-0 page-title">Detail Barang</h2>
            </div>
        </div>
        <hr class="my-4"> 
            <div class="row">
                <div class="col-4">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <img src="{{ asset('storage/'.$item->item_image) }}" alt="item" width="300px">
                        </div>
                    </div>
                </div>
                <div class="col-8">
                    <div class="row">
                        <p for="nip" class="col-sm-3 text-right">Kode</p>
                        <div class="col-sm-9">
                            <h5>{{ $item->item_code }}</h5>
                        </div>
                    </div>
                    <div class="row">
                        <p class="col-sm-3 text-right">Nama</p>
                        <div class="col-sm-9">
                            <h5>{{ $item->item_name }}</h5>
                        </div>
                    </div> 
                    <div class="row">
                        <p class="col-sm-3 text-right">Stok</p>
                        <div class="col-sm-9">
                            <h5>{{ $item->stock }}</h5>
                        </div>
                    </div>
                    <div class="row">
                        <p class="col-sm-3 text-right">Harga Jual</p>
                        <div class="col-sm-9">
                            <h5>Rp {{ number_format($item->sales_price, 0) }},-</h5>
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