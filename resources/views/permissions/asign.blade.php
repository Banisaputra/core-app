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
          <h2 class="h3 mb-0 page-title">Permission Asign</h2>
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
       
      <form action={{ route('permissions.asigned') }} method="POST" id="form-member" enctype="multipart/form-data">
        @csrf
        <div class="form-row">
            <div class="form-group col-md-5">
                <label for="simple-select2">Role</label>
                <select id="roleSelect" name="role_id" class="form-control"></select>
            </div>
        </div>
        <hr class="my-4">
        <div class="form-row">
          <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="customSwitch1">
            <label class="custom-control-label" for="customSwitch1">Toggle this switch element</label>
          </div>
        </div>
         
         <hr class="my-4">
         <div class="form-row">
           <div class="col-md-6">
            <small>Izin akses yang terdaftar akan tergantikan</small>
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
    $('#roleSelect').select2({
        placeholder: 'Search role...',
        theme: 'bootstrap4',
        minimumInputLength: 2,
        multiple: true,
        ajax: {
            url: '/api/roles/search', // Your route
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
    $('#userSelect').select2({
        placeholder: 'Search user...',
        theme: 'bootstrap4',
        minimumInputLength: 2,
        ajax: {
            url: '/api/users/search', // Your route
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
</script>
@endsection