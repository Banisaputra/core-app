@extends('layouts.main')

@section('title')
    <title>Tambah Anggota - Sistem Informasi Koperasi dan Usaha</title>
@endsection
@section('page_css')
    
@endsection
@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12 col-xl-10">
      <div class="row align-items-center my-4">
        <div class="col">
          <h2 class="h3 mb-0 page-title">Tambah Anggota</h2>
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
      <h5 class="mb-2 mt-4">Personal</h5>
      <p class="mb-4">Data personal</p>
       
      <form action={{ route('members.store') }} method="POST" id="form-member" enctype="multipart/form-data">
        @csrf
        <div class="form-row">
          <div class="form-group col-md-4">
            <label for="nip">NIK</label>
            <input type="text" class="form-control" id="nip" name="nip" value="{{ old('nip')}}">
          </div>
          <div class="form-group col-md-4">
            <label for="name">Nama</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name')}}">
          </div>
          <div class="form-group col-md-4">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email')}}">
          </div>
        </div> 
        <div class="form-row">
          <div class="form-group col-md-3">
            <label for="telphone">No.Tlpn</label>
            <input type="text" class="form-control" id="telphone" name="telphone" value="{{old('telphone')}}">
          </div>
          <div class="form-group col-md-3">
            <label for="gender">Gender</label>
            <select class="custom-select" name="gender" id="gender">
              <option value="">-- Pilih gender</option>
              <option value="PRIA">Pria</option>
              <option value="WANITA">Wanita</option>
            </select>
          </div>
          <div class="form-group col-md-3">
            <label for="position">Jabatan</label>
            <select class="custom-select" name="position" id="position">
              <option value="">-- Pilih jabatan</option>
              @foreach ($positions as $ps)
              <option value="{{ $ps->id }}">{{ $ps->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group col-md-3">
            <label for="devision">Bagian</label>
            <select class="custom-select" name="devision" id="devision">
              <option value="">-- Pilih bagian</option>
              @foreach ($devisions as $dv)
              <option value="{{ $dv->id }}">{{ $dv->name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-4">
            <label for="no_kk">No.KK</label>
            <input type="number" class="form-control" id="no_kk" name="no_kk" value="{{old('no_kk')}}">
          </div>
          <div class="form-group col-md-4">
            <label for="no_ktp">No.KTP</label>
            <input type="number" class="form-control" id="no_ktp" name="no_ktp" value="{{old('no_ktp')}}">
          </div>
          <div class="form-group col-md-4">
            <label for="date_joined">Tanggal Bergabung</label>
            <input type="date" class="form-control" id="date_joined" name="date_joined" value="{{old('date_joined')}}">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="example-textarea">Alamat</label>
            <textarea class="form-control" id="example-textarea" rows="5" name="address">{{old('address')}}</textarea>
          </div>
          <div class="form-group col-md-6">
            <label for="profile_photo">Profile Photo</label>
            <div class="custom-file">
              <input type="file" class="custom-file-input" id="profile_photo" name="profile_photo">
              <label class="custom-file-label" for="profile_photo" id="label_photo">Choose file</label>
              <small>*Format file jpg/jpeg,png dengan ukuran max:2MB</small>
            </div>
            <!-- Preview container -->
            <div class="mt-2">
                <img id="preview-image" src="" alt="Preview" style="max-width: 300px;" hidden>
            </div>
          </div>
        </div>
         <hr class="my-4">
         <div class="form-row">
           <div class="col-md-6">
             <div class="custom-control custom-switch mb-2">
               <input type="checkbox" class="custom-control-input" id="accountGenerate" name="accountGenerate">
               <label class="custom-control-label" for="accountGenerate">Generate Account</label>
            </div>
            <small>*Buat akun untuk pengguna dan generate password dari email terdaftar.</small>
           </div>
           <div class="col-md-6 text-right">
             <button type="submit" class="btn btn-primary"><span class="fe fe-16 mr-2 fe-check-circle"></span>Simpan</button>
           </div>
         </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('page_script')
<script>
  document.getElementById('profile_photo').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('preview-image');
    const fileNameDisplay = document.getElementById('label_photo');
    
    // Tampilkan nama file
    fileNameDisplay.textContent = file ? file.name.substr(1, 70) : 'Choose file';

    // Tampilkan preview gambar
    if (file) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.hidden = false;
        }
        
        reader.readAsDataURL(file);
    } else {
        preview.src = '';
        preview.hidden = true;
    }
  });
</script>
@endsection