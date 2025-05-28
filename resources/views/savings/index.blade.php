@extends('layouts.main')

@section('page_css')
    <link rel="stylesheet" href="{{ asset('fedash/css/dataTables.bootstrap4.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-12">
        <div class="col">
            <h2 class="h3 mb-0 page-title">Tambah Karyawan</h2>
            <p class="card-text">Daftar Simpanan Anggota.</p>
        </div>
        <div class="row align-items-center my-4">
            <div class="col">
                <a href="{{ route('savings.create') }}" class="btn mb-2 btn-primary">
                    <span class="fe fe-plus fe-16 mr-1"></span> Tambah Data
                </a>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-success"><span class="fe fe-16 mr-2 fe-download"></span>Import Data <small>(soon)</small></button>
            </div>
        </div>
        <div class="row my-4">
          <!-- Small table -->
          <div class="col-md-12">
            <div class="card shadow">
              <div class="card-body">
                <!-- table -->
                <table class="table datatables" id="savings">
                  <thead>
                    <tr>
                      <th>No.</th>
                      <th>Kode</th>
                      <th>Anggota</th>
                      <th>Tanggal</th>
                      <th>Jenis</th>
                      <th>Nominal</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    
                    @foreach ($savings as $saving)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $saving->saving_code }}</td>
                            <td>{{ $saving->member_id }}</td>
                            <td>{{ date('d M Y', strtotime($saving->saving_date)) }}</td>
                            <td>{{ $saving->saving_type_id }}</td>
                            <td>{{ number_format($saving->saving_value, 2) }}</td>
                            <td>
                                <a href="{{ route('savings.edit', $saving->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                <form action="{{ route('savings.destroy', $saving->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
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
    $('#savings').DataTable(
    {
      autoWidth: true,
      "lengthMenu": [
        [10, 25, 50, -1],
        [10, 25, 50, "All"]
      ]
    });
</script>
@endsection