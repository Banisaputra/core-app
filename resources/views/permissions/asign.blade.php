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
      
      <form action="#" method="POST" id="form-member" enctype="multipart/form-data">
        @csrf
          <input type="hidden" name="role_id" value="{{ $role->id }}">
          <h4>Manage Permissions untuk Role: {{ ucwords($role->name) }}</h4>
          <div class="custom-control custom-switch mt-3">
            <input type="checkbox" class="custom-control-input actAllToggle" id="actAll" {{ count($allPermissions) == count($rolePermissions) ? 'checked' : '' }}>
            <label class="custom-control-label" for="actAll">Aktifkan semua</label>
          </div>
          <div class="row my-4">
          <div class="col-md-12">
            <div class="card shadow">
              <div class="card-body">
                <table class="table datatables" id="permissions">
                  <thead>
                    <tr>
                      <th width="5%">No.</th>
                      <th width="20%">Nama</th>
                      <th width="50%">Deskripsi</th>
                      <th width="5%">Action</th>
                    </tr>
                  </thead>
                  <tbody> 
                    @foreach($allPermissions as $key => $permission)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $permission->name }}</td>
                        <td>{{ $permission->description }}</td>
                        <td>
                          <div class="custom-control custom-switch">
                            <input type="checkbox" value="{{ $permission->name }}" class="custom-control-input actPermission" name="permissions[]" id="permission-{{$key}}" {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="permission-{{$key}}"></label>
                          </div>
                        </td> 
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <hr class="my-4">
        <div class="form-row">
          <div class="col-md-6">
          <small>Izin akses yang terdaftar akan tergantikan</small>
          </div>
          <div class="col-md-6 text-right">
            {{-- <button type="submit" class="btn btn-primary"><span class="fe fe-16 mr-2 fe-check-circle"></span>Submit</button> --}}
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
  $(document).ready(function () {
    $('#permissions').DataTable(
    {
      autoWidth: false,
      "lengthMenu": [
        [10, 25, 50, -1],
        [10, 25, 50, "All"]
      ]
    });

    $('#actAll').on('click', function() {
      console.log("all check");
      var checkAll = $(this).is(":checked");
      var type = $(this).is(":checked") ? "given" : "revoke";
      saveRowData("all", type, 0);      
    })

    $('#permissions tbody').on('change', '.actPermission', function () {
      var value = $(this).val();
      var type = $(this).is(":checked") ? "given" : "revoke";
      saveRowData(value, type, 1);
    })
  
  });

function saveRowData(permission, type, isRow) {
    $.ajax({
        url: '/permissions/asign',
        type: 'POST',
        data: {
          _token: '{{ csrf_token() }}',
          role_id: '{{ $role->id }}',
          permission: permission,
          type: type,
          is_row: isRow
        },
        beforeSend: function() {
          $('#loadingOverlay, #loadingIndicator').show();
        },
        success: function(response) {
          alert(response.message);
        },
        error: function(xhr, status, error, message) {
          alert(xhr.responseJSON.message);
        },
        complete: function() {
          $('#loadingIndicator').hide();
          window.location.reload()
        }
    });
}
 
</script>
@endsection