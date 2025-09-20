@extends('layouts.main')

@section('title')
    <title>Edit Penarikan - Sistem Informasi Koperasi dan Usaha</title>
@endsection

@section('page_css')
    
@endsection
@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12 col-xl-10">
      <div class="row align-items-center my-4">
        <div class="col">
          <h2 class="h3 mb-0 page-title">Edit Penarikan</h2>
        </div>
      </div>
      <form action="{{ route('withdrawals.update', $withdrawal->id)}}" method="POST" enctype="multipart/form-data">
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
            <h5>{{ $withdrawal->wd_code }}</h5>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="simple-select2">Anggota</label>
            <select id="memberSelect" name="member_id" class="form-control">
              <option value="{{ $withdrawal->member_id}}" selected>{{ $withdrawal->member->name}}</option>
            </select>
          </div>
          <div class="form-group col-md-3">
            <label for="wd_date">Tanggal Penarikan</label>
            <input type="date" class="form-control" id="wd_date" name="wd_date" value="{{old('wd_date', date('Y-m-d', strtotime($withdrawal->wd_date)) ?? '')}}">
          </div>
          <div class="form-group col-md-3">
            <label for="wd_value">Jumlah</label>
            <input type="number" class="form-control" id="wd_value" name="wd_value" value="{{old('wd_value', $withdrawal->wd_value*1 ?? '')}}">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="remark">Catatan</label>
            <textarea class="form-control" id="remark" rows="3" name="remark">{{old('remark', $withdrawal->remark ?? '')}}</textarea>
          </div>
          <div class="form-group col-md-6">
            <label for="proof_of_withdrawal">Payment Photo</label>
            <div class="custom-file">
              <input type="file" class="custom-file-input" id="proof_of_withdrawal" name="proof_of_withdrawal">
              <label class="custom-file-label" for="proof_of_withdrawal" id="label_photo">Choose file</label>
              <small>*Format file jpg/jpeg,png dengan ukuran max:2MB</small>
            </div>
            <!-- Preview container -->
            <div class="mt-2">
                <img id="preview-image" src="{{ asset('storage/'.$withdrawal->proof_of_withdrawal) }}" alt="Preview" style="max-width: 300px;">
            </div>
          </div>
        </div>
        <hr class="my-4">
        <div class="form-row">
          <div class="col-md-6">
            <small>*Kode dibuat otomatis oleh sistem</small>
          </div>
          <div class="col-md-6 text-right">
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
            active: 0
          };
        },
        processResults: function (data) {
          return {
            results: data.map(function (item) {
              return {
                id: item.id,
                text: "["+ item.nip +"] " + item.name
              };
            })
          };
        },
        cache: true
      }
    });

    $('#proof_of_withdrawal').on('change', function(event) {
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