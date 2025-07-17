@extends('layouts.main')

@section('page_css')
    <link rel="stylesheet" href="{{ asset('fedash/css/dataTables.bootstrap4.css') }}">
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-12">
        <div class="col">
            <h2 class="h3 mb-0 page-title">Daftar Barang</h2>
            <p class="card-text">Daftar Barang.</p>
        </div>
        <div class="row align-items-center my-4">
            <div class="col">
                <a href="{{ route('items.create') }}" class="btn mb-2 btn-primary">
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
                <table class="table datatables" id="items">
                  <thead>
                    <tr>
                      <th width="5%">No.</th>
                      <th width="15%">Kode</th>
                      <th width="">Supplier</th>
                      <th width="15%">Total</th>
                      <th width="15%">Status</th>
                      <th width="5%">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    
                    @foreach ($purchases as $purchase)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $purchase->pr_code }}</td>
                            <td>{{ $purchase->supplier }}</td>
                            <td>Rp {{ number_format($purchase->sub_total, 0) }}</td>
                            <td>{!! $purchase->is_transactional == 1 ? "<span class='dot dot-lg bg-success mr-1'></span>Aktif" : "<span class='dot dot-lg bg-secondary mr-1'></span>Tidak Aktif" !!}</td>
                            <td><button class="btn btn-sm dropdown-toggle more-horizontal" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="text-muted sr-only">Action</span>
                              </button>
                              <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('items.show', $item->id) }}">View</a>
                                <a class="dropdown-item" href="{{ route('items.edit', $item->id) }}">Edit</a>
                                <form action="{{ route('items.destroy', $item->id) }}" method="POST" style="display: inline;" id="deleteForm">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" id="btnDelete" class="dropdown-item">{{ $item->is_transactional==1 ? "Nonaktifkan" : "Aktifkan"}}</button>
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
    $('#items').DataTable({
      autoWidth: true,
      "lengthMenu": [
        [10, 25, 50, -1],
        [10, 25, 50, "All"]
      ]
    });
    $('#items tbody #btnDelete').on('click', function (e) {
      if (!confirm('Apakah anda yakin untuk perubahan Aktifasi ini?')) {
        e.preventDefault();
      } else {
        $('#deleteForm').submit();
      }

    });
    
  })
</script>
@endsection