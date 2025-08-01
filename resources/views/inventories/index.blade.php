@extends('layouts.main')

@section('title')
    <title>Inventory - Sistem Informasi Koperasi dan Usaha</title>
@endsection

@section('page_css')
    <link rel="stylesheet" href="{{ asset('fedash/css/dataTables.bootstrap4.css') }}">
@endsection

@section('content')
 
<div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-12">
        <div class="col">
            <h2 class="h3 mb-0 page-title">Inventory</h2>
            <p class="card-text">Dokumen koreksi persedian barang</p>
        </div>
        <div class="row align-items-center my-4">
            <div class="col">
              <a href="{{ route('inv.create') }}" class="btn mb-2 btn-primary">
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
        <div class="row my-4">
          <div class="col-md-12">
            <div class="card shadow">
              <div class="card-body">
                <table class="table datatables" id="inventory">
                  <thead>
                    <tr>
                      <th width="5%">No.</th>
                      <th width="15%">Kode</th>
                      <th width="15%">Tanggal</th>
                      <th width="15%">Jenis</th>
                      <th>Keterangan</th>
                      <th width="15%">Jumlah</th>
                      <th width="15%">Status</th>
                      <th width="5%">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    
                    @foreach ($inventories as $inv)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $inv->code }}</td>
                            <td>{{ date('d M Y', strtotime($inv->inv_date)) }}</td>
                            <td>{{ $inv->type }}</td>
                            <td>{!! $inv->remark !!}</td>
                            <td>{{ $inv->amount }}</td>
                            <td>@switch($inv->inv_state)
                                  @case(99)
                                    <span class="text-danger">Dibatalkan</span>
                                    @break
                                  @case(2)
                                    <span class="text-success">Dikonfirmasi</span>
                                    @break
                                  @default
                                    <span class="text-info">Pengajuan</span>
                                @endswitch</td> 
                            <td><button class="btn btn-sm dropdown-toggle more-horizontal" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="text-muted sr-only">Action</span>
                              </button>
                              <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('inv.show', $inv->id) }}">View</a>
                                <a class="dropdown-item" href="{{ route('inv.edit', $inv->id) }}">Edit</a>
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
  $(document).ready(function() {
    $('#inventory').DataTable(
    {
      autoWidth: true,
      "lengthMenu": [
        [10, 25, 50, -1],
        [10, 25, 50, "All"]
      ]
    });
    $('#inventory tbody #btnDelete').on('click', function (e) {
      if (!confirm('Apakah anda yakin untuk perubahan Aktifasi ini?')) {
        e.preventDefault();
      } else {
        $('#deleteForm').submit();
      }

    });

    $('#memberFile').on('change', function() {
      var fileName = $(this).val().split('\\').pop();
      $(this).next('.custom-file-label').html(fileName);
    });

    $('#importModal').on('hidden.bs.modal', function () {
      $(this).find('form')[0].reset();
      $('#importModal .custom-file-label').html('Choose file');
    });
    
  })
</script>
@endsection