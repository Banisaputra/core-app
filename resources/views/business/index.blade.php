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
                            <a class="nav-link active" id="v-pills-sales-tab" data-toggle="pill" href="#v-pills-sales" role="tab" aria-controls="v-pills-sales" aria-selected="false">Simpanan</a>
                        </div>
                    </div>
                    {{-- content --}}
                    <div class="col-10">
                        <div class="tab-content mb-4" id="v-pills-tabContent"> 
                            <div class="tab-pane fade active show" id="v-pills-sales" role="tabpanel" aria-labelledby="v-pills-sales-tab">
                                @include('business.sales')
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_script')
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

    // sales
    $(document).on({
        input: function() {
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
        }
    }, '#salesMargin' );

    // For form submission
    $(document).on({
        blur: function(e) {
            const numericValue = $(this).val().replace(/\./g, '');
      $(this).attr('data-value', numericValue)
        }
    },'#salesMargin' );

});
</script>

@endsection
