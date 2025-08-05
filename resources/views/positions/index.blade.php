@extends('layouts.main')

@section('title')
    <title>Jabatan - Sistem Informasi Koperasi dan Usaha</title>
@endsection
@section('page_css')
    <link rel="stylesheet" href="{{ asset('fedash/css/dataTables.bootstrap4.css') }}">
@endsection

@section('content')
{{-- modal import --}}
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalTitle" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form action="{{ route('position.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="importModalTitle">Upload file</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">x</span>
          </button>
        </div>
        <div class="modal-body"> 
          <div class="form-group mb-3">
            <label for="positionFile"> Format file harus sesuai template</label>
            <div class="custom-file">
              <input type="file" class="custom-file-input" id="positionFile" name="file" accept=".xlsx,.xls" required>
              <label class="custom-file-label" for="positionFile">Choose file</label>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn mb-2 btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn mb-2 btn-primary">Upload</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-12">
        <div class="col">
            <h2 class="h3 mb-0 page-title">Daftar Jabatan</h2>
        </div>
        <div class="row align-items-center my-4">
            <div class="col">
                <a href="{{ route('position.create') }}" class="btn mb-2 btn-primary">
                    <span class="fe fe-plus fe-16 mr-1"></span> Tambah Data
                </a>
            </div>
            <div class="col-auto">
              <div class="dropdown">
                <button class="btn btn-sm btn-success more-dropdown" type="button" id="dropdownMenuButton" data-toggle="dropdown">
                  <span class="fe fe-24 fe-download"></span>Import file
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton" style="">
                  <a class="dropdown-item" href="{{ route('position.template')}}"><small>Download Template</small></a>
                  <button class="dropdown-item" data-toggle="modal" data-target="#importModal"><small>Upload data</small></button>
                </div>
              </div>
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
          <div class="col-md-12">
            <div class="card shadow">
              <div class="card-body">
                <table class="table datatables" id="position">
                  <thead>
                    <tr>
                      <th width="5%">No.</th>
                      <th width="15%">Kode</th>
                      <th>Nama</th>
                      <th>Keterangan</th>
                      <th width="10%">Status</th>
                      <th width="5%">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    
                    @foreach ($positions as $pst)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $pst->code }}</td>
                        <td>{{ $pst->name }}</td>
                        <td>{{ $pst->description }}</td>
                        <td>{!! $pst->is_transactional == 1 ? "<span class='dot dot-lg bg-success mr-1'></span>Aktif" : "<span class='dot dot-lg bg-secondary mr-1'></span>Tidak Aktif" !!}</td>
                        <td><button class="btn btn-sm dropdown-toggle more-horizontal" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="text-muted sr-only">Action</span>
                          </button>
                          <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{ route('position.edit', $pst->id) }}">Edit</a>
                            <form action="{{ route('position.destroy', $pst->id) }}" method="POST" style="display: inline;" id="deleteForm">
                                @csrf
                                @method('DELETE')
                                <button type="submit" id="btnDelete" class="dropdown-item">{{ $pst->is_transactional==1 ? "Nonaktifkan" : "Aktifkan"}}</button>
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
    $('#position').DataTable(
    {
      autoWidth: true,
      "lengthMenu": [
        [10, 25, 50, -1],
        [10, 25, 50, "All"]
      ]
    });

    $('#btnDelete').on('click', function(e) {
      if (!confirm('Anda yakin ingin mengubah Aktivasi Jabatan ini?')) {
        e.preventDefault();
      } else {
        $('#deleteForm').submit()
      }
    });

    $('#positionFile').on('change', function() {
      var fileName = $(this).val().split('\\').pop();
      $(this).next('.custom-file-label').html(fileName);
    });

    $('#importModal').on('hidden.bs.modal', function () {
      $(this).find('form')[0].reset();
      $('#importModal .custom-file-label').html('Choose file');
    });
</script>
@endsection