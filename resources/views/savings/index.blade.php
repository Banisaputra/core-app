@extends('layouts.main')

@section('title')
    <title>Simpanan - Sistem Informasi Koperasi dan Usaha</title>
@endsection
@section('page_css')
    <link rel="stylesheet" href="{{ asset('fedash/css/dataTables.bootstrap4.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-12">
        <div class="col">
            <h2 class="h3 mb-0 page-title">Daftar Simpanan</h2>
            <p class="card-text">Daftar Simpanan Anggota.</p>
        </div>
        <div class="row align-items-center my-4">
            <div class="col">
                <a href="{{ route('savings.create') }}" class="btn mb-2 btn-primary mr-3">
                    <span class="fe fe-plus fe-16 mr-1"></span> Tambah Data
                </a>
                <a href="{{ route('savings.generate') }}" class="btn mb-2 btn-warning">
                    <span class="fe fe-plus fe-16 mr-1"></span> Generate
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
          <!-- Small table -->
          <div class="col-md-12">
            <div class="card shadow">
              <div class="card-body">
                <!-- table -->
                <table class="table datatables" id="savings">
                  <thead>
                    <tr>
                      <th width="5%">No.</th>
                      <th width="15%">Kode</th>
                      <th width="">NIK</th>
                      <th width="">Anggota</th>
                      <th width="15%">Tanggal</th>
                      <th width="10%">Jenis</th>
                      <th width="15%">Nominal</th>
                      <th width="10%">Status</th>
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
    $('#savings').DataTable(
    {
      processing: true,
      serverSide: true,
      ajax: "{{ route('savings.index') }}",
      columns: [
          { data: 'rownum', name: 'rownum', orderable: false, searchable: false},
          { data: 'sv_code', name: 'sv_code' },
          { data: 'member.nip', name: 'nip' },
          { data: 'member.name', name: 'name' },
          { data: 'sv_date', name: 'sv_date' },
          { data: 'type', name: 'type' },
          { data: 'sv_value', name: 'sv_value' },
          { data: 'sv_state', name: 'sv_state', orderable: false, searchable: false },
          { data: 'action', name: 'action', orderable: false, searchable: false },
      ],
      autoWidth: true,
      "lengthMenu": [
        [10, 25, 50, -1],
        [10, 25, 50, "All"]
      ]
    });
    $('#deleteForm').on('submit', function(e) {
      if (!confirm('Apakah anda yakin ingin menghapus simpanan ini?')) {
          e.preventDefault();
      }
    });
  })
</script>
@endsection