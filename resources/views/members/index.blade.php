@extends('layouts.main')

@section('page_css')
    <link rel="stylesheet" href="{{ asset('fedash/css/dataTables.bootstrap4.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-12">
        <div class="col">
            <h2 class="h3 mb-0 page-title">Daftar Anggota</h2>
            <p class="card-text">Anggota koperasi yang dapat melakukan transaksi.</p>
        </div>
        <div class="row align-items-center my-4">
            <div class="col">
                <a href="{{ route('members.create') }}" class="btn mb-2 btn-primary">
                    <span class="fe fe-plus fe-16 mr-1"></span> Tambah Data
                </a>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-success"><span class="fe fe-16 mr-2 fe-download"></span>Import Data <small>(soon)</small></button>
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
                <table class="table datatables" id="members">
                  <thead>
                    <tr>
                      <th>No.</th>
                      <th>NIP</th>
                      <th>Nama</th>
                      <th>No.Telp</th>
                      <th>Alamat</th>
                      <th>Saldo</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    
                    @foreach ($members as $member)
                      <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $member->nip }}</td>
                        <td>{{ $member->name }}</td>
                        <td>{{ $member->telphone }}</td>
                        <td>{{ $member->address }}</td>
                        <td>{{ number_format($member->balance, 2) }}</td>
                        <td><button class="btn btn-sm dropdown-toggle more-horizontal" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="text-muted sr-only">Action</span>
                          </button>
                          <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{ route('members.show', $member->id) }}">View</a>
                            <a class="dropdown-item" href="{{ route('members.edit', $member->id) }}">Edit</a>
                            <form action="{{ route('members.destroy', $member->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item">Delete</button>
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
    $('#members').DataTable(
    {
      autoWidth: true,
      "lengthMenu": [
        [10, 25, 50, -1],
        [10, 25, 50, "All"]
      ]
    });
</script>
@endsection