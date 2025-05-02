@extends('auth.layouts.main')

@section('content')
    <div class="wrapper vh-100">
        <div class="row align-items-center h-100">
            <form class="col-lg-6 col-md-8 col-10 mx-auto needs-validation" action="" method="POST">
                @csrf
                <div class="mx-auto text-center my-4">
                    <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="{{ url('/') }}">
                    <svg version="1.1" id="logo" class="navbar-brand-img brand-md" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 120 120" xml:space="preserve">
                        <g>
                            <polygon class="st0" points="78,105 15,105 24,87 87,87"/>
                            <polygon class="st0" points="96,69 33,69 42,51 105,51"/>
                            <polygon class="st0" points="78,33 15,33 24,15 87,15"/>
                        </g>
                    </svg>
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