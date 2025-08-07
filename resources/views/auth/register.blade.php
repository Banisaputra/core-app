@extends('auth.layouts.main')

@section('content')
    <div class="wrapper vh-100">
        <div class="row align-items-center h-100">
            <form class="col-lg-6 col-md-8 col-10 mx-auto needs-validation" action="" method="POST">
                @csrf
                <div class="mx-auto text-center my-4">
                    <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="{{ url('/') }}">
                        <img src="{{ asset('images/logo-kokarhardo.png')}}" class="navbar-brand-img" width="200px" alt="logo-company">
                    </a>
                    <h2 class="my-3">Register</h2>
                </div>

                @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    @foreach ($errors->all() as $error)
                    <span class="fe fe-minus-circle fe-16 mr-2"></span> {{ $error }}
                    <br>
                    @endforeach
                </div>
                    
                @endif
                <div class="form-row">
                    <div class="col-md-6 mb-3">
                      <label for="full-name">Nama Lengkap</label>
                      <input class="form-control" id="full-name" name="name" value="{{ old('name')}}" required="">
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="inpEmail">Email address</label>
                      <input type="email" class="form-control" id="inpEmail" name="email" aria-describedby="emailHelp" value="{{ old('email')}}" required="">
                    </div>
                </div>
                <div class="form-group mb-3">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control">
                    <span class="help-block"><small>Minimal 8 karakter</small></span>
                </div>
                <div class="form-group mb-3">
                    <label for="password_confirm">Konfirmasi Password</label>
                    <input type="password" id="password_confirm" name="password_confirmation" class="form-control">
                </div>
                
                <button class="btn btn-lg btn-primary btn-block" type="submit">Sign up</button>
                <p class="text-center mt-3 mb-3">Have an account? - <a href="{{ route('login') }}">Sign in</a></p>
                <p class="mt-5 mb-3 text-muted text-center">&copy; <script>document.write(new Date().getFullYear('Y'))</script></p>
            </form>
        </div>
    </div>
@endsection