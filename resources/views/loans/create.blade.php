@extends('layouts.main')

@section('title')
    <title>Tambah Pinjaman - Sistem Informasi Koperasi dan Usaha</title>
@endsection

@section('page_css')
    
@endsection

@section('content')
{{-- syarat pinjaman --}}
<div class="modal fade modal-right modal-slide" tabindex="-1" role="dialog" aria-labelledby="defaultModalLabel" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="defaultModalLabel">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">x</span>
          </button>
        </div>
        <div class="modal-body" style="overflow-y: auto;">
            @if ($pdfExists)
                <div class="embed-responsive embed-responsive-16by9" style="height: 80vh;width: 100vh">
                    <iframe src="{{ $fileUrl }}" width="100%" height="100%" frameborder="0"></iframe>
                </div>
            @else
                <div class="alert alert-warning">
                    File syarat dan ketentuan belum tersedia.
                </div>
            @endif
        </div>
        <div class="modal-footer">
          <button type="button" class="btn mb-2 btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
  </div>
</div>

<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12 col-xl-10">
      <div class="row align-items-center my-4">
        <div class="col">
          <h2 class="h3 mb-0 page-title">Tambah Pinjaman</h2>
        </div>
        
      </div>
      <form action="{{ route('loans.store')}}" method="POST" enctype="multipart/form-data">
        @csrf
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
            <h5>{{ $loan_code }}</h5>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="simple-select2">Anggota</label>
            <select id="memberSelect" name="member_id" class="form-control"></select>
          </div>
          <div class="form-group col-md-3">
            <label for="loan_date">Tanggal Pinjaman</label>
            <input type="date" class="form-control" id="loan_date" name="loan_date" value="{{old('loan_date')}}">
          </div>
          <div class="form-group col-md-3">
            <label for="loan_value">Jumlah Pinjaman</label>
            <input type="text" class="form-control" id="loan_value" name="loan_value" data-value="" value="{{old('loan_value')}}">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-3">
            <label for="loan_tenor">Tenor Bulan</label>
            <input type="number" class="form-control" id="loan_tenor" name="loan_tenor">
          </div>
          <div class="form-group col-md-3">
            <label for="interest_percent">Bunga Cicilan</label>
            <input type="number" class="form-control" id="interest_percent" name="interest_percent" value="{{ $loan_policies['bunga_pinjaman']['value'] ?? 0 }}" readonly>
          </div>
          <div class="form-group col-md-3">
            <label for="due_date">Jatuh Tempo</label>
            <input type="date" class="form-control" id="due_date" name="due_date" value="{{old('due_date')}}" readonly>
          </div>
          <div class="form-group col-md-3">
            <label for="religion">Status</label>
            <select class="custom-select" name="loan_status" id="loan_status">
              <option value="1" selected>Diajukan</option>
            </select>
          </div>
        </div>
        <hr class="my-4">
        <div class="row mb-2">
          <div class="col-md-6">
            <div class="form-group">
              <label for="ln_agunan">Jaminan Pinjaman</label>
                <select class="custom-select" name="ln_agunan" id="ln_agunan" disabled>
                  <option value="">-- Pilih Jaminan</option>
                  <option value="MOTOR">BPKB MOTOR</option>
                  <option value="MOBIL">BPKB MOBIL</option>
                  <option value="SERTIFIKAT">SERTIFIKAT</option>
                </select>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="ln_docYear">Tahun</label>
                  <input type="number" class="form-control" id="ln_docYear" name="ln_docYear" value="{{ old('ln_docYear')}}" disabled>
                </div>
                <div class="form-group col-md-6">
                  <label for="ln_docNumber">Nomor Dokumen</label>
                  <input type="text" class="form-control" id="ln_docNumber" name="ln_docNumber" value="{{ old('ln_docNumber')}}" disabled>
                </div>
            </div>
            <div class="form-group">
              <label for="ln_docDetail">Detail Dokumen</label>
              <textarea class="form-control" id="ln_docDetail" name="ln_docDetail" rows="3" disabled>{{ old('ln_docDetail')}}</textarea>
            </div>
          </div>
          <div class="col-md-6">
            <div class="custom-control custom-switch mb-3">
              <input type="checkbox" class="custom-control-input" id="cbAgunan" name="cbAgunan">
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
            <small>*informasi lainnya, <a href="javascript:void(0)" data-toggle="modal" data-target=".modal-right">Syarat dan ketentuan lebih lanjut</a></small>
          </div>
          <div class="col-md-6 text-right">
            <button type="submit" class="btn btn-primary"><span class="fe fe-16 mr-2 fe-check-circle"></span>Simpan</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('page_script')
<script>
  const cut_off_day = {{ $cut_off_day }};

  function hitungTanggalAngsuranPertama(tanggalPinjam, cutoffDay = 1) {
    const tanggal = new Date(tanggalPinjam);
    const hariPinjam = tanggal.getDate();
    
    // Buat salinan tanggal untuk dihitung
    const angsuranPertama = new Date(tanggal);
    
    if (hariPinjam <= cutoffDay) {
        // Angsuran pertama 1 bulan depan
        angsuranPertama.setMonth(angsuranPertama.getMonth() + 0);
    } else {
        // Angsuran pertama 2 bulan depan
        angsuranPertama.setMonth(angsuranPertama.getMonth() + 1);
    }
    
    // Set selalu tanggal 2
    angsuranPertama.setDate(2);
    
    return angsuranPertama.toISOString().split('T')[0]; // Format YYYY-MM-DD
  }

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
        var firstAngsuran = hitungTanggalAngsuranPertama(loanDate, cut_off_day);
        console.log(firstAngsuran);
        
        const date = new Date(firstAngsuran);
        date.setMonth(date.getMonth() + loanTenor);
        
        // Format to YYYY-MM-DD (HTML date input format)
        const formattedDate = date.toISOString().split('T')[0];
        dueDate.val(formattedDate);
      } else {
        dueDate.val(loanDate);
      }
    });

    $('#loan_date').on('change', function() {
        $('#loan_tenor').trigger('keyup');
    });
 
    $('#cbAgunan').on('click', function (e) {
      if ($(this).is(':checked')) {
        $('#ln_agunan').prop('disabled', false);
        $('#ln_docNumber').prop('disabled', false);
        $('#ln_docDetail').prop('disabled', false);
        $('#ln_docYear').prop('disabled', false);
      } else {
        $('#ln_agunan').val('').prop('disabled', true);
        $('#ln_docNumber').val('').prop('disabled', true);
        $('#ln_docDetail').val('').prop('disabled', true);
        $('#ln_docYear').val('').prop('disabled', true);
      }
    })

    $('#loan_value').on('input', function() {
      // Save cursor position
      const cursorPosition = this.selectionStart;
      const originalLength = this.value.length;
      
      // Get raw value without formatting
      let value = $(this).val().replace(/[^\d]/g, '');
      
      // Format to IDR
      if (value.length > 0) {
          let formattedValue = '';
          for (let i = 0; i < value.length; i++) {
              if (i > 0 && (value.length - i) % 3 === 0) {
                  formattedValue += '.';
              }
              formattedValue += value[i];
          }
          
          $(this).val(formattedValue);
          
          // Adjust cursor position based on added dots
          const newLength = formattedValue.length;
          const lengthDiff = newLength - originalLength;
          const newCursorPosition = cursorPosition + lengthDiff;
          this.setSelectionRange(newCursorPosition, newCursorPosition);
      } else {
          $(this).val('');
      }
    });

    // For form submission
    $('#loan_value').on('blur', function() {
      const numericValue = $(this).val().replace(/\./g, '');
      $(this).attr('data-value', numericValue)
    });

  });
</script>
@endsection