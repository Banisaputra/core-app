@extends('layouts.main')

@section('title')
    <title>Pelunasan - Sistem Informasi Koperasi dan Usaha</title>
@endsection
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
{{-- modal confirm --}}
<div class="modal fade" id="settleConfirmModal" tabindex="-1" role="dialog" aria-labelledby="settleConfirmModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form action="{{ route('repayments.settleConfirm')}}" method="post">
        @csrf
        <input type="hidden" id="member_id" name="member_id" value="">
        <div class="modal-header">
          <h5 class="modal-title" id="settleConfirmModalLabel">Konfirmasi Pelunasan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">x</span>
          </button>
        </div>
        <div class="modal-body"> 
          <div class="card-body">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Kode</th>
                  <th>Angsuran (%)</th>
                  <th>Penalti</th>
                  <th>Total Pelunasan</th>
                </tr>
              </thead>
              <tbody id="confirmTable">
                {{-- content --}}
              </tbody>
              <tfoot>
                <tr>
                  <td colspan='3' style="text-align: right"><b>Subtotal</b></td>
                  <td id="bungaTotal"></td>
                  <td id="subtotal"></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn mb-2 btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn mb-2 btn-primary">Konfirmasi</button>
        </div>
      </form>
    </div>
  </div>
</div>


<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12 col-xl-10">
      <div class="row align-items-center my-4">
        <div class="col">
          <h2 class="h3 mb-0 page-title">Pelunasan Pinjaman</h2>
          <p>Pelunasan Cepat untuk semua pinjaman aktif</p>
        </div>
      </div>
      <hr class="my-4">
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
      <form action={{ route('repayments.getSettle') }} method="POST" id="form-member" enctype="multipart/form-data">
        @csrf
        <div class="form-row">
          <div class="form-group col-md-6">  
            <label for="simple-select2">Anggota</label>
            <select id="memberSelect" name="member_id" class="form-control" required></select>
          </div>
          <div class="form-group col-md-6">

          </div>
        </div>
        <div class="form-row">
          <div class="col-md-6">
              <button type="submit" class="btn btn-success"><span class="fe fe-16 mr-2 fe-check-circle"></span>Check</button>
          </div>
        </div>
      </form>
      <hr class="my-4">
      @if (count($loans) > 0)
        <button type="button" class="btn mb-2 btn-primary" id="settleConfirm" data-id="{{ $member_id }}" data-toggle="modal" data-target="#settleConfirmModal"><span class="fe fe-16 mr-2 fe-check-circle"></span>Konfirmasi Pelunasan</button>
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
                      <th width="10%">Total tenor</th>
                      <th width="10%">Jatuh Tempo</th>
                      <th width="15%">Sisa Tenor</th>
                      <th width="15%">Bunga Pinjaman</th>
                      <th width="15%">Sisa Pokok</th>
                      <th width="15%">Subtotal</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($loans as $loan)
                      <?php 
                        $bunga = (($loan->interest_percent * $loan->loan_value) / 100);
                        $minAngsur = ceil(($loan->loan_tenor*30)/100);
                        $currAngsur = $loan->loan_tenor - $loan->payments->where('lp_state', 1)->count();
                        $sisa_pokok = $loan->payments->where('lp_state', 1)->sum('lp_value');
                        // $subtotal = $loan->payments->where('lp_state', 1)->sum('lp_total');

                      ?>
                      <tr>
                        {{-- <td>{{ $loop->iteration }}</td> --}}
                        <td>{{ $currAngsur }}</td>
                        <td>{{ $loan->loan_code }}</td>
                        <td>{{ $loan->loan_tenor }}</td>
                        <td>{{ date('d M Y', strtotime($loan->due_date)) }}</td>
                        <td>{{ $loan->payments->where('lp_state', 1)->count() }}</td>
                        <td>Rp {{ number_format(($loan->interest_percent * $loan->loan_value) / 100, 0) }}</td>
                        <td>Rp {{ number_format($loan->payments->where('lp_state', 1)->sum('lp_value'), 0) }}</td>
                        <td>Rp {{ number_format( $currAngsur < $minAngsur ? $sisa_pokok + ($bunga*($minAngsur-$currAngsur)) : $sisa_pokok, 0) }}</td>
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
  function formatIDR(value, decimal) {
    return value.toLocaleString('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: decimal
    });
  }
  const loansJS = <?= json_encode($loans); ?>;
  $(document).ready(function() {
    $('#loanDetails').DataTable({
      autoWidth: true,
      "lengthMenu": [
        [10, 25, 50, -1],
        [10, 25, 50, "All"]
      ]
    });
    $('#memberSelect').select2({
      placeholder: 'Search member...',
      theme: 'bootstrap4',
      minimumInputLength: 2,
      multiple: false,
      ajax: {
          url: '/api/members/search',
          dataType: 'json',
          delay: 250,
          data: function (params) {
              return {
                  q: params.term,
                  active: 1
              };
          },
          processResults: function (data) {
              return {
                  results: data.map(function (item) {
                      return {
                          id: item.id,
                          text: "["+ item.nip +"] " + item.name
                      };
                  })
              };
          },
          cache: true
      }
    });

    $('#settleConfirm').on('click', function() {
      let id = $(this).data('id');
      var data = '';
      var total_penalti = 0;
      var total_pokok = 0;
      for (let i = 0; i < loansJS.length; i++) {
        const tempLoan = loansJS[i];
        console.log(tempLoan);
        var bunga = ((tempLoan['loan_value']*tempLoan['interest_percent'])/100)/tempLoan['loan_tenor'];
        var tenor_remaining = 0;
        var value_remaining = 0;

        tempLoan['payments'].forEach(pay => {
          if (pay['lp_state'] == 1) {
            tenor_remaining ++;
            value_remaining += pay['lp_value'];
          }
          
        });
        var angsuran = ((tempLoan['loan_tenor']-tenor_remaining) / tempLoan['loan_tenor']) * 100;
        console.log(angsuran < 30);
        

        data += `<tr>
          <td>${i+1}</td>
          <td>${tempLoan['loan_code']}</td>
          <td>${angsuran}</td>
          <td>${angsuran < 30 ? formatIDR(bunga,0) : formatIDR(0,0)}</td>
          <td>${formatIDR(value_remaining,0) }</td>
        </tr>`

        total_penalti += bunga;
        total_pokok += value_remaining;
        
      }
      $('#settleConfirmModal #member_id').val(id);
      $('#settleConfirmModal #confirmTable').html(data);
      $('#settleConfirmModal #bungaTotal').html("<b>"+formatIDR(total_penalti,0)+"</b>");
      $('#settleConfirmModal #subtotal').html("<b>"+formatIDR(total_pokok,0)+"</b>");
      
    });
  })
</script>
@endsection