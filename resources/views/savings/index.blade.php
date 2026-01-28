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
              <option value="2">Dikonfirmasi</option>
              <option value="99">Dibatalkan</option>
            </select>
          </div>

          <div class="col-md-3">
            <label>Jenis Simpanan</label>
            <select id="type" class="form-control">
              <option value="">Semua</option>
              @foreach ($sv_types as $sv_type)
                <option value="{{ $sv_type->id }}">{{ $sv_type->name }}</option>
              @endforeach
            </select>
          </div>
      </div> 
      {{-- <button id="btnFilter" class="btn btn-primary mb-2">
          Terapkan Filter
      </button>
      <button id="btnReset" class="btn btn-secondary mb-2">
          Reset Filter
      </button> --}}
  
        {{-- ajax data --}}
        <div class="row my-4">
          <!-- Small table -->
          <div class="col-md-12">
            <div class="card shadow">
              <div class="card-body">
                <!-- table -->
                <form id="bulkForm" method="POST" action="{{ route('savings.bulk') }}">
                  @csrf
                  <input type="hidden" name="ids" id="selectedIds">
                  <table class="table datatables" id="savings">
                    <thead>
                      <tr>
                        <th width="30">
                          <div class="custom-control custom-checkbox">
                            <input type="checkbox" id="checkAll" class="custom-control-input">
                            <label class="custom-control-label" for="checkAll"></label>
                          </div>
                        </th>
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
                  {{-- <button type="submit" class="btn btn-danger mt-2" id="bulkBtn" disabled>
                    Konfirmasi Data Terpilih
                  </button> --}}
                </form>
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
    let selectedIds = [];
    let table = $('#savings').DataTable(
    {
      processing: true,
      serverSide: true,
      ajax: {
        url: "{{ route('savings.index') }}",
        type: 'GET',
        data: function (d) {
            d.date_start = $('#date_start').val();
            d.date_end   = $('#date_end').val();
            d.status     = $('#status').val();
            d.type       = $('#type').val();
        }
      },
      columns: [
         {
            data: 'id',
            orderable: false,
            searchable: false,
            render: function (data) {
              return `
                      <div class="custom-control custom-checkbox">
                        <input type="checkbox" id="check-${data}" class="custom-control-input row-check" value="${data}">
                        <label class="custom-control-label" for="check-${data}"></label>
                      </div>
              `;
            }
        },
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
      rowCallback: function (row, data) {
        if (selectedIds.includes(data.id.toString())) {
          $(row).find('.row-check').prop('checked', true);
        }
      },
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
    
    // checkbox per baris
    $('#savings').on('change', '.row-check', function () {
        const id = $(this).val();
  
        if (this.checked) {
            if (!selectedIds.includes(id)) {
              selectedIds.push(id);
            }
        } else {
          selectedIds = selectedIds.filter(item => item !== id);
        }
  
        updateBulkState();
    });
  
    // select all (halaman aktif saja)
    $('#checkAll').on('change', function () {
        const checked = this.checked;
  
        $('#savings .row-check').each(function () {
            $(this).prop('checked', checked).trigger('change');
        });
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
  
    // update hidden input & button
    function updateBulkState() {
        $('#selectedIds').val(JSON.stringify(selectedIds));
        $('#bulkBtn').prop('disabled', selectedIds.length === 0);
    }

    
  })
</script>
@endsection