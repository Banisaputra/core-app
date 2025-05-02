@extends('layouts.main')

@section('content')
<div class="container-fluid">
   <div class="row justify-content-center">
     <div class="col-12 col-xl-10">
       <div class="row align-items-center my-4">
         <div class="col">
           <h2 class="h3 mb-0 page-title">Tambah Karyawan</h2>
         </div>
         <div class="col-auto">
           <button type="button" class="btn btn-success"><span class="fe fe-16 mr-2 fe-download"></span>Import Data <small>(soon)</small></button>
         </div>
       </div>
       <form>
         <hr class="my-4">
         <h5 class="mb-2 mt-4">Personal</h5>
         <p class="mb-4">Data personal sesuai KTP </p>
         <div class="form-row">
           <div class="form-group col-md-8">
             <label for="inpName">Nama Lengkap</label>
             <input type="text" id="inpName" name="name" class="form-control">
           </div>
           <div class="form-group col-md-4">
             <label for="inpGender">Jenis Kelamin</label>
             <select id="inpGender" name="gender" class="form-control">
               <option value="">-- Tidak Memilih</option>
               <option value="pria">Pria</option>
               <option value="wanita">Wanita</option>
             </select>
           </div>
         </div>
         <div class="form-row">
           <div class="form-group col-md-8">
             <label for="inpEmail">Email</label>
             <input type="email" class="form-control" id="inpEmail" name="email">
           </div>
           <div class="form-group col-md-4">
             <label for="inpTelp">No.Telp</label>
             <input type="number" class="form-control" id="inpTelp" name="no_telp" max="20">
           </div>
         </div>
         <div class="form-row">
            <div class="form-group col-md-4">
               <label for="inpBirthday">Tanggal Lahir</label>
               <input class="form-control" id="inpBirthday" type="date" name="birthday">
            </div>
            <div class="form-group col-md-8">
               <label for="inpAddress">Alamat</label>
               <textarea class="form-control" id="inpAddress" placeholder="alamat sekarang atau sesuai ktp"></textarea>
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
     </div> <!-- .col-12 -->
   </div> <!-- .row -->
</div>
@endsection