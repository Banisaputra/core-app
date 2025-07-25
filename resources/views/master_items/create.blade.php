@extends('layouts.main')

@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12 col-xl-10">
      <div class="row align-items-center my-4">
        <div class="col">
          <h2 class="h3 mb-0 page-title">Tambah Barang</h2>
        </div>
      </div>
      <form action="{{ route('items.store')}}" method="POST" enctype="multipart/form-data">
        @csrf
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
          <div class="form-group col-md-3">
            <label for="item_code">Kode Barang</label>
            <input type="text" class="form-control" id="item_code" name="item_code" value="{{old('item_code')}}">
          </div>
          <div class="form-group col-md-6">
            <label for="item_name">Nama Barang</label>
            <input type="text" class="form-control" id="item_name" name="item_name" value="{{old('item_name')}}">
          </div>
          <div class="form-group col-md-3">
            <label for="simple-select2">Kategori</label>
            <select id="categorySelect" name="category_id" class="form-control"></select>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-3">
            <label for="sales_price">Harga Jual</label>
            <input type="number" class="form-control" id="sales_price" name="sales_price" value="{{old('sales_price')}}">
          </div>
          <div class="form-group col-md-3">
            <label for="stock">Stok</label>
            <input type="number" class="form-control" id="stock" name="stock" value="{{old('stock')}}">
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
                <img id="preview-image" src="" alt="Preview" style="max-width: 300px;" hidden>
            </div>
          </div>
        </div>
        <hr class="my-4">
        <div class="form-row">
          <div class="col-md-6">
            <small></small>
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
    $('#categorySelect').select2({
        placeholder: 'Search category...',
        theme: 'bootstrap4',
        minimumInputLength: 2,
        ajax: {
            url: '/api/category/search', // Your route
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term // search term
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function (item) {
                        return {
                            id: item.id,
                            text: item.name
                        };
                    })
                };
            },
            cache: true
        }
    });

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