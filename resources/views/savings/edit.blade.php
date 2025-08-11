@extends('layouts.main')

@section('title')
    <title>Edit Simpanan - Sistem Informasi Koperasi dan Usaha</title>
@endsection
@section('page_css')
    
@endsection
@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12 col-xl-10">
      <div class="row align-items-center my-4">
        <div class="col">
          <h2 class="h3 mb-0 page-title">Edit Simpanan</h2>
        </div>
      </div>
      <form action="{{ route('savings.update', $saving->id)}}" method="POST" enctype="multipart/form-data">
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
          <div class="form-group col-md-4">
            <label for="svDate">Kode</label>
            <h5>{{ $saving->sv_code }}</h5>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="simple-select2">Anggota</label>
            <select id="memberSelect" name="member_id" class="form-control">
              <option value="{{ $saving->member_id }}" selected>
                {{ $saving->member->name ?? 'Search anggota...' }}
              </option>
            </select>
          </div>
          <div class="form-group col-md-3">
            <label for="simple-select2">Jenis Simpanan</label>
            <select id="svType" name="sv_type_id" class="form-control">
              <option value="">-- Pilih jenis simpanan</option>
              @foreach ($sv_types as $type)
                  <option value="{{ $type->id }}" {{ $saving->sv_type_id == $type->id ? 'selected' : ''}}>{{ ucwords($type->name) }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group col-md-3">
            <label for="sv_date">Tanggal Simpanan</label>
            <input type="date" class="form-control" id="sv_date" name="sv_date" value="{{old('sv_date', date('Y-m-d', strtotime($saving->sv_date)) ?? '')}}">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-4">
            <label for="sv_value">Jumlah</label>
            <input type="number" class="form-control" id="sv_amount" name="sv_value" value="{{old('sv_value', $saving->sv_value*1 ?? '')}}">
          </div>
          <div class="form-group col-md-6">
            <label for="profile_photo">Payment Photo</label>
            <div class="custom-file">
              <input type="file" class="custom-file-input" id="proof_of_payment" name="proof_of_payment">
              <label class="custom-file-label" for="proof_of_payment" id="label_photo">Choose file</label>
              <small>*Format file jpg/jpeg,png dengan ukuran max:2MB</small>
            </div>
            <!-- Preview container -->
            <div class="mt-2">
                <img id="preview-image" src="{{ asset('storage/'. $saving->proof_of_payment)}}" alt="Preview" style="max-width: 300px;">
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
    $('#memberSelect').select2({
        placeholder: 'Search anggota...',
        theme: 'bootstrap4',
        minimumInputLength: 2,
        ajax: {
            url: '/api/members/search',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term,
                    active: 1
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

    $('#proof_of_payment').on('change', function(event) {
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