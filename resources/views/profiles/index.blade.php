@extends('layouts.main')

@section('title')
    <title>Profil - Sistem Informasi Koperasi dan Usaha</title>
@endsection

@section('page_css')

@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="row align-items-center my-4">
                <div class="col">
                    <h2 class="h3 mb-0 page-title">Detail Profil</h2>
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
            @if (session()->has('credentials'))
            <div class="alert alert-danger" role="alert">
                <span class="fe fe-minus-circle fe-16 mr-2"></span> {{ session('credentials') }}
            </div>
            @endif
            @if (session()->has('success'))
            <div class="alert alert-success" role="alert">
                <span class="fe fe-check-circle fe-16 mr-2"></span> {{ session('success') }}
            </div>
            @endif
            <div class="row">
                <div class="col-4">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <img src="{{ $user->member->image?? '' != '' && file_exists(public_path('storage/'.$user->member->image)) 
                            ? asset('storage/'.$user->member->image) 
                            : asset('images/default.png') }}" alt="profile" width="300px">
                        </div>
                    </div>
                </div>
                <div class="col-8">
                    <div class="row">
                        <p class="col-sm-3 text-right">Nama</p>
                        <div class="col-sm-9">
                            <h5>{{ $user->name }}</h5>
                        </div>
                    </div> 
                    <div class="row">
                        <p class="col-sm-3 text-right">Email</p>
                        <div class="col-sm-9">
                            <h5>{{ $user->email }}</h5>
                        </div>
                    </div> 
                    <div class="row">
                        <p class="col-sm-3 text-right">Role</p>
                        <div class="col-sm-9">
                            <h5>{{ $user->roles[0]->name }}</h5>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <form action="{{route('password.change')}}" method="post" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="user_id" value="{{ auth()->id()}}">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="old_pass">Password Lama</label>
                            <input type="password" name="old_pass" class="form-control" id="old_pass">
                        </div>
                        <div class="form-group">
                            <label for="password">Password Baru</label>
                            <input type="password" name="password" class="form-control" id="password">
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" id="password_confirmation">
                        </div>
                        <div class="custom-control custom-checkbox text-left mb-3">
                            <input type="checkbox" class="custom-control-input" id="showPassword">
                            <label class="custom-control-label" for="showPassword">Show Password </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-2">Syarat & Ketentuan</p>
                        <p class="small text-muted mb-2"> Untuk membuat kata sandi baru, Anda harus memenuhi semua persyaratan berikut: </p>
                        <ul class="small text-muted pl-4 mb-0">
                            <li>Minimal 8 karakter </li>
                            <li>Setidaknya satu nomor</li>
                            <li>Tidak boleh sama dengan kata sandi sebelumnya </li>
                        </ul>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Save Change</button>
            </form>
            
        </div>
    </div>
</div>
@endsection

@section('page_script')
<script>
    $(document).ready(function() {
        $('#showPassword').on('click', function () {
            if ($(this).is(':checked')) {
                $('#password_confirmation').attr('type', "text")
                $('#password').attr('type', "text")
                $('#old_pass').attr('type', "text")
            } else {
                $('#password_confirmation').attr('type', "password")
                $('#password').attr('type', "password")
                $('#old_pass').attr('type', "password")
            }
        });
    })
</script>
@endsection