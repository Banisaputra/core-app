@extends('layouts.main')

@section('title')
    <title>Edit Anggota - Sistem Informasi Koperasi dan Usaha</title>
@endsection
@section('page_css')
    
@endsection
@section('content')
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-12 col-xl-10">
      <div class="row align-items-center my-4">
        <div class="col">
          <h2 class="h3 mb-0 page-title">Perubahan Anggota</h2>
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
       
      <form action={{ route('members.update', $member->id) }} method="POST" id="form-member" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="name">Nama</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $member->name ?? '') }}">
          </div>
          <div class="form-group col-md-6">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $member->user->email ?? '') }}">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-4">
            <label for="date_of_birth">Tanggal Lahir</label>
            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $member->date_of_birth ?? '') }}">
          </div>
          <div class="form-group col-md-4">
            <label for="nip">NIP</label>
            <input type="text" class="form-control" id="nip" name="nip" value="{{ old('nip', $member->nip ?? '')}}">
          </div>
          <div class="form-group col-md-4">
            <label for="employment">Pekerjaan</label>
            <input type="text" class="form-control" id="employment" name="employment" value="{{ old('employment', $member->employment ?? '')}}">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-4">
            <label for="telphone">No.Tlpn</label>
            <input type="text" class="form-control" id="telphone" name="telphone" value="{{old('telphone', $member->telphone ?? '')}}">
          </div>
          <div class="form-group col-md-4">
            <label for="gender">Gender</label>
            <select class="custom-select" name="gender" id="gender">
              <option value="">-- Pilih gender</option>
              <option value="male" {{ old('gender', $member->gender ?? '') == 'male' ? 'selected' : '' }}>Pria</option>
              <option value="female" {{ old('gender', $member->gender ?? '') == 'female' ? 'selected' : '' }}>Wanita</option>
            </select>
          </div>
          <div class="form-group col-md-4">
            <label for="religion">Agama</label>
            <select class="custom-select" name="religion" id="religion">
              <option value="">-- Pilih agama</option>
              <option value="Islam" {{ old('religion', $member->religion ?? '') == 'Islam' ? 'selected' : '' }}>Islam</option>
              <option value="Kristen" {{ old('religion', $member->religion ?? '') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
              <option value="Katholik" {{ old('religion', $member->religion ?? '') == 'Katholik' ? 'selected' : '' }}>Katholik</option>
              <option value="Hindu" {{ old('religion', $member->religion ?? '') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
              <option value="Budha" {{ old('religion', $member->religion ?? '') == 'Budha' ? 'selected' : '' }}>Budha</option>
            </select>
          </div>
        </div>
        <div class="form-row"> 
          <div class="form-group col-md-4">
            <label for="balance">Saldo Awal</label>
            <input type="number" class="form-control" id="balance" name="balance" value="{{ old('balance', ($member->balance*1) ?? '')}}">
          </div>
          <div class="form-group col-md-4">
            <label for="date_joined">Tanggal Bergabung</label>
            <input type="date" class="form-control" id="date_joined" name="date_joined" value="{{old('date_joined', $member->date_joined ?? '')}}">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="example-textarea">Alamat</label>
            <textarea class="form-control" id="example-textarea" rows="5" name="address">{{old('address', $member->address ?? '')}}</textarea>
          </div>
          <div class="form-group col-md-6">
            <label for="profile_photo">Profile Photo</label>
            <div class="custom-file">
              <input type="file" class="custom-file-input" id="profile_photo" name="profile_photo">
              <label class="custom-file-label" for="profile_photo">Choose file</label>
            </div>
            <div class="text-center mt-4">
                <img src="{{ asset('storage/'. $member->image)}}" alt="..." width="300px">
            </div>
          </div>
        </div>
         <hr class="my-4">
         <div class="form-row">
           <div class="col-md-6">
             {{-- update note --}}
           </div>
           <div class="col-md-6 text-right">
             <button type="submit" class="btn btn-primary"><span class="fe fe-16 mr-2 fe-check-circle"></span>Update</button>
           </div>
         </div>
      </form>
    </div>
  </div>
</div>
@endsection