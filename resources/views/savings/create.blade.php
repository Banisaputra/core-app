@extends('layouts.main')

@section('content')
<div class="container-fluid">
   <div class="row justify-content-center">
     <div class="col-12 col-xl-10">
       <div class="row align-items-center my-4">
         <div class="col">
           <h2 class="h3 mb-0 page-title">Tambah Simpanan</h2>
         </div>
         
       </div>
       <form>
         <hr class="my-4">
         <h5 class="mb-2 mt-4">Personal</h5>
         <p class="mb-4">Data personal</p>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="svDate">Tanggal</label>
                <input type="date" class="form-control" id="svDate" name="sv_date">
            </div>
            <div class="form-group col-md-4">
                <label for="svType">Jenis Simpanan</label>
                <select id="svType" name="saving_type_id" class="form-control">
                    <option value="">-- Tidak Memilih</option>
                    @foreach ($sv_types as $type)
                        <option value="{{ $type->id }}"> {{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-4">
                <label for="svDate">Tanggal</label>
                <input type="number" class="form-control" id="svDate" name="sv_date">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="member">Nama Anggota</label>
                <select id="member" name="member_id" class="form-control">
                    <option value="">-- Tidak Memilih</option>
                    @foreach ($members as $member)
                        <option value="{{ $member->id }}"> {{ $member->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="member">Nama Anggota</label>
                <input type="checkbox" class="custom-control-input" id="accountGenerate" checked>
            </div>
            
         </div>
          
          
         <hr class="my-4">
         <div class="form-row">
           <div class="col-md-6">
             <div class="custom-control custom-switch mb-2">
               <input type="checkbox" class="custom-control-input" id="accountGenerate" checked>
               <label class="custom-control-label" for="accountGenerate">Generate Account</label>
            </div>
            <small>*Buat akun untuk pengguna dan generate password dari email terdaftar.</small>
           </div>
           <div class="col-md-6 text-right">
             <button type="button" class="btn btn-primary"><span class="fe fe-16 mr-2 fe-check-circle"></span>Simpan</button>
           </div>
         </div>
       </form>
     </div>
   </div>
</div>
@endsection