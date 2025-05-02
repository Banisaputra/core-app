@extends('layouts.main')

@section('page_css')
    <link rel="stylesheet" href="{{ asset('fedash/css/dataTables.bootstrap4.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-12">
        <h2 class="mb-2 page-title">Data Karyawan</h2>
        <p class="card-text">Daftar karyawan dapat berisikan pengurus, pegawai, pengelola, dsb.</p>
        <a href="{{ route('employees.create') }}" class="btn mb-2 btn-primary">
           <span class="fe fe-plus fe-16 mr-1"></span> Tambah Data
        </a>
        <div class="row my-4">
          <!-- Small table -->
          <div class="col-md-12">
            <div class="card shadow">
              <div class="card-body">
                <!-- table -->
                <table class="table datatables" id="employees">
                  <thead>
                    <tr>
                      <th>No.</th>
                      <th>Kode</th>
                      <th>Name</th>
                      <th>Jabatan</th>
                      <th>Address</th>
                      <th>Telp.</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     
                  </tbody>
                </table>
              </div>
            </div>
          </div> <!-- simple table -->
        </div> <!-- end section -->
      </div> <!-- .col-12 -->
    </div> <!-- .row -->
  </div> <!-- .container-fluid -->
@endsection

@section('page_script')
<script src="{{ asset('fedash/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('fedash/js/dataTables.bootstrap4.min.js') }}"></script>
<script>
    $('#employees').DataTable(
    {
      autoWidth: true,
      "lengthMenu": [
        [10, 25, 50, -1],
        [10, 25, 50, "All"]
      ]
    });
</script>
@endsection