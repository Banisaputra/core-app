@extends('layouts.main')

@section('page_css')
    <link rel="stylesheet" href="{{ asset('fedash/css/dataTables.bootstrap4.css') }}">
@endsection
@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12 col-xl-10">
      <div class="row align-items-center my-4">
        <div class="col">
          <h2 class="h3 mb-0 page-title">Generate Simpanan</h2>
          <p>generate simpanan bulanan</p>
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
      <h5 class="mb-2 mt-4"> Anggota</h5>
      <p class="mb-4">Kosongkan jika tidak ada Anggota yang ter blacklist, atau isikan nama Anggota sesuai daftar blacklist</p>
       
      <form action={{ route('savings.generated') }} method="POST" id="form-member" enctype="multipart/form-data">
        @csrf
        <div class="form-row">
           <div class="form-group mb-3 col-md-3">
               <label for="periode">Periode</label>
               <input class="form-control" id="periode" type="month" name="periode">
            </div>
            <div class="form-group col-md-3">
              <label for="simple-select2">Jenis Simpanan</label>
              <select id="svType" name="sv_type_id" class="form-control">
                <option value="">-- Pilih jenis simpanan</option>
                @foreach ($sv_types as $type)
                    <option value="{{ $type->id }}">{{ ucwords($type->name) }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group col-md-6">
                <label for="simple-select2">Anggota</label>
                <select id="memberSelect" name="member_id[]" class="form-control"></select>
            </div>
        </div>
         
         <hr class="my-4">
         <div class="form-row">
           <div class="col-md-6">
            <small>Note: Anggota tercatat tidak akan memiliki simpanan pada periode terpilih</small>
           </div>
           <div class="col-md-6 text-right">
             <button type="submit" class="btn btn-primary"><span class="fe fe-16 mr-2 fe-check-circle"></span>Submit</button>
           </div>
         </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('page_script')
<script src="{{ asset('fedash/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('fedash/js/dataTables.bootstrap4.min.js') }}"></script>
<script>
    $('#roleUser').DataTable(
    {
      autoWidth: true,
      "lengthMenu": [
        [10, 25, 50, -1],
        [10, 25, 50, "All"]
      ]
    });
    $('#memberSelect').select2({
        placeholder: 'Search member...',
        theme: 'bootstrap4',
        minimumInputLength: 2,
        multiple: true,
        ajax: {
            url: '/api/members/search',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
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
   
</script>
@endsection