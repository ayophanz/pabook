@extends('layouts.app')

@section('content')
<div class="limiter">
    <form method="POST" action="{{ route('login') }}">
        @csrf 
        <div class="container-login100" style="background-image: url('/loginStyle/images/destinations.svg'); background-size: contain; background-repeat: no-repeat;background-position: center;">
            <div class="wrap-login100 p-t-190 p-b-30">
                <form class="login100-form validate-form">
                    <div class="login100-form-avatar">
                        <img src="{{asset('/images/orig-logo.png')}}" alt="pabook">
                    </div>

                    <span class="login100-form-title p-t-20 p-b-45">
                        Welcome to pabook
                    </span>

                    <div class="wrap-input100 validate-input m-b-10" data-validate = "Username is required">
                        <input class="input100" id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Your email">
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-user"></i>
                        </span>
                    </div>

                    <div class="wrap-input100 validate-input m-b-10" data-validate = "Password is required">
                        <input class="input100" id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Your password">
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-lock"></i>
                        </span>
                    </div>

                    @error('email')
                        <span class="invalid-feedback access-failed" role="alert">
                            <strong>Access denied</strong>
                        </span>
                    @enderror

                    <div class="container-login100-form-btn p-t-10">
                        <button class="login100-form-btn">
                            Login
                        </button>
                    </div>

                    <div class="text-center w-full p-t-25 p-b-30">
                        <a href="{{ route('password.request') }}" class="txt1">
                            Forgot Username / Password?
                        </a>
                    </div>

                    <div class="text-center w-full">
                        <a class="txt1" href="{{ route('register') }}">
                            Create new account
                            <i class="fa fa-long-arrow-right"></i>                      
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </form>
</div>
@endsection
