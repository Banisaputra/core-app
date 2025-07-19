@extends('layouts.main')

@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12 col-xl-10">
      <div class="row align-items-center my-4">
        <div class="col">
          <h2 class="h3 mb-0 page-title">Ubah Supplier</h2>
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
       
      <form action={{ route('supplier.update', $supplier->id) }} method="POST" id="form-supplier" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="code">Kode</label>
            <input type="text" class="form-control" id="code" name="code" value="{{ old('code', $supplier->code ?? '')}}">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="name">Nama</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $supplier->name ?? '')}}">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="address">Alamat</label>
            <textarea class="form-control" id="address" name="address" rows="4">{{ old('address', $supplier->address ?? '')}}</textarea>
          </div>
        </div>
        <div class="form-row"> 
          <div class="form-group col-md-6">
            <div class="custom-control custom-switch mb-2">
               <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" {{ $supplier->is_active == 1 ? "checked" : ""}}>
               <label class="custom-control-label" for="is_active">Status Aktifasi</label>
            </div>
          </div>
        </div>
         <hr class="my-4">
         <div class="form-row">
           <div class="col-md-6 text-left">
             <button type="submit" class="btn btn-primary"><span class="fe fe-16 mr-2 fe-check-circle"></span>Update</button>
           </div>
         </div>
      </form>
    </div>
  </div>
</div>
@endsection