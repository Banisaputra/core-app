@extends('auth.layouts.main')

@section('content')
    <div class="wrapper vh-100">
        <div class="row align-items-center h-100">
        <form class="col-lg-3 col-md-4 col-10 mx-auto text-center" method="POST">
            @csrf
            <a class="navbar-brand mx-auto mt-2 flex-fill text-center" href="{{ url('/')}}">
            <svg version="1.1" id="logo" class="navbar-brand-img brand-md" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 120 120" xml:space="preserve">
                <g>
                <polygon class="st0" points="78,105 15,105 24,87 87,87 	" />
                <polygon class="st0" points="96,69 33,69 42,51 105,51 	" />
                <polygon class="st0" points="78,33 15,33 24,15 87,15 	" />
                </g>
            </svg>
            </a>
            <h1 class="h6 mb-3">Sign in</h1>
            <a style="display:none;" href="#" id="modeSwitcher" data-mode="dark"></a>
            @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                @foreach ($errors->all() as $error)
                <span class="fe fe-minus-circle fe-16 mr-2"></span> {{ $error }}
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
                <label for="inputEmail" class="sr-only">Email address</label>
                <input type="email" id="inputEmail" name="email" class="form-control form-control-lg" tabindex='1' placeholder="Email address" required="" autofocus="">
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
            <p>Don't have an account? - <a class="mb-3" href="{{ route('register') }}"> Sign Up</a></p>
            <p class="mt-5 mb-3 text-muted">&copy; <script>document.write(new Date().getFullYear('Y'))</script></p>
        </form>
        </div>
    </div>
@endsection

@section('page_script')
    <script>
        $(document).ready(function() {
            $('#showPassword').on('click', function () {
                console.log('ready bosskuhh');
                if ($(this).is(':checked')) {
                    $('#inputPassword').attr('type', "text")
                } else {
                    $('#inputPassword').attr('type', "password")
                }
            });
        })
    </script>
    
@endsection