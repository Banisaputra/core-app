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
                <option value="sales">Penjualan</option>
                <option value="profitNlose">Laba Rugi</option>
                <option value="inventory">Adjustment Stock</option>
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
          break;
      
        default:
          $('#typeSales').prop('disabled', true);
          break;
      }
      
    })
  });
</script>
<script>
// Function untuk handle AJAX request
function handleReportRequest(isPreview = false) {
    const form = document.getElementById('form-report');
    const previewBtn = document.getElementById('preview-btn');
    const previewText = document.getElementById('preview-text');
    const previewSpinner = document.getElementById('preview-spinner');
    const submitBtn = document.getElementById('submit-btn');
    const submitText = document.getElementById('submit-text');
    const downloadSpinner = document.getElementById('download-spinner');
    const reportType = document.getElementById('reportSelect').value;
     
    // Show loading state
    if (isPreview) {
        previewBtn.disabled = true;
        previewText.textContent = 'Loading...';
        previewSpinner.classList.remove('d-none');
    } else {
        submitBtn.disabled = true;
        submitText.textContent = 'Mengunduh...';
        downloadSpinner.classList.remove('d-none');
    }
    
    // Get form data
    const formData = new FormData(form);
    if (isPreview) {
        formData.append('preview', 'true');
    }

    // AJAX request
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.blob())
    .then(blob => {
    const reader = new FileReader();
    reader.onload = function() {
        const base64 = reader.result;
        console.log(base64);
        
        if (isPreview) {
          // Buat tab baru dengan PDF viewer
          const newWindow = window.open('', '_blank');
          newWindow.document.write(`
              <!DOCTYPE html>
              <html>
              <head>
                <title>Preview Laporan</title>
                <style>
                  body { margin: 0; }
                  iframe { width: 100%; height: 100vh; border: none; }
                </style>
              </head>
              <body>
                <iframe src="data:application/pdf;base64,${base64.split(',')[1]}"></iframe>
              </body>
              </html>
          `);
          newWindow.document.close();
        } else {
          const now = new Date();

          const formattedDate = 
            now.getFullYear() +
            String(now.getMonth() + 1).padStart(2, '0') +
            String(now.getDate()).padStart(2, '0') +
            String(now.getHours()).padStart(2, '0') +
            String(now.getMinutes()).padStart(2, '0') +
            String(now.getSeconds()).padStart(2, '0');

          const blobUrl = URL.createObjectURL(blob);
          const link = document.createElement('a');
          link.href = blobUrl;
          link.download = `Laporan-${reportType}-${formattedDate}.pdf`;
          link.click();
          URL.revokeObjectURL(blobUrl);
        }
    };
    reader.readAsDataURL(blob);
    if (isPreview) {
      previewBtn.disabled = false;
      previewText.textContent = 'Preview';
      previewSpinner.classList.add('d-none');
    } else {
      submitBtn.disabled = false;
      submitText.textContent = 'Download';
      downloadSpinner.classList.add('d-none');
    }
  })
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