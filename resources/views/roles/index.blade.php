@extends('layouts.main')

@section('title')
    <title>Role Informasi - Sistem Informasi Koperasi dan Usaha</title>
@endsection

@section('page_css')
    <link rel="stylesheet" href="{{ asset('fedash/css/dataTables.bootstrap4.css') }}">
@endsection

@section('content')
{{-- addModal --}}
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="defaultModalLabel" style="display: none;" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="{{ route('roles.store')}}" method="POST" id="formRole">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="ModalLabel">Tambah Role</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div id="formErrors" class="alert alert-danger d-none">
          <ul class="mb-0" id="errorList"></ul>
        </div>
        <div class="modal-body">
          <div class="form-group mb-3">
            <label for="name">Nama Role</label>
            <input type="text" id="name" name="name" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn mb-2 btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" id="submitBtn" class="btn mb-2 btn-primary">Save</button>
        </div>
      </div>
    </form>
  </div>
</div>
{{-- end addModal --}}
<div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-12">
        <div class="col">
            <h2 class="h3 mb-0 page-title">Daftar Role</h2>
            <p class="card-text">Role membagi akses untuk setiap anggota</p>
        </div>
        <div class="row align-items-center my-4">
            <div class="col">
              @can('role_create')
                <button type="button" class="btn mb-2 mr-2 btn-primary" data-toggle="modal" data-target="#addModal">
                <span class="fe fe-plus fe-16 mr-1"></span> Tambah Data</button>
              @endcan
              @can('role_management_access')
                <a href="{{ route('roles.asign') }}" class="btn mb-2 btn-success">
                  <span class="fe fe-16 fe-corner-up-right"></span>Asign Role
                </a>
              @endcan
            </div>
            <div class="col-auto">
              {{--more button  --}}
            </div>
        </div>
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
        <div class="row my-4">
          <!-- Small table -->
          <div class="col-md-12">
            <div class="card shadow">
              <div class="card-body">
                <!-- table -->
                <table class="table datatables" id="roles">
                  <thead>
                    <tr>
                      <th width="5%">No.</th>
                      <th width="20%">Nama</th>
                      <th width="5%">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    
                    @foreach ($roles as $role)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $role->name }}</td>
                        <td><button class="btn btn-sm dropdown-toggle more-horizontal" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="text-muted sr-only">Action</span>
                          </button>
                          <div class="dropdown-menu dropdown-menu-right">
                            @can('role_edit')
                            <button class="dropdown-item btnEdit" data-id="{{ $role->id }}">Edit</button>
                            @endcan
                            @can('role_delete')
                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display: inline;" id="deleteForm">
                                @csrf
                                @method('DELETE')
                                <button type="submit" id="btnDelete" class="dropdown-item">Delete</button>
                            </form>
                            @endcan
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
      </div>
    </div>
  </div>
@endsection

@section('page_script')
<script src="{{ asset('fedash/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('fedash/js/dataTables.bootstrap4.min.js') }}"></script>
<script>
  $(document).ready(function () {
    $('#roles').DataTable(
    {
      autoWidth: true,
      "lengthMenu": [
        [10, 25, 50, -1],
        [10, 25, 50, "All"]
      ]
    });

    $('#deleteForm').on('submit', function(e) {
      if (!confirm('Apakah anda yakin ingin menghapus role ini?')) {
          e.preventDefault();
      }
    });

    $('input, select, textarea').on('input change', function () {
      $('#formErrors').addClass('d-none');
      $('#errorList').empty();
    });

    $('#addModal').on('hidden.bs.modal', function () {
      $('#formRole')[0].reset();
      $('#formRole input[name="_method"]').remove();
      $('#formRole').attr('action', '/roles');
      $('#formErrors').addClass('d-none').find('#errorList').empty();
      $('#formRole #submitBtn').text('Save');
      $('#formRole #ModalLabel').text('Tambah Role');
    });

    // submit
    $('#formRole').on('submit', function(e) {
      e.preventDefault();
      $('#submitBtn').prop('disabled', true).text('Saving...');

      var formData = new FormData(this);

      $.ajax({
        url: $(this).attr('action'),
        method: $(this).attr('method'),
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
          alert(response.message);
          $('#addModal').modal('hide');
          setTimeout(function() {
            location.reload();
          }, 500);
        },
        error: function(xhr) {
          console.error(xhr.responseText);

          // Optionally show validation errors
          if (xhr.status === 422) {
            let errors = xhr.responseJSON.errors;
            $('#errorList').empty();

            $.each(errors, function(key, messages) {
              messages.forEach(function(message) {
                $('#errorList').append('<li>' + message + '</li>');
              });
            });

            // Show the alert
            $('#formErrors').removeClass('d-none');
          } else {
            alert('Something went wrong. Please try again.');
          }
        },
        complete: function() {
          // Re-enable button
          $('#submitBtn').prop('disabled', false).text('Submit');
        }

      })
    });

    // edit
    $('.btnEdit').on('click', function () {
      var id = $(this).data('id');

      $('#formErrors').addClass('d-none').find('#errorList').empty();
      $('#formRole')[0].reset();
      $('#formRole').attr('action', '/roles/' + id);
      $('#formRole #submitBtn').text('Update');
      $('#formRole #ModalLabel').text('Edit Role');

      // check _method
      if (!$('#formRole input[name="_method"]').length) {
        $('#formRole').append('<input type="hidden" name="_method" value="PUT">');
      } else {
        $('#formRole input[name="_method"]').val('PUT');
      }

      $.ajax({
        url: '/roles/' + id + '/edit',
        method: 'GET',
        success: function(response) {
          console.log(response);
          
          $('#formRole #name').val(response.data.name);
          $('#addModal').modal('show');
        },
        error: function(xhr) {
          alert('Failed to load data.');
          console.error(xhr.responseText);
        }
      });
    });

  });
</script>
@endsection