@extends('layouts.main')

@section('title')
    <title>Laporan - Sistem Informasi Koperasi dan Usaha</title>
@endsection
@section('page_css')

@endsection
@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12 col-xl-10">
      <div class="row align-items-center my-4">
        <div class="col">
          <h2 class="h3 mb-0 page-title">Laporan Umum</h2>
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
      <h5 class="mb-2 mt-4"> Rekap Laporan</h5>
      <p class="mb-4">Laporan penarikan semua data yang tercatat sesuai periode dan jenis laporan.</p>
       
      <form action={{ route('reports.getReport') }} method="POST" id="form-report" enctype="multipart/form-data">
        @csrf
        <div class="form-row">
          <div class="form-group col-md-6">
              <label for="reportSelect">Jenis Laporan</label>
              <select id="reportSelect" name="typeReport" class="form-control">
                <option value="">-- Pilih laporan </option>
                <option value="saving">Simpanan</option>
                <option value="loan">Pinjaman</option>
                <option value="purchase">Pembelian</option>
                <option value="itemStock">Stok Barang</option>
                <option value="sales">Penjualan</option>
                <option value="profitNlose">Laba Rugi</option>
                <option value="inventory">Adjustment Stock</option>
                {{-- <option value="svsummary">Summary Simpanan</option>
                <option value="lnsummary">Summary Pinjaman</option> --}}
              </select>
          </div>
          <div class="form-group mb-3 col-md-3">
              <label for="dateStart">Mulai</label>
              <input class="form-control" id="dateStart" type="date" name="dateStart">
          </div>
          <div class="form-group mb-3 col-md-3">
              <label for="dateEnd">Sampai</label>
              <input class="form-control" id="dateEnd" type="date" name="dateEnd">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-3">
            <label for="typeSales">Jenis Transaksi</label>
            <select id="typeSales" name="typeSales" class="form-control" disabled>
              <option value="all">SEMUA</option>
              <option value="cash">Cash</option>
              <option value="kredit">Kredit</option>
            </select>
          </div>
          <div class="form-group col-md-3">
            <label for="typeStock">Type stock</label>
            <select id="typeStock" name="typeStock" class="form-control" disabled>
              <option value="all">SEMUA</option>
              <option value="10">Dibawah 10</option>
              <option value="0">Stok Kosong</option>
            </select>
          </div>
           
        </div>

        <hr class="my-4">
         <div class="form-row">
           <div class="col-md-6">
            <small>Note:laporan berdasarkan tanggal dibuatnya dokumen.</small>
           </div>
           <div class="col-md-6 text-right">
            <button type="button" id="preview-btn" class="btn btn-info mr-2">
              <span class="fe fe-16 mr-2 fe-eye"></span>
              <span id="preview-text">Preview</span>
              <span id="preview-spinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
            </button>
            <button type="submit" id="submit-btn" class="btn btn-primary">
              <span class="fe fe-16 mr-2 fe-download"></span>
              <span id="submit-text">Download</span>
              <span id="download-spinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
            </button>
           </div>
         </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('page_script')
<script>
  $(document).ready(function() {
    $('#reportSelect').on("change", function () {
      let type = $(this).val().toUpperCase();

      switch (type) {
        case "SALES":
          $('#typeSales').prop('disabled', false);
          $('#typeStock').val("all").prop('disabled', true);
          break;
        case "ITEMSTOCK":
          $('#typeStock').prop('disabled', false);
          $('#typeSales').val("all").prop('disabled', true);
          break;
      
        default:
          $('#typeSales').val('all').prop('disabled', true);
          $('#typeStock').val('all').prop('disabled', true);
          break;
      }
      
    })
  });
</script>
<script>
// Function untuk handle AJAX request
function handleReportRequest(isPreview = false) {

    const form = document.getElementById('form-report');
    const reportType = document.getElementById('reportSelect').value;

    const formData = new FormData(form);
    if (isPreview) formData.append('preview', 'true');

    showLoader();

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => {

        const contentType = response.headers.get('Content-Type') || '';

        // 🚨 BUKAN PDF = ERROR
        if (!contentType.includes('application/pdf')) {
            return response.text().then(text => {
              console.log('===== RESPONSE BUKAN PDF =====');
              console.log(text);
              console.log('==============================');
              throw new Error('Response bukan PDF');
            });
        }

        return response.blob();
    })
    .then(blob => {

        const blobUrl = URL.createObjectURL(blob);

        if (isPreview) {
            window.open(blobUrl, '_blank');
        } else {
            const now = new Date();
            const filename = `Laporan-${reportType}-${now.getTime()}.pdf`;

            const link = document.createElement('a');
            link.href = blobUrl;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        URL.revokeObjectURL(blobUrl);
    })
    .catch(err => {
        console.error(err);
        alert('Gagal generate PDF');
    })
    .finally(() => {
        hideLoader();
        // reset UI
        document.getElementById('preview-btn')?.removeAttribute('disabled');
        document.getElementById('submit-btn')?.removeAttribute('disabled');
    });
}


// Event listeners
document.getElementById('preview-btn').addEventListener('click', function() {
    handleReportRequest(true);    
});

document.getElementById('form-report').addEventListener('submit', function(e) {
    e.preventDefault();
    handleReportRequest(false);
});
</script>
 
@endsection