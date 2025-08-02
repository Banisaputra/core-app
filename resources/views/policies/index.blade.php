@extends('layouts.main')

@section('title')
    <title>Pengaturan Koperasi</title>
@endsection
@section('page_css')
    <link rel="stylesheet" href="{{ asset('fedash/css/dataTables.bootstrap4.css') }}">
@endsection

@section('content')
@if ($errors->any())
<div class="alert alert-danger" role="alert" id="alertBox">
    @foreach ($errors->all() as $error)
    <span class="fe fe-minus-circle fe-16 mr-2"></span> {{ $error }} <br>           
    @endforeach
</div>
@endif
@if (session()->has('error'))
    <div class="alert alert-danger" role="alert" id="alertBox">
    <span class="fe fe-minus-circle fe-16 mr-2"></span> {{ session('error') }} <br>           
    </div>
@endif
@if (session()->has('success'))
    <div class="alert alert-success" role="alert" id="alertBox">
    <span class="fe fe-help-circle fe-16 mr-2"></span> {{ session('success') }} <br>           
    </div>
@endif
{{-- modal saving --}}
<div class="modal fade" id="savingTypeModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="savingTypeForm">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Form Jenis Simpanan</h5>
           <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">x</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="_method" id="method">
          <input type="hidden" id="type_id">
          <div class="mb-3">
            <label>Nama</label>
            <input type="text" name="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Deskripsi</label>
            <textarea name="description" class="form-control" required></textarea>
          </div>
          <div class="mb-3">
            <label>Nominal</label>
            <input type="number" name="value" class="form-control" min="1" required></input>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn mb-2 btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn mb-2 btn-primary">Simpan</button>
        </div>
      </div>
    </form>
  </div>
</div>

    <div class="col-md-12 mb-3">
        <div class="card shadow">
            <div class="card-body py-4 mb-1">
                <div class="row">
                    {{-- menu --}}
                <div class="col-2">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link active" id="v-pills-condition-tab" data-toggle="pill" href="#v-pills-condition" role="tab" aria-controls="v-pills-condition" aria-selected="true">Syarat Ketentuan</a>
                        <a class="nav-link" id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false">Simpanan</a>
                        <a class="nav-link" id="v-pills-messages-tab" data-toggle="pill" href="#v-pills-messages" role="tab" aria-controls="v-pills-messages" aria-selected="false">Pinjaman</a>
                    </div>
                </div>
                {{-- content --}}
                <div class="col-10">
                    <div class="tab-content mb-4" id="v-pills-tabContent">
                    <div class="tab-pane fade active show" id="v-pills-condition" role="tabpanel" aria-labelledby="v-pills-condition-tab">
                        <form action="{{ route('policy.upload') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label for="terms">Syarat & Ketentuan</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="terms" name="fileTerms">
                                    <label class="custom-file-label" for="terms" id="label_terms">Choose file</label>
                                    <small>*Format file PDF dengan ukuran max:10MB</small>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </form>
                        <hr class="my-4" style="border-top: 1px solid #919192;">
                        <h4>Syarat dan Ketentuan</h4>

                        @if ($pdfExists)
                            <div class="embed-responsive embed-responsive-16by9" style="height: 80vh;">
                                <iframe src="{{ $fileUrl }}" width="100%" height="100%" frameborder="0"></iframe>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                File syarat dan ketentuan belum tersedia.
                            </div>
                        @endif

                    </div>
                    <div class="tab-pane fade mb-4" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                        <div class="container-fluid">
                            <div class="row justify-content-center">
                            <div class="col-12">
                                <div class="col">
                                    <h2 class="h3 mb-0 page-title">Jenis Simpanan</h2>
                                </div>
                                <div class="row align-items-center my-4">
                                    <div class="col">
                                       <button class="btn btn-primary mb-3" id="btnAdd">Tambah Data</button>
                                    </div>
                                    <div class="col-auto">
                                        {{-- other button --}}
                                    </div>
                                </div>
                            
                                <div class="row my-4"> 
                                    <div class="col">
                                        <div class="card shadow">
                                        <div class="card-body"> 
                                            <table class="table datatables" id="savingType">
                                            <thead>
                                                <tr>
                                                <th width="5%">No.</th> 
                                                <th width="15%">Nama</th>
                                                <th width="45%">Deskripsi</th>
                                                <th width="20%">Nominal</th>
                                                <th width="10%">Status</th>
                                                <th width="5%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                
                                                @foreach ($svTypes as $svt)
                                                <tr data-id="{{ $svt->id }}">
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $svt->name }}</td>
                                                    <td>{{ $svt->description }}</td>
                                                    <td>Rp {{ number_format($svt->value, 2) }}</td>
                                                    <td>{!! $svt->is_transactional == 1 ? "<span class='dot dot-lg bg-success mr-1'></span>Aktif" : "<span class='dot dot-lg bg-secondary mr-1'></span>Tidak Aktif" !!}</td>
                                                    <td><button class="btn btn-sm dropdown-toggle more-horizontal" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <span class="text-muted sr-only">Action</span>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <button class="dropdown-item btn-edit">Edit</button>
                                                        <form action="{{ route('saving-types.destroy', $svt->id) }}" method="POST" style="display: inline;" id="deleteForm">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" id="btnDelete" class="dropdown-item">{{ $svt->is_transactional==1 ? "Nonaktifkan" : "Aktifkan"}}</button>
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

                    </div>
                    <div class="tab-pane fade mb-4" id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab"> Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. </div>
                    <div class="tab-pane fade mb-4" id="v-pills-settings" role="tabpanel" aria-labelledby="v-pills-settings-tab"> Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. </div>
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
function showAlert(type, message) {
    const html = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;

    $('#notifBox').html(html);

    // Hilangkan otomatis setelah 3 detik
    setTimeout(() => {
        $('#notifBox .alert').fadeOut();
    }, 3000);
}
$(document).ready(function() {
    setTimeout(function() {
        $('#alertBox').fadeOut('slow');
    }, 3000);

    $('#terms').on('change', function(event) {
        const file = event.target.files[0];
        const fileNameDisplay = $('#label_terms');
        fileNameDisplay.html( file ? file.name.substr(1, 70) : 'Choose file');
    });

    $('#savingType').DataTable({
        autoWidth: false,
        "lengthMenu": [
            [5, 20, 50, -1],
            [5, 20, 50, "All"]
        ]
    });

    const modal = new bootstrap.Modal($('#savingTypeModal')[0]);

    $('#btnAdd').on('click', function () {
        $('#savingTypeForm')[0].reset();
        $('#savingTypeForm .alert').remove();
        $('#method').val('');
        $('#type_id').val('');
        modal.show();
    });

    $('#savingType').on('click', '.btn-edit', function () {
        const id = $(this).closest('tr').data('id');
        $.get(`/saving-types/${id}/edit`, function (data) {
            $('[name="name"]').val(data.name);
            $('[name="description"]').val(data.description);
            $('[name="value"]').val(data.value ?? 0);
            $('#type_id').val(data.id);
            $('#method').val('PUT');
            modal.show();
        });
    });  

    $('#savingTypeForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#type_id').val();
        const method = $('#method').val() || 'POST';
        const url = id ? `/saving-types/${id}` : `/saving-types`;
        const formData = $(this).serialize();
        // Hapus error lama
        $('#savingTypeForm .alert').remove();

        $.ajax({
            url,
            type: method === 'PUT' ? 'POST' : 'POST',
            data: method === 'PUT' ? formData + '&_method=PUT' : formData,
            success: function (res) {
                if (res.success) {
                    location.reload(); // untuk load flash session
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    const errors = xhr.responseJSON.errors;
                    let html = '<div class="alert alert-danger"><ul>';
                    Object.keys(errors).forEach(function (key) {
                        html += `<li>${errors[key][0]}</li>`;
                    });
                    html += '</ul></div>';
                    $('#savingTypeModal .modal-body').prepend(html);
                } else {
                    $('#savingTypeModal .modal-body').prepend(`
                        <div class="alert alert-danger">
                            ${xhr.responseJSON.message || 'Terjadi kesalahan saat menyimpan data.'}
                        </div>
                    `);
                }
            }
        });
    });
});
</script>

@endsection
