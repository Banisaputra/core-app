@extends('layouts.main')

@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12 col-xl-10">
      <div class="row align-items-center my-4">
        <div class="col">
          <h2 class="h3 mb-0 page-title">Edit Pinjaman</h2>
        </div>
        
      </div>
      <form action="{{ route('loans.update', $loan->id)}}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
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
        <div class="form-row">
          <div class="form-group col-md-4">
            <label for="loan_code">Kode Pinjaman</label>
            <h5>{{ $loan->loan_code }}</h5>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="simple-select2">Anggota</label>
            <select id="memberSelect" name="member_id" class="form-control" {{ $loan->loan_state>1 ? "disabled" : ''}}>
              <option value="{{ $loan->member_id }}" selected>
                {{ $loan->member->name ?? 'Search anggota...' }}
              </option>
            </select>
          </div>
          <div class="form-group col-md-3">
            <label for="loan_date">Tanggal Pinjaman</label>
            <input type="date" class="form-control" id="loan_date" name="loan_date" value="{{old('loan_date', date('Y-m-d', strtotime($loan->loan_date)) ?? '')}}" {{ $loan->loan_state>1 ? "readonly" : ''}}>
          </div>
          <div class="form-group col-md-3">
            <label for="loan_value">Jumlah Pinjaman</label>
            <input type="number" class="form-control" id="loan_value" name="loan_value" value="{{old('loan_value', $loan->loan_value*1 ?? '')}}" {{ $loan->loan_state>1 ? "readonly" : ''}}>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-3">
            <label for="loan_tenor">Tenor Bulan</label>
            <input type="number" class="form-control" id="loan_tenor" name="loan_tenor" value="{{old('loan_tenor', $loan->loan_tenor ?? '')}}" {{ $loan->loan_state>1 ? "readonly" : ''}}>
          </div>
          <div class="form-group col-md-3">
            <label for="interest_percent">Bunga Cicilan(%)</label>
            <input type="number" class="form-control" id="interest_percent" name="interest_percent" value="{{old('interest_percent', $loan->interest_percent ?? '')}}">
          </div>
          <div class="form-group col-md-4">
            <label for="due_date">Jatuh Tempo</label>
            <input type="date" class="form-control" id="due_date" name="due_date" value="{{old('due_date', date('Y-m-d', strtotime($loan->due_date)) ?? '')}}" readonly>
          </div>
          <div class="form-group col-md-2">
            <label for="loan_state">Status Pinjaman</label>
            @switch($loan->loan_state)
              @case(99)
                <input type="text" class="form-control" id="loan_state" name="loan_state" value="Ditolak" readonly>
                @break
              @case(2)
                <input type="text" class="form-control" id="loan_state" name="loan_state" value="Disetujui" readonly>
                @break
              @default
                <input type="text" class="form-control" id="loan_state" name="loan_state" value="Pengajuan" readonly>
            @endswitch
          </div>
        </div>
        <hr class="my-4">
        <div class="form-row">
          <div class="col-md-6">
            <small>*Kode dibuat otomatis oleh sistem</small>
          </div>
          <div class="col-md-6 text-right">
            <button type="submit" class="btn btn-primary" {{ $loan->loan_state>1 ? "disabled" : ''}}><span class="fe fe-16 mr-2 fe-check-circle"></span>Update</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('page_script')
<script>
  $(document).ready(function () {
    $('#memberSelect').select2({
        placeholder: 'Search anggota...',
        theme: 'bootstrap4',
        minimumInputLength: 2,
        ajax: {
            url: '/api/members/search', // Your route
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term // search term
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function (item) {
                        return {
                            id: item.id,
                            text: item.name
                        };
                    })
                };
            },
            cache: true
        }
    });

    $('#loan_tenor').keyup(function() {
      const loanDate = $('#loan_date').val();
      const loanTenor = parseInt($(this).val()) || 0;
      const dueDate = $('#due_date');
      if (loanDate && loanTenor > 0) { 
        const date = new Date(loanDate);
        date.setMonth(date.getMonth() + loanTenor);
        
        // Format to YYYY-MM-DD (HTML date input format)
        const formattedDate = date.toISOString().split('T')[0];
        dueDate.val(formattedDate);
      } else {
        dueDate.val(loanDate);
      }
    });
    // Also trigger calculation when loan date changes
    $('#loan_date').on('change', function() {
        $('#loan_tenor').trigger('keyup');
    });

  });
</script>
@endsection