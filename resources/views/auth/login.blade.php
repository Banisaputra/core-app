@extends('auth.layouts.main')

@section('content')
    <div class="wrapper vh-100">
        <div class="row align-items-center h-100">
            <form class="col-lg-3 col-md-4 col-10 mx-auto text-center" method="POST">
                @csrf
                <a class="navbar-brand mx-auto mb-4 mt-2 flex-fill text-center" href="{{ url('/')}}">
                    <img src="{{ asset('images/logo-kokarhardo.png')}}" class="navbar-brand-img" width="200px" alt="logo-company">
                </a>
                <h1 class="h6 mb-3">Sign in</h1>
                <a style="" class="btn mb-3 btn-outline-secondary" href="#" id="modeSwitcher" data-mode="dark"><i class="fe fe-sun fe-16"></i>Thema</a>
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
                <div class="form-group">
                    <label for="inputEmail" class="sr-only">Username</label>
                    <input type="text" id="inputEmail" name="email" class="form-control form-control-lg" tabindex='1' placeholder="Email/NIK" required="" autofocus="">
                </div>
                <div class="custom-control custom-checkbox text-left mb-3">
                    <input type="checkbox" class="custom-control-input" id="showPassword">
                    <label class="custom-control-label" for="showPassword">Show Password </label>
                </div>
                <div class="form-group">
                    <label for="inputPassword" class="sr-only">Password</label>
                    <input type="password" id="inputPassword" name="password" class="form-control form-control-lg" tabindex='2' placeholder="Password" required="">
                </div>
                <hr class="my-4">
                {{-- <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <div class="custom-control custom-checkbox text-left mb-3">
                            <input type="checkbox" class="custom-control-input" id="customControlValidation1" value="remember-me">
                            <label class="custom-control-label" for="customControlValidation1">Stay logged in </label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <p class="text-right"><a class="mb-3" href="#}"> Forgot password?</a></p>
                    </div>
                </div> --}}
            
                <button class="btn btn-lg btn-primary btn-block mb-3" tabindex="3" type="submit">Login</button>
                {{-- <p>Don't have an account? - <a class="mb-3" href="{{ route('register') }}"> Sign Up</a></p> --}}
                <p class="mt-5 mb-3 text-muted">&copy; <script>document.write(new Date().getFullYear('Y'))</script></p>
            </form>             
        </div>
    </div>
@endsection

@section('page_script')
    <script>
        $(document).ready(function() {
            $('#showPassword').on('click', function () {
                if ($(this).is(':checked')) {
                    $('#inputPassword').attr('type', "text")
                } else {
                    $('#inputPassword').attr('type', "password")
                }
            });
        })
    </script>
    
@endsection