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
          <h2 class="h3 mb-0 page-title">Laporan</h2>
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
      <h5 class="mb-2 mt-4"> Laporan</h5>
      <p class="mb-4">laporan sesuai filter periode</p>
       
      <form action={{ route('reports.getReport') }} method="POST" id="form-member" enctype="multipart/form-data">
        @csrf
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="reportSelect">Jenis Laporan</label>
                <select id="reportSelect" name="typeReport" class="form-control">
                    <option value="">-- Pilih laporan </option>
                    <option value="saving">Simpanan</option>
                    <option value="loan">Pinjaman</option>
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
         
         <hr class="my-4">
         <div class="form-row">
           <div class="col-md-6">
            <small>Note:laporan berdasarkan tanggal dibuatnya dokumen.</small>
           </div>
           <div class="col-md-6 text-right">
             <button type="button" class="btn btn-primary"><span class="fe fe-16 mr-2 fe-check-circle"></span>Submit</button>
           </div>
         </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('page_script')

@endsection