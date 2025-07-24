@extends('layouts.main')

@section('page_css')
    <link rel="stylesheet" href="{{ asset('fedash/css/dataTables.bootstrap4.css') }}">
@endsection

@section('content')
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
                <table class="table datatables" id="loans">
                  <thead>
                    <tr>
                      <th width="5%">No.</th>
                      <th width="10%">Kode</th>
                      <th width="20%">Anggota</th>
                      <th width="15%">Tanggal Pinjam</th>
                      <th width="15%">Jatuh Tempo</th>
                      <th width="15%">Nominal</th>
                      <th width="15%">Status</th>
                      <th width="5%">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    
                    @foreach ($loans as $loan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $loan->loan_code }}</td>
                            <td>{{ ucwords($loan->member->name) }}</td>
                            <td>{{ date('d M Y', strtotime($loan->loan_date)) }}</td>
                            <td>{{ date('d M Y', strtotime($loan->due_date)) }}</td>
                            <td>Rp {{ number_format($loan->loan_value, 2) }}</td>
                            <td>
                              @switch($loan->loan_state)
                                  @case(99)
                                      Ditolak
                                      @break
                                  @case(2)
                                      Disetujui
                                      @break
                                  @default
                                      Pengajuan
                              @endswitch
                            </td>
                            <td><button class="btn btn-sm dropdown-toggle more-horizontal" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="text-muted sr-only">Action</span>
                              </button>
                              <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('loans.show', $loan->id) }}">View</a>
                                <a class="dropdown-item" href="{{ route('loans.edit', $loan->id) }}">Edit</a>
                                <form action="{{ route('loans.destroy', $loan->id) }}" method="POST" style="display: inline;" id="deleteForm">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" id="btnDelete" class="dropdown-item">Delete</button>
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
  $(document).ready(function() {
    $('#loans').DataTable({
      autoWidth: true,
      "lengthMenu": [
        [10, 25, 50, -1],
        [10, 25, 50, "All"]
      ]
    });
    $('#deleteForm').on('submit', function(e) {
      if (!confirm('Apakah anda yakin ingin menghapus pinjaman ini?')) {
          e.preventDefault();
      }
    });
  })
</script>
@endsection