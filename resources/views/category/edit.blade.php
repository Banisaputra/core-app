@extends('layouts.main')

@section('title')
    <title>Edit Kategori - Sistem Informasi Koperasi dan Usaha</title>
@endsection
@section('page_css')
    
@endsection
@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12 col-xl-10">
      <div class="row align-items-center my-4">
        <div class="col">
          <h2 class="h3 mb-0 page-title">Ubah Kategori</h2>
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
       
      <form action={{ route('category.update', $category->id) }} method="POST" id="form-category" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="code">Kode</label>
            <input type="text" class="form-control" id="code" name="code" value="{{ old('code', $category->code ?? '')}}">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="name">Nama</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $category->name ?? '')}}">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-3">
            <label for="margin_percent">Margin (%)</label>
            <input type="number" class="form-control" id="margin_percent" name="margin_percent" value="{{ old('margin_percent', $category->margin_percent ?? 0)}}">
          </div>
          <div class="form-group col-md-3">
            <label for="margin_price">Margin (Rp)</label>
            <input type="number" class="form-control" id="margin_price" name="margin_price" value="{{ old('margin_price', $category->margin_price ?? 0)}}">
          </div>
        </div>
        <div class="form-row"> 
          <div class="form-group col-md-6">
            <div class="custom-control custom-switch mb-2">
               <input type="checkbox" class="custom-control-input" id="is_turunan" name="is_turunan" {{ $category->is_parent != 1 ? "checked" : ""}}>
               <label class="custom-control-label" for="is_turunan">Kategori Turunan</label>
            </div>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="ct_parent">Kategori Utama</label>
            <select class="custom-select" name="ct_parent" id="ct_parent" {{ $category->is_parent == 1 ? "disabled" : ""}}>
              <option value="">-- Pilih kategori utama</option>
              @foreach ($ct_parent as $ctp)
              <option value="{{$ctp->id}}" {{$category->parent_id==$ctp->id ? "selected" : ""}}>{{ $ctp->name}}</option>
              @endforeach
            </select>
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