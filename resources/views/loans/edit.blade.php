@extends('layouts.main')

@section('title')
    <title>Edit Pinjaman - Sistem Informasi Koperasi dan Usaha</title>
@endsection

@section('page_css')
    
@endsection

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
            <input type="hidden" name="member_id" value="{{ $loan->member_id }}">
            <select id="memberSelect" class="form-control" {{ $loan->loan_state>0 ? "disabled" : ''}}>
              <option value="{{ $loan->member_id }}" selected>
                {{ $loan->member->name ?? 'Search anggota...' }}
              </option>
            </select>
          </div>
          <div class="form-group col-md-3">
            <label for="loan_date">Tanggal Pinjaman</label>
            <input type="date" class="form-control" id="loan_date" name="loan_date" value="{{old('loan_date', date('Y-m-d', strtotime($loan->loan_date)) ?? '')}}" {{ $loan->loan_state>0 ? "readonly" : ''}}>
          </div>
          <div class="form-group col-md-3">
            <label for="loan_value">Jumlah Pinjaman</label>
            <input type="text" class="form-control" id="loan_value" name="loan_value" value="{{old('loan_value', $loan->loan_value*1 ?? '')}}" {{ $loan->loan_state>0 ? "readonly" : ''}}>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-3">
            <label for="loan_tenor">Tenor Bulan</label>
            <input type="number" class="form-control" id="loan_tenor" name="loan_tenor" value="{{old('loan_tenor', $loan->loan_tenor ?? '')}}" {{ $loan->loan_state>0 ? "readonly" : ''}}>
          </div>
          <div class="form-group col-md-3">
            <label for="interest_percent">Bunga Cicilan(%)</label>
            <input type="number" class="form-control" id="interest_percent" name="interest_percent" value="{{old('interest_percent', $loan->interest_percent ?? '')}}" {{ $loan->loan_state>0 ? "readonly" : ''}}>
          </div>
          <div class="form-group col-md-3">
            <label for="due_date">Jatuh Tempo</label>
            <input type="date" class="form-control" id="due_date" name="due_date" value="{{old('due_date', date('Y-m-d', strtotime($loan->due_date)) ?? '')}}" readonly>
          </div>
          <div class="form-group col-md-3">
            <label for="loan_state">Status Pinjaman</label>
            <select class="custom-select" name="loan_status" id="loan_status" {{ $loan->loan_state>1 ? "disabled" : ''}}>
              <option value="1" {{ $loan->loan_state==1 ? "selected" : ''}}>Diajukan</option>
              <option value="2" {{ $loan->loan_state==2 ? "selected" : ''}}>Disetujui</option>
              <option value="99" {{ $loan->loan_state==99 ? "selected" : ''}}>Ditolak</option>
            </select>
          </div>
        </div>
        <hr class="my-4">
        <div class="row mb-2">
          <div class="col-md-6">
            <div class="form-group">
              <label for="ln_agunan">Jaminan Pinjaman</label>
                <select class="custom-select" name="ln_agunan" id="ln_agunan" disabled>
                  <option value="" {{ $loan->loanAgunan->agunan_type?? '' == '' ? 'selected' : ''}}>-- Pilih Jaminan</option>
                  <option value="MOTOR" {{ $loan->loanAgunan->agunan_type?? '' == 'MOTOR' ? 'selected' : ''}}>BPKB MOTOR</option>
                  <option value="MOBIL" {{ $loan->loanAgunan->agunan_type?? '' == 'MOBIL' ? 'selected' : ''}}>BPKB MOBIL</option>
                  <option value="SERTIFIKAT" {{ $loan->loanAgunan->agunan_type?? '' == 'SERTIFIKAT' ? 'selected' : ''}}>SERTIFIKAT</option>
                </select>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="ln_docYear">Tahun</label>
                  <input type="number" class="form-control" id="ln_docYear" name="ln_docYear" value="{{ $loan->loanAgunan->doc_year?? '' }}" disabled>
                </div>
                <div class="form-group col-md-6">
                  <label for="ln_docNumber">Nomor Dokumen</label>
                  <input type="text" class="form-control" id="ln_docNumber" name="ln_docNumber" value="{{ $loan->loanAgunan->doc_number?? '' }}" disabled>
                </div>
            </div>
            <div class="form-group">
              <label for="ln_docDetail">Detail Dokumen</label>
              <textarea class="form-control" id="ln_docDetail" name="ln_docDetail" rows="3" disabled>{{ $loan->loanAgunan->doc_detail?? '' }}</textarea>
            </div>
          </div>
          <div class="col-md-6">
            <div class="custom-control custom-switch mb-3">
              <input type="checkbox" class="custom-control-input" id="cbAgunan" name="cbAgunan" {{ $loan->ref_doc_id > 0 ? 'checked' : '' }} disabled>
              <label class="custom-control-label" for="cbAgunan">Aktifkan jika menggunakan agunan</label>
            </div>
            <p class="mb-2">Syarat Pinjaman</p>
            <p class="small text-muted mb-2"> pengajuan pinjaman tanpa agunan:</p>
            <ul class="small text-muted pl-4 mb-2">
              <li>Kepesertaan Anggota < 1 tahun max. 2.000.000,-</li>
              <li>Kepesertaan Anggota < 5 tahun max. 3.500.000,-</li>
              <li>Kepesertaan Anggota > 5 tahun max. 5.500.000,-</li>
              <li>Maksimum angsuran 12x</li>
            </ul>
            <p class="small text-muted mb-2"> pengajuan pinjaman dengan agunan:</p>
            <ul class="small text-muted pl-4 mb-0">
              <li>BPKB motor tahun 2010-1025 max. 8.000.000,-</li>
              <li>BPKB motor tahun 2016-2020 max. 10.000.000,-</li>
              <li>BPKB motor tahun 2020-2022 max. 12.000.000,-</li>
              <li>BPKB motor tahun 2023-2025 max. 15.000.000,-</li>
              <li>Khusus BPKB mobil max. 15.000.000,-</li>
              <li>Maksimum angsuran 36x</li>
            </ul>
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

    $('#loan_value').on('input', function() {
      let value = $(this).val().replace(/[^\d.]/g, '');
      
      if ((value.match(/\./g) || []).length > 1) {
          value = value.substring(0, value.lastIndexOf('.'));
      }
      
      if (value) {
          let parts = value.split('.');
          let wholePart = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ".");
          let formattedValue = wholePart;
          
          if (parts.length > 1) {
              formattedValue += ',' + parts[1].substring(0, 2);
          }
          
          $(this).val(formattedValue);
      }
    });
      
    $('#loan_value').on('blur', function() {
      let numericValue = $(this).val().replace(/\./g, '').replace(',', '.');
      console.log('Numeric value:', numericValue);
    });

  });
</script>
@endsection