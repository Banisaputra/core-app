@extends('layouts.main')

@section('title')
    <title>Edit Kategori - Sistem Informasi Koperasi dan Usaha</title>
@endsection
@section('page_css')
    
@endsection
@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12 col-xl-10">
      <div class="row align-items-center my-4">
        <div class="col">
          <h2 class="h3 mb-0 page-title">Ubah Kategori</h2>
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
       
      <form action={{ route('category.update', $category->id) }} method="POST" id="form-category" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-row">
          <div class="form-group col-md-9">
            <label for="code">Kode</label>
            <input type="text" class="form-control" id="code" name="code" value="{{ old('code', $category->code ?? '')}}">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-9">
            <label for="name">Nama</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $category->name ?? '')}}">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-3">
            <label for="margin_percent">Margin (%)</label>
            <input type="text" class="form-control" id="margin_percent" name="margin_percent" value="{{ old('margin_percent', $category->margin_percent ?? 0)}}">
          </div>
          <div class="form-group col-md-3">
            <label for="margin_price">Margin (Rp)</label>
            <input type="hidden" class="form-control" id="margin_price" name="margin_price" data-value="" value="{{ old('margin_price', $category->margin_price ?? 0)}}">
            <input type="text" class="form-control" id="margin_price_input" value="{{ old('margin_price', $category->margin_price ?? 0)}}">
          </div>
          <div class="form-group col-md-3">
            <label for="ppn_percent">PPN (%)</label>
            <input type="text" class="form-control" id="ppn_percent" name="ppn_percent" value="{{ old('ppn_percent', $category->ppn_percent ?? 0)}}">
          </div>
        </div>
        <div class="form-row"> 
          <div class="form-group col-md-9">
            <div class="custom-control custom-switch mb-2">
               <input type="checkbox" class="custom-control-input" id="is_turunan" name="is_turunan" {{ $category->is_parent != 1 ? "checked" : ""}}>
               <label class="custom-control-label" for="is_turunan">Kategori Turunan</label>
            </div>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-9">
            <label for="ct_parent">Kategori Utama</label>
            <select class="custom-select" name="ct_parent" id="ct_parent" {{ $category->is_parent == 1 ? "disabled" : ""}}>
              <option value="">-- Pilih kategori utama</option>
              @foreach ($ct_parent as $ctp)
              <option value="{{$ctp->id}}" {{$category->parent_id==$ctp->id ? "selected" : ""}}>{{ $ctp->name}}</option>
              @endforeach
            </select>
          </div>
        </div>
         <hr class="my-4">
         <div class="form-row">
           <div class="col-md-6 text-left">
             <button type="submit" class="btn btn-primary"><span class="fe fe-16 mr-2 fe-check-circle"></span>Update</button>
           </div>
         </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('page_script')
<script>
  $(document).ready( function () {\
     $('#is_turunan').on('click', function () {
        if ($('#is_turunan').is(':checked')) {
          $('#ct_parent').prop('disabled', false)
        } else {
          $('#ct_parent').val('').prop('disabled', true)
         }
      })
      
    $('#margin_price_input').on('input', function() {
        // Save cursor position
        const cursorPosition = this.selectionStart;
        const originalLength = this.value.length;
        
        // Get raw value without formatting
        let value = $(this).val().replace(/[^\d]/g, '');
        
        // Format to IDR
        if (value.length > 0) {
            let formattedValue = '';
            for (let i = 0; i < value.length; i++) {
                if (i > 0 && (value.length - i) % 3 === 0) {
                    formattedValue += '.';
                }
                formattedValue += value[i];
            }
            
            $(this).val(formattedValue);
            
            // Adjust cursor position based on added dots
            const newLength = formattedValue.length;
            const lengthDiff = newLength - originalLength;
            const newCursorPosition = cursorPosition + lengthDiff;
            this.setSelectionRange(newCursorPosition, newCursorPosition);
        } else {
            $(this).val('');
        }
      });

      // For form submission
      $('#margin_price_input').on('blur', function() {
        const numericValue = $(this).val().replace(/\./g, '');
        $('#margin_price').val(numericValue)
      });
  })

</script>
@endsection