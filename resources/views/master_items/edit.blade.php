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
          <div class="form-group col-md-3">
            <label for="item_code">Kode Barang</label>
            <input type="text" class="form-control" id="item_code" name="item_code" value="{{old('item_code', $item->item_code ?? '')}}">
          </div>
          <div class="form-group col-md-6">
            <label for="item_name">Nama Barang</label>
            <input type="text" class="form-control" id="item_name" name="item_name" value="{{old('item_name', $item->item_name ?? '')}}">
          </div>
          <div class="form-group col-md-3">
            <label for="stock">Stok</label>
            <input type="number" class="form-control" id="stock" name="stock" value="{{$item->stock}}" readonly>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-4">
            <label for="simple-select2">Kategori</label>
            <select id="categorySelect" name="category_id" class="form-control">
              @if ($item->category)
              <option value="{{$item->category->id}}" selected>{{$item->category->name}}</option>
              @endif
            </select>
          </div>
          <div class="form-group col-md-2">
            <label for="margin_percent">Margin (%)</label>
            <input type="number" class="form-control" id="margin_percent" name="margin_percent" value="{{old('margin_percent', $item->category->margin_percent ?? 0)}}" readonly>
          </div>
          <div class="form-group col-md-2">
            <label for="margin_price">Margin (Rp)</label>
            <input type="number" class="form-control" id="margin_price" name="margin_price" value="{{old('margin_price', $item->category->margin_price ?? 0)}}" readonly>
          </div>
          <div class="form-group col-md-4">
            <label for="hpp">Harga Pokok</label>
            <input type="number" class="form-control" id="hpp" name="hpp" value="{{old('hpp', $item->hpp ?? 0)}}">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="sales_price">Harga Jual</label>
            <input type="number" class="form-control" id="sales_price" name="sales_price" value="{{old('sales_price', $item->sales_price ?? 0)}}">
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
                <img id="preview-image" src="{{ asset('storage/'. $item->item_image)}}" alt="preview" style="max-width: 300px;" {{ $item->item_image != '' && file_exists(public_path('storage/'.$item->item_image)) ? "" : "hidden"}}>
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
   document.getElementById('item_image').addEventListener('change', function(event) {
      const file = event.target.files[0];
      const preview = document.getElementById('preview-image');
      const fileNameDisplay = document.getElementById('label_photo');
      
      // Tampilkan nama file
      fileNameDisplay.textContent = file ? file.name.substr(1, 70) : 'Choose file';
    
      // Tampilkan preview gambar
      if (file) {
          const reader = new FileReader();
          
          reader.onload = function(e) {
              preview.src = e.target.result;
              preview.hidden = false;
          }
          
          reader.readAsDataURL(file);
      } else {
          preview.src = '';
          preview.hidden = true;
      }
    });
</script>
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
                            text: "["+item.code+"] "+item.name,
                            mp: 100
                        };
                    })
                };
            },
            cache: true
        }
    }).on('change', function(e) {
      var selectedId = $(this).val();
      if (selectedId) {
          // Lakukan request untuk mendapatkan detail margin
          $.ajax({
              url: '/api/category/' + selectedId + '/margin',
              method: 'GET',
              dataType: 'json',
              success: function(response) {
                  $('#margin_percent').val(response.margin_percent || '0');
                  $('#margin_price').val(response.margin_price || '0');
                  calculateHargaJual();
                   
              },
              error: function(xhr) {
                  console.error('Error fetching margin data:', xhr.responseText);
                  // Set default values jika error
                  $('#margin_percent').val('0');
                  $('#margin_price').val('0');
              }
          });
      } else {
          // Reset nilai jika tidak ada kategori dipilih
          $('#margin_percent').val('0');
          $('#margin_price').val('0');
      }
    });

    $('#hpp').on('input', function() {
      calculateHargaJual();
    });

    function calculateHargaJual() {
      const hpp = parseFloat($('#hpp').val()) || 0;
      const marginPercent = parseFloat($('#margin_percent').val()) || 0;
      const marginRp = parseFloat($('#margin_price').val().replace(/\./g, '')) || 0;
      
      let hargaJualFinal = 0;
      let hargaJualPercent = 0;
      let hargaJualPrice = 0;
      
      if (marginRp > 0) {
        hargaJualPrice = marginRp;
      } 
      if (marginPercent > 0) {
        hargaJualPercent = hpp * ((marginPercent / 100));
      }
      if (hargaJualPercent == 0 && hargaJualPrice == 0) {
        hargaJualFinal = hpp;
      } else {
        hargaJualFinal = hpp + (hargaJualPercent + hargaJualPrice) ;
      }
      
      $('#sales_price').val(hargaJualFinal.toFixed(0));
    }


  });
</script>
@endsection