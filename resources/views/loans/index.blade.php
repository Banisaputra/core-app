@extends('layouts.main')

@section('title')
    <title>Pinjaman - Sistem Informasi Koperasi dan Usaha</title>
@endsection

@section('page_css')
    <link rel="stylesheet" href="{{ asset('fedash/css/dataTables.bootstrap4.css') }}">
@endsection

@section('content')
{{-- modal import --}}
<div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalTitle" style="display: none;" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form action="{{ route('loans.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="importModalTitle">Upload file</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body"> 
          <div class="form-group mb-3">
            <label for="loanFile"> Format file harus sesuai template</label>
            <div class="custom-file">
              <input type="file" class="custom-file-input" id="loanFile" name="file" accept=".xlsx,.xls" required>
              <label class="custom-file-label" for="loanFile">Choose file</label>
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
            <h2 class="h3 mb-0 page-title">Daftar Pinjaman</h2>
            <p class="card-text">Daftar Pinjaman Anggota.</p>
        </div>
        <div class="row align-items-center my-4">
            <div class="col">
                <a href="{{ route('loans.create') }}" class="btn mb-2 btn-primary">
                    <span class="fe fe-plus fe-16 mr-1"></span> Tambah Data
                </a>
            </div>
            <div class="col-auto">
                {{-- other button --}}
                {{-- <div class="dropdown">
                <button class="btn btn-sm btn-success more-dropdown" type="button" id="dropdownMenuButton" data-toggle="dropdown">
                  <span class="fe fe-24 fe-download"></span>Import file
                </button>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton" style=""> 
                  <button class="dropdown-item" data-toggle="modal" data-target="#importModal"><small>Upload data</small></button>
                </div>
              </div> --}}
              {{-- end other button --}}
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

        {{-- filter awal --}}
        <div class="row mb-3">
          <div class="col-md-3">
            <label>Tanggal Awal</label>
            <input type="date" id="date_start" class="form-control">
          </div>

          <div class="col-md-3">
            <label>Tanggal Akhir</label>
            <input type="date" id="date_end" class="form-control">
          </div>

          <div class="col-md-3">
            <label>Status</label>
            <select id="status" class="form-control">
              <option value="">Semua</option>
              <option value="1">Pengajuan</option>
              <option value="2">Disetujui</option>
              <option value="3">Selesai</option>
              <option value="99">Ditolak</option>
            </select>
          </div>

          <div class="col-md-3">
            <label>Jenis Pinjaman</label>
            <select id="type" class="form-control">
              <option value="">Semua</option>
              <option value="UANG">Uang</option>
              <option value="BARANG">Barang</option>
            </select>
          </div>
      </div>

      {{-- <button id="btnFilter" class="btn btn-primary mb-2">
          Terapkan Filter
      </button>
      <button id="btnReset" class="btn btn-secondary mb-2">
          Reset Filter
      </button> --}}


        <div class="row my-4">
          <div class="col-md-12">
            <div class="card shadow">
              <div class="card-body">
                <table class="table datatables" id="loans">
                  <thead>
                    <tr>
                      <th width="5%">No.</th>
                      <th width="10%">Kode</th>
                      <th width="20%">NIK</th>
                      <th width="20%">Anggota</th>
                      <th width="15%">Tanggal Pinjam</th>
                      <th width="15%">Jatuh Tempo</th>
                      <th width="10%">Jenis</th>
                      <th width="15%">Nominal</th>
                      <th width="15%">Status</th>
                      <th width="5%">Action</th>
                    </tr>
                  </thead> 
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
    let table = $('#loans').DataTable(
    {
      processing: true,
      serverSide: true,
      ajax: {
        url: "{{ route('loans.index') }}",
        type: 'GET',
        data: function (d) {
            d.date_start = $('#date_start').val();
            d.date_end   = $('#date_end').val();
            d.status     = $('#status').val();
            d.type       = $('#type').val();
        }
      },
      columns: [
        { data: 'rownum', name: 'rownum', orderable: false, searchable: false},
        { data: 'loan_code', name: 'loan_code' },
        { data: 'member.nip', name: 'nip' },
        { data: 'member.name', name: 'name' },
        { data: 'loan_date', name: 'loan_date' },
        { data: 'due_date', name: 'due_date' },
        { data: 'type', name: 'type' },
        { data: 'loan_value', name: 'loan_value' },
        { data: 'state', name: 'state', orderable: false, searchable: false },
        { data: 'action', name: 'action', orderable: false, searchable: false },
      ],
      autoWidth: true,
      "lengthMenu": [
        [10, 25, 50, -1],
        [10, 25, 50, "All"]
      ]
    });

    // filter button
    $('#btnFilter').on('click', function () {
      table.ajax.reload();
      $(this).blur();

    });
    // tombol reset
    $('#btnReset').on('click', function () {
        $('#date_start, #date_end, #status, #type').val('');
        table.ajax.reload();
    });
    
    $('#deleteForm').on('submit', function(e) {
      if (!confirm('Apakah anda yakin ingin menghapus pinjaman ini?')) {
          e.preventDefault();
      }
    });

    // import file
    $('#loanFile').on('change', function() {
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