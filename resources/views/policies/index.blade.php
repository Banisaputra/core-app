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


    <div class="col-md-12 mb-3">
        <div class="card shadow">
            <div class="card-body py-4 mb-1">
                <div class="row">
                    {{-- menu --}}
                    <div class="col-2">
                        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <a class="nav-link active" id="v-pills-terms-tab" data-toggle="pill" href="#v-pills-terms" role="tab" aria-controls="v-pills-terms" aria-selected="true">Syarat Ketentuan</a>
                            <a class="nav-link" id="v-pills-saving-tab" data-toggle="pill" href="#v-pills-saving" role="tab" aria-controls="v-pills-saving" aria-selected="false">Simpanan</a>
                            <a class="nav-link" id="v-pills-loan-tab" data-toggle="pill" href="#v-pills-loan" role="tab" aria-controls="v-pills-loan" aria-selected="false">Pinjaman</a>
                        </div>
                    </div>
                    {{-- content --}}
                    <div class="col-10">
                        <div class="tab-content mb-4" id="v-pills-tabContent">
                            <div class="tab-pane fade active show" id="v-pills-terms" role="tabpanel" aria-labelledby="v-pills-terms-tab">
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
                            <div class="tab-pane fade mb-4" id="v-pills-saving" role="tabpanel" aria-labelledby="v-pills-saving-tab">
                                @include('policies.saving')
                            </div>
                            <div class="tab-pane fade mb-4" id="v-pills-loan" role="tabpanel" aria-labelledby="v-pills-loan-tab"> 
                                @include('policies.loan')
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

    $('#svtSelect').select2({
        placeholder: 'Search Simpanan...',
        theme: 'bootstrap4',
        minimumInputLength: 2,
        multiple: true,
        ajax: {
            url: '/api/saving-type/search',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term
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

@endsection
