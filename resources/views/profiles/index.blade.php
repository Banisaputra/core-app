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
            <div class="row">
                <div class="col-4">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <img src="{{ $user->member->image != '' && file_exists(public_path('storage/'.$user->member->image)) 
                            ? asset('storage/'.$user->member->image) 
                            : asset('images/default.png') }}" alt="profile" width="300px">
                        </div>
                    </div>
                </div>
                <div class="col-8">
                    <div class="row">
                        <p for="nip" class="col-sm-3 text-right">Nama</p>
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
            
        </div>
    </div>
</div>
@endsection

@section('page_script')
  
@endsection