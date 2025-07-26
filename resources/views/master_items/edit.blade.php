@extends('layouts.main')

@section('title')
    <title>Edit Barang - Sistem Informasi Koperasi dan Usaha</title>
@endsection
@section('page_css')
    
@endsection
@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12 col-xl-10">
      <div class="row align-items-center my-4">
        <div class="col">
          <h2 class="h3 mb-0 page-title">Edit Barang</h2>
        </div>
      </div>
      <form action="{{ route('items.update', $item->id)}}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
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
        <div class="form-row">
          <div class="form-group col-md-5">
            <label for="item_code">Kode Barang</label>
            <input type="text" class="form-control" id="item_code" name="item_code" value="{{old('item_code', $item->item_code ?? '')}}">
          </div>
          <div class="form-group col-md-7">
            <label for="item_name">Nama Barang</label>
            <input type="text" class="form-control" id="item_name" name="item_name" value="{{old('item_name', $item->item_name ?? '')}}">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-3">
            <label for="sales_price">Harga Jual</label>
            <input type="number" class="form-control" id="sales_price" name="sales_price" value="{{old('sales_price', $item->sales_price ?? '')}}">
          </div>
          <div class="form-group col-md-3">
            <label for="stock">Stok</label>
            <input type="number" class="form-control" id="stock" name="stock" value="{{old('stock', $item->stock)}}">
          </div>
          <div class="form-group col-md-6">
            <label for="item_image">Gambar</label>
            <div class="custom-file">
              <input type="file" class="custom-file-input" id="item_image" name="item_image">
              <label class="custom-file-label" for="item_image" id="label_photo">Choose file</label>
              <small>*Format file jpg/jpeg,png dengan ukuran max:2MB</small>
            </div>
            <!-- Preview container -->
            <div class="mt-2">
                <img id="preview-image" src="{{ asset('storage/'. $item->item_image)}}" alt="Preview" style="max-width: 300px;">
            </div>
          </div>
        </div>
        <hr class="my-4">
        <div class="form-row">
          <div class="col-md-6">
            <small>*Kode dibuat otomatis oleh sistem</small>
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
<script>
  $(document).ready(function () { 

    $('#item_image').on('change', function(event) {
      const file = event.target.files[0];
      const preview = $('#preview-image');
      const fileNameDisplay = $('#label_photo');
      fileNameDisplay.html( file ? file.name.substr(1, 70) : 'Choose file');
      if (file) {
        const reader = new FileReader(); 
        reader.onload = function(e) {
            preview.prop('src', e.target.result);
            preview.prop('hidden', false);
        }
        reader.readAsDataURL(file);
      } else {
        preview.prop('src' , '');
        preview.prop('hidden' , true);
      }
    });


  });
</script>
@endsection