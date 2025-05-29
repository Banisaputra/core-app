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
          <h2 class="h3 mb-0 page-title">Role Asign</h2>
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
      <h5 class="mb-2 mt-4">User Role</h5>
      <p class="mb-4">Akse user dari role</p>
       
      <form action={{ route('roles.asigned') }} method="POST" id="form-member" enctype="multipart/form-data">
        @csrf
        <div class="form-row">
            <div class="form-group col-md-5">
                <label for="simple-select2">User</label>
                <select id="userSelect" name="user_id" class="form-control"></select>
            </div>
            <div class="form-group col-md-7">
                <label for="simple-select2">Roles</label>
                <select id="roleSelect" name="role_id[]" class="form-control"></select>
            </div>
        </div>
         
         <hr class="my-4">
         <div class="form-row">
           <div class="col-md-6">
            <small>Role terdaftar akan terhapus</small>
           </div>
           <div class="col-md-6 text-right">
             <button type="submit" class="btn btn-primary"><span class="fe fe-16 mr-2 fe-check-circle"></span>Submit</button>
           </div>
         </div>
      </form>
      <hr class="my-4">
      <div class="row align-items-center my-4">
        <div class="col">
          <h2 class="h3 mb-0 page-title">User Role (soon)</h2>
        </div>
      </div>
        {{-- <div class="row my-4">
          <!-- Small table -->
          <div class="col-md-12">
            <div class="card shadow">
              <div class="card-body">
                <!-- table -->
                <table class="table datatables" id="roleUser">
                  <thead>
                    <tr>
                      <th width="5%">No.</th>
                      <th width="20%">Nama</th>
                      <th>Roles</th>
                    </tr>
                  </thead>
                  <tbody>
                    
                    @foreach ($roles as $role)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $role->name }}</td>
                        <td>{{ $role->name }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div> --}}
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