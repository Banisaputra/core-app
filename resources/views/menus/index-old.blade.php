@extends('layouts.main')

@section('title')
    <title>Menu - Sistem Informasi Koperasi dan Usaha</title>
@endsection
@section('page_css')
    <link rel="stylesheet" href="{{ asset('fedash/css/dataTables.bootstrap4.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-12">
        <div class="col">
            <h2 class="h3 mb-0 page-title">Daftar Menu</h2>
        </div>
        <div class="row align-items-center my-4">
            <div class="col">
                <a href="{{ route('menus.create') }}" class="btn mb-2 btn-primary">
                    <span class="fe fe-plus fe-16 mr-1"></span> Tambah Data
                </a>
            </div>
            <div class="col-auto">
               {{-- other button --}}
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
      {{-- handle error import --}}
      @if (session()->has('failed') && count(session('failed')) > 0)
        <div class="alert alert-danger" role="alert">Kesalahan Row <br>
          @foreach (session('failed') as $error)
            <span class="fe fe-minus-circle fe-16 mr-2"></span> {{ "[".$error['row']."] ".$error['errors'][0] }} <br>           
          @endforeach
        </div>
      @endif
        <div class="row my-4">
          <!-- Small table -->
          <div class="col-md-12">
            <div class="card shadow">
              <div class="card-body">
                <!-- table -->
                <table class="table datatables" id="supplier">
                  <thead>
                    <tr>
                      <th width="5%">No.</th>
                      <th width="25%">Nama</th>
                      <th>Permission</th>
                      <th>Icon</th>
                      <th>Parent</th>
                      <th width="5%">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    
                    @foreach ($menus as $menu)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $menu->name }}</td>
                        <td>{{ $menu->permission }}</td>
                        <td>{{ $menu->icon ?? "-" }}</td>
                        <td>{{ $menu->parent_id ? "TIDAK" : "YA" }}</td>
                        <td><button class="btn btn-sm dropdown-toggle more-horizontal" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="text-muted sr-only">Action</span>
                          </button>
                          <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{ route('menus.edit', $menu->id) }}">Edit</a>
                            <form action="{{ route('menus.destroy', $menu->id) }}" method="POST" style="display: inline;" id="deleteForm">
                                @csrf
                                @method('DELETE')
                                <button type="submit" id="btnDelete" class="dropdown-item text-danger">Delete</button>
                            </form>
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
    $('#supplier').DataTable(
    {
      autoWidth: true,
      "lengthMenu": [
        [10, 25, 50, -1],
        [10, 25, 50, "All"]
      ]
    });

    $('#btnDelete').on('click', function(e) {
      if (!confirm('Anda yakin ingin mengubah Aktivasi Supplier ini?')) {
        e.preventDefault();
      } else {
        $('#deleteForm').submit()
      }
    });

    $('#supplierFile').on('change', function() {
      var fileName = $(this).val().split('\\').pop();
      $(this).next('.custom-file-label').html(fileName);
    });

    $('#importModal').on('hidden.bs.modal', function () {
      $(this).find('form')[0].reset();
      $('#importModal .custom-file-label').html('Choose file');
    });
</script>
@endsection