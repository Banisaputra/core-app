@extends('layouts.main')

@section('title')
    <title>User Informasi - Sistem Informasi Koperasi dan Usaha</title>
@endsection

@section('page_css')
    <link rel="stylesheet" href="{{ asset('fedash/css/dataTables.bootstrap4.css') }}">
@endsection

@section('content')
{{-- addModal --}}
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="defaultModalLabel" style="display: none;" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="{{ route('users.store')}}" method="POST" id="formUser">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="ModalLabel">Tambah User</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">x</span>
          </button>
        </div>
        <div id="formErrors" class="alert alert-danger d-none">
          <ul class="mb-0" id="errorList"></ul>
        </div>
        <div class="modal-body">
          <div class="form-group mb-3">
            <label for="name">Nama</label>
            <input type="text" id="name" name="name" class="form-control" required>
          </div>
          <div class="form-group mb-3">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" required>
          </div>
          <div class="row mb-1">
            <div class="col">
              <label for="password" class="form-label">Password</label>
              <div class="input-group">
                <input type="text" class="form-control" id="password" name="password" placeholder="Paswword">
                <button class="btn btn-info" type="button" id="generatePass" onclick="generatePassword()">Generate</button>
              </div>
              <small id="noted">Salin password dahulu sebelum disimpan.</small>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn mb-2 btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" id="submitBtn" class="btn mb-2 btn-primary">Submit</button>
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
            <h2 class="h3 mb-0 page-title">Daftar User</h2>
            <p class="card-text">User account untuk akses sistem.</p>
        </div>
        <div class="row align-items-center my-4">
            <div class="col">
              @can('user_create')
                <button type="button" class="btn mb-2 mr-2 btn-primary" data-toggle="modal" data-target="#addModal">
                <span class="fe fe-plus fe-16 mr-1"></span> Tambah Data</button>
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
          <div class="col-md-12">
            <div class="card shadow">
              <div class="card-body">
                <table class="table datatables" id="users">
                  <thead>
                    <tr>
                      <th width="5%">No.</th>
                      <th width="35%">Nama</th>
                      <th width="35%">Email</th>
                      <th width="20%">Verified</th>
                      <th width="5%">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($users as $user)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->email_verified_at }}</td>
                        <td><button class="btn btn-sm dropdown-toggle more-horizontal" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="text-muted sr-only">Action</span>
                          </button>
                          <div class="dropdown-menu dropdown-menu-right">
                            @can('user_edit')
                            <button class="dropdown-item btnEdit" data-id="{{ $user->id }}">Edit</button>
                            @endcan
                            @can('user_delete')
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: inline;" id="deleteForm-{{ $user->id }}">
                              @csrf
                              @method('DELETE')
                              <button type="button" id="btnDelete-{{ $user->id }}" class="dropdown-item btnDelete">Delete</button>
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
    $('#users').DataTable({
      autoWidth: false,
      "lengthMenu": [
        [10, 25, 50, -1],
        [10, 25, 50, "All"]
      ]
    });

    $('#users tbody').on('click', '.btnDelete', function(e) {
      var id = $(this).prop('id').split('-')[1];
      console.log(id);
      if (confirm('Apakah anda yakin ingin menghapus user ini?')) {
        $('#deleteForm-'+id).submit();
      }
    });
    
    $('input, select, textarea').on('input change', function () {
      $('#formErrors').addClass('d-none');
      $('#errorList').empty();
    });
 
    $('#addModal').on('hidden.bs.modal', function () {
      $('#formUser')[0].reset();
      $('#formUser input[name="_method"]').remove();
      $('#formUser').attr('action', '/user');
      $('#formErrors').addClass('d-none').find('#errorList').empty();
      $('#formUser #submitBtn').text('Save');
      $('#formUser #ModalLabel').text('Tambah Role');
    });

    // submit
    $('#formUser').on('submit', function(e) {
      e.preventDefault();
      $('#submitBtn').prop('disabled', true).text('Saving...');

      var formData = new FormData(this);

      $.ajax({
        url: $(this).attr('action'),
        method: $(this).attr('method'),
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function() {
          $('#loadingOverlay, #loadingIndicator').show();
        },
        success: function(response) {
          alert(response.success);
          $('#addModal').modal('hide');
        },
        error: function(xhr) {
          $('#loadingOverlay, #loadingIndicator').hide();

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
        complete: function(response) {
          $('#submitBtn').prop('disabled', false).text('Submit');
          
          if (response.responseJSON.success) {
            $('#loadingIndicator').hide();
            window.location.reload()
          }
        }
      })
    });

    // edit
    $('#users tbody').on('click', '.btnEdit', function () {
      var id = $(this).data('id');

      $('#formErrors').addClass('d-none').find('#errorList').empty();
      $('#formUser')[0].reset();
      $('#formUser').attr('action', '/users/' + id);
      $('#formUser #submitBtn').text('Update');
      $('#formUser #ModalLabel').text('Edit Role');

      // check _method
      if (!$('#formUser input[name="_method"]').length) {
        $('#formUser').append('<input type="hidden" name="_method" value="PUT">');
      } else {
        $('#formUser input[name="_method"]').val('PUT');
      }

      $.ajax({
        url: '/users/' + id + '/edit',
        method: 'GET',
        success: function(response) {
          
          $('#formUser #name').val(response.data.name);
          $('#formUser #email').val(response.data.email);
          $('#formUser #password').prop('required', false);
          $('#addModal').modal('show');
        },
        error: function(xhr) {
          alert('Failed to load data.');
          console.error(xhr.responseText);
        }
      });
    });
  
  });

  function generatePassword() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let password = '';
    for (let i = 0; i < 8; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    $('#password').val(password); 
      
  }
</script>
@endsection