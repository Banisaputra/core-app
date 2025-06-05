@extends('layouts.main')

@section('page_css')
    <link rel="stylesheet" href="{{ asset('fedash/css/dataTables.bootstrap4.css') }}">
    <style>
      .quantity-control {
        display: flex;
        align-items: center;
        gap: 5px;
      } 

    </style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
      <div class="col-12">
        <div class="col">
            <h2 class="h3 mb-0 page-title">Daftar Pelunasan</h2>
            <p class="card-text">Daftar angsuran pinjaman anggota bulan ini.</p>
        </div>
        <div class="row align-items-center my-4">
            <div class="col">
                <a href="#" class="btn mb-2 btn-primary">
                    <span class="fe fe-plus fe-16 mr-1"></span> Tambah Pelunasan
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
          <div class="col-md-12">
            <div class="card shadow">
              <div class="card-body">
                <table class="table datatables" id="loanDetails">
                  <thead>
                    <tr>
                      <th width="5%">No.</th>
                      <th width="10%">Kode Pinjaman</th>
                      <th width="20%">Anggota</th>
                      <th width="10%">Kode Angsuran</th>
                      <th width="15%">Jumlah</th>
                      <th width="15%">Status</th>
                      <th width="5%">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    
                    @foreach ($loanDetails as $loan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $loan->loan_code }}</td>
                            <td>{{ ucwords($loan->member->name) }}</td>
                            <td><ul class="list-unstyled">
                              <?php $total = 0 ?>
                                @foreach ($loan->payments as $payment)
                                <?php $total += $payment->lp_total ?>
                                <li class="my-1">{{ $payment->lp_code }}</li> 
                                @endforeach
                            </ul></td>
                            <td>Rp {{ number_format($total, 2) }}</td>
                            <td>
                              @switch($loan->loan_state)
                                  @case(99)
                                      Ditutup
                                      @break
                                  @case(2)
                                      Dibayarkan
                                      @break
                                  @default
                                      Pending
                              @endswitch
                            </td>
                            <td><button class="btn btn-sm dropdown-toggle more-horizontal" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="text-muted sr-only">Action</span>
                              </button>
                              <div class="dropdown-menu dropdown-menu-right">
                                <form action="#" method="POST" style="display: inline;" id="paidForm">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" id="btnPaid" class="dropdown-item">Lunasi</button>
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
    $('#loanDetails').DataTable({
      autoWidth: true,
      "lengthMenu": [
        [10, 25, 50, -1],
        [10, 25, 50, "All"]
      ]
    });
    $('#btnPaid').on('click', function (e) {
        if (!confirm('Apakah anda yakin ingin melunasi cicilan ini?')) {
            e.preventDefault();
        } else {
            $('#paidForm').submit();
        }
    });
  })
</script>
@endsection