@extends('layouts.main')

@section('page_css')

@endsection

@section('content')
{{-- edit account --}}
<div class="modal fade" id="accountModal" tabindex="-1" role="dialog" aria-labelledby="accountModalTitle" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="accountModalTitle">Edit Akun Pengguna</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">x</span>
                </button>
            </div>
            <form action="{{ route('members.account')}}" method="POST">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" name="member_id" value="{{ $member->id }}">
                    <div class="form-group">
                        <label for="email" class="col-form-label">Email</label>
                        <input type="text" name="email" value="{{ $member->user['email'] }}" class="form-control" id="email" readonly>
                    </div>
                    <div class="form-group">
                        <label for="role" class="col-form-label">Role</label>
                        <select class="custom-select" name="role" id="role">
                            <option value="">-- Pilih Role</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id}}">{{ $role->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="password" class="col-form-label">Password</label>
                        <input type="password" class="form-control" name="password">
                    </div>
                    <div class="custom-control custom-switch mb-2">
                        <input type="checkbox" class="custom-control-input" id="accountActive" name="accountActive">
                        <label class="custom-control-label" for="accountActive">Aktifasi Account</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn mb-2 btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn mb-2 btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="row align-items-center my-4">
                <div class="col">
                    <h2 class="h3 mb-0 page-title">Detail Anggota</h2>
                </div>
            </div>
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
            <hr class="my-4">
            <h5 class="mb-2 mt-4">Personal</h5>
            <div class="row">
                <div class="col-4">
                    <div class="card shadow mb-4">
                        <div class="card-body text-center">
                            <img src="{{ file_exists(asset('storage/'.$member->image)) 
                            ? asset('storage/'.$member->image) 
                            : asset('images/default.png') }}" alt="profile" width="300px">
                        </div>
                    </div>
                </div>
                <div class="col-8">
                    <div class="row">
                        <p for="nip" class="col-sm-3 text-right">NIP</p>
                        <div class="col-sm-9">
                            <h5>{{ $member->nip }}</h5>
                        </div>
                    </div>
                    <div class="row">
                        <p class="col-sm-3 text-right">Nama</p>
                        <div class="col-sm-9">
                            <h5>{{ ucwords($member->name) }}</h5>
                        </div>
                    </div>
                    <div class="row">
                        <p class="col-sm-3 text-right">Email</p>
                        <div class="col-sm-9">
                            <h5>{{ $member->user['email'] }}</h5>
                        </div>
                    </div>
                    <div class="row">
                        <p class="col-sm-3 text-right">No.Tlpn</p>
                        <div class="col-sm-9">
                            <h5>{{ $member->telphone }}</h5>
                        </div>
                    </div>
                    <div class="row">
                        <p class="col-sm-3 text-right">Alamat</p>
                        <div class="col-sm-9">
                            <h5>{{ $member->address }}</h5>
                        </div>
                    </div>
                    <div class="row">
                        <p class="col-sm-3 text-right">Tanggal Bergabung</p>
                        <div class="col-sm-9">
                            <h5>{{ date('d - M - Y', strtotime($member->date_joined)) }}</h5>
                        </div>
                    </div>
                    <div class="row">
                        <p class="col-sm-3 text-right">Saldo</p>
                        <div class="col-sm-9">
                            <h5>Rp{{ number_format($member->balance, 0) }}</h5>
                        </div>
                    </div>
                    <div class="row">
                        <p class="col-sm-3 text-right">Status</p>
                        <div class="col-sm-9">
                            <h5>{!! $member->is_transactional == 1 
                            ? "<span class='dot dot-lg bg-success mr-1'></span>Aktif" 
                            : "<span class='dot dot-lg bg-secondary mr-1'></span>Tidak Aktif" !!}</h5>
                        </div>
                    </div>
                </div> 
            </div>
            <hr class="my-4">
            <h5 class="mb-2 mt-4">Account</h5>
            <button type="button" class="btn mb-2 btn-outline-success" data-toggle="modal" data-target="#accountModal">Edit Account</button>
            <div class="card shadow mb-4">
                <div class="card-header">
                    <strong class="card-title">Informasi terkait akun pengguna</strong>
                    <span class="float-right"><span class="badge badge-pill badge-{{ $member->user->is_transactional == 1 ? "success" : "danger"}} text-white">{{ $member->user->is_transactional == 1 ? "Active" : "Nonactive"}}</span></span>
                </div>
                <div class="card-body">
                    <dl class="row align-items-center mb-0">
                        <dt class="col-sm-2 mb-3 text-muted">Email</dt>
                        <dd class="col-sm-4 mb-3">
                            <strong>{{ $member->user->email}}</strong>
                        </dd>
                        <dt class="col-sm-2 mb-3 text-muted">Nama Pengguna</dt>
                        <dd class="col-sm-4 mb-3">
                            <strong>{{ $member->user->name }}</strong>
                        </dd>
                    </dl>
                    <dl class="row mb-0">
                        <dt class="col-sm-2 mb-3 text-muted">Created On</dt>
                        <dd class="col-sm-4 mb-3">{{ $member->user->created_at}}</dd>
                        <dt class="col-sm-2 mb-3 text-muted">Last Update</dt>
                        <dd class="col-sm-4 mb-3">{{ $member->user->updated_at}}</dd>
                        <dt class="col-sm-2 text-muted">Role</dt>
                        <dd class="col-sm-10">
                            @foreach ($userRole as $role)
                                <li>{{ $role->name }}</li>
                            @endforeach
                        </dd>
                    </dl>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('page_script')
 
@endsection