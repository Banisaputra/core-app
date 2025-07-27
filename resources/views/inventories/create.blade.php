@extends('layouts.main')

@section('title')
    <title>Tambah Inventory - Sistem Informasi Koperasi dan Usaha</title>
@endsection

@section('page_css')
    
@endsection

@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12 col-xl-10">
      <div class="row align-items-center my-4">
        <div class="col">
          <h2 class="h3 mb-0 page-title">Tambah Koreksi Persediaan</h2>
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
      <form action="{{ route('inv.store')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-row">
          <div class="form-group col-md-4">
            <label for="svDate">Kode</label>
            <h5>{{ $inv_code }}</h5>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="inv_date">Tanggal Pembelian</label>
            <input type="date" class="form-control" id="inv_date" name="inv_date" value="{{old('inv_date')}}">
          </div>
          <div class="form-group col-md-6">
            <label for="type">Jenis Koreksi</label>
            <select class="custom-select" name="type" id="type">
              <option value="">-- Pilih Jenis Koreksi</option>
              <option value="ADJUSTMENT IN">Penambahan</option>
              <option value="ADJUSTMENT OUT">Pengurangan</option>
              <option value="OPERATIONAL">Operasional</option>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-12">
            <label for="remark">Keterangan Koreksi</label>
            <textarea class="form-control" id="remark" rows="5" name="remark">{{old('address')}}</textarea>
          </div> 
        </div>
        <hr class="my-4">

        <!-- Tabel Item -->
        <div class="table-responsive">
          <table class="table table-bordered" id="purchaseTable">
            <thead class="thead-dark">
              <tr>
                <th width="55%">Nama Barang</th>
                <th width="40%">Qty</th>
                <th><button type="button" class="btn btn-sm btn-success" onclick="addRow()">+</button></th>
              </tr>
            </thead>
            <tbody id="itemsBody">
              <tr>
                <td><select name="items[0][item_id]" class="form-control itemSelect" required></select></td>
                <td><input type="number" name="items[0][qty]" class="form-control qty" min="1" required></td>
                <td><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)">x</button></td>
              </tr>
              </tbody>                
          </table>
        </div>
        <hr class="my-4">
        <div class="form-row">
          <div class="col-md-6">
            {{-- other note --}}
          </div>
          <div class="col-md-6 text-right">
            <button type="submit" class="btn btn-primary"><span class="fe fe-16 mr-2 fe-check-circle"></span>Simpan</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('page_script')
<script src="{{ asset('fedash/js/inventory.js')}}"></script>

<script>
  $(document).ready(function () {
    initSelectItem();

  });
</script>
@endsection