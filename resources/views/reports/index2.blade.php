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
          <h2 class="h3 mb-0 page-title">Laporan Anggota</h2>
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
      <h5 class="mb-2 mt-4"> Daftar Anggota</h5>
      <p class="mb-4">Laporan penarikan data yang tercatat sesuai periode dan jenis laporan.</p>
       
      <form action={{ route('reports.getMemberList') }} method="POST" id="form-report-1" data-form-id="form1" enctype="multipart/form-data">
        @csrf
        <div class="form-row">
          <div class="form-group col-md-3">
              <label for="reportSelect-1">Jenis Laporan</label>
              <select id="reportSelect-1" name="typeReport" class="form-control">
                <option value="">-- Pilih laporan </option>
                <option value="member">Data Anggota</option>
              </select>
          </div>
          <div class="form-group col-md-3">
              <label for="aktifasiSelect">Status Aktifasi</label>
              <select id="aktifasiSelect" name="activate" class="form-control">
                <option value="2">SEMUA</option>
                <option value="1">Aktif</option>
                <option value="0">Tidak Aktif</option>
              </select>
          </div>
          <div class="form-group mb-3 col-md-3">
            <label for="startJoined">Tanggal Mulai Bergabung</label>
            <input class="form-control" id="startJoined" type="date" name="startJoined">
            <small>*Kosongkan untuk mengambil semua data</small>
          </div>
          <div class="form-group mb-3 col-md-3">
            <label for="endJoined">Batas Tanggal Bergabung</label>
            <input class="form-control" id="endJoined" type="date" name="endJoined">
            <small>*Kosongkan untuk mengambil semua data</small>
          </div>
        </div>

        <hr class="my-4">
         <div class="form-row">
            <div class="col-md-6">
              {{-- <small>Note: </small> --}}
            </div>
            <div class="col-md-6 text-right">
              <button type="button" id="preview-btn-1" data-form="form1" class="btn btn-info mr-2 preview-btn">
                <span class="fe fe-16 mr-2 fe-eye"></span>
                <span id="preview-text-1">Preview</span>
                <span id="preview-spinner-1" class="spinner-border spinner-border-sm d-none" role="status"></span>
              </button>
              <button type="submit" id="submit-btn-1" class="btn btn-primary">
                <span class="fe fe-16 mr-2 fe-download"></span>
                <span id="submit-text-1">Download</span>
                <span id="download-spinner-1" class="spinner-border spinner-border-sm d-none" role="status"></span>
              </button>
           </div>
         </div>
      </form>

      <hr class="my-4">
      <h5 class="mb-2 mt-4"> Detil Anggota</h5>
      <p class="mb-4">Laporan penarikan data yang tercatat sesuai periode dan jenis laporan.</p>
       
      <form action={{ route('reports.getMemberDetail') }} method="POST" id="form-report-2" data-form-id="form2" enctype="multipart/form-data">
        @csrf
        <div class="form-row">
          <div class="form-group col-md-6">
              <label for="reportSelect-2">Jenis Laporan</label>
              <select id="reportSelect-2" name="typeReport" class="form-control">
                <option value="">-- Pilih laporan </option>
                <option value="detail_saving">Simpanan Anggota</option>
                <option value="detail_loans">Pinjaman Anggota</option>
              </select>
          </div>
          <div class="form-group col-md-6">
            <label for="simple-select2">Anggota</label>
            <select id="memberSelect" name="member_id" class="form-control"></select>
          </div>
          <div class="form-group mb-3 col-md-3">
            <label for="startJoined">Tanggal Mulai</label>
            <input class="form-control" id="startJoined" type="date" name="startJoined">
            <small>*Kosongkan untuk mengambil semua data</small>
          </div>
          <div class="form-group mb-3 col-md-3">
            <label for="endJoined">Batas Tanggal</label>
            <input class="form-control" id="endJoined" type="date" name="endJoined">
            <small>*Kosongkan untuk mengambil semua data</small>
          </div>
          <div class="form-group col-md-3">
              <label for="aktifasiSelect">Status Aktifasi</label>
              <select id="aktifasiSelect" name="activate" class="form-control">
                <option value="2">SEMUA</option>
                <option value="1">Aktif</option>
                <option value="0">Tidak Aktif</option>
              </select>
          </div>
        </div>

        <hr class="my-4">
        <div class="form-row">
            <div class="col-md-6">
              {{-- <small>Note: </small> --}}
            </div>
            <div class="col-md-6 text-right">
              <button type="button" id="preview-btn-2" data-form="form2" class="btn btn-info mr-2 preview-btn">
                <span class="fe fe-16 mr-2 fe-eye"></span>
                <span id="preview-text-2">Preview</span>
                <span id="preview-spinner-2" class="spinner-border spinner-border-sm d-none" role="status"></span>
              </button>
              <button type="submit" id="submit-btn-2" class="btn btn-primary">
                <span class="fe fe-16 mr-2 fe-download"></span>
                <span id="submit-text-2">Download</span>
                <span id="download-spinner-2" class="spinner-border spinner-border-sm d-none" role="status"></span>
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
                    active: 2
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
  });
</script>
<script>
// Function untuk handle AJAX request
function handleReportRequest(isPreview = false, id = 0) {
    const form = document.getElementById('form-report-'+id);
    const previewBtn = document.getElementById('preview-btn-'+id);
    const previewText = document.getElementById('preview-text-'+id);
    const previewSpinner = document.getElementById('preview-spinner-'+id);
    const submitBtn = document.getElementById('submit-btn-'+id);
    const submitText = document.getElementById('submit-text-'+id);
    const downloadSpinner = document.getElementById('download-spinner-'+id);
    const reportType = document.getElementById('reportSelect-'+id).value;
     
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
          // Download seperti biasa
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
document.querySelectorAll('.preview-btn').forEach(button => {
  button.addEventListener('click', function() {
    const ID = this.getAttribute('id').split('-')[2];
    handleReportRequest(true, ID);

  });
});

document.getElementById('form-report-1').addEventListener('submit', function(e) {
    e.preventDefault();
    handleReportRequest(false, 1);
});
document.getElementById('form-report-2').addEventListener('submit', function(e) {
    e.preventDefault();
    handleReportRequest(false, 2);

});
</script>
 
@endsection