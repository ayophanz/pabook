@extends('layouts.app')

@section('content')
<div class="container-login100" style="opacity: 0.8;height: 500px;background-image: url('/loginStyle/images/setup-wizard.svg'); background-size: contain; background-repeat: no-repeat;background-position: center;">
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="wrap-login100 p-t-10 p-b-30" style="width: 900px;">
            <form class="login100-form validate-form">
                <div class="login100-form-avatar">
                    <img src="{{asset('/storage/images/pabook.png')}}" alt="pabook">
                </div>
                <span class="login100-form-title p-t-20 p-b-45">
                        Register with pabook
                </span>
                
                <div class="wrap-input100 validate-input m-b-10" data-validate = "Username is FullName">
                        <input class="input100" id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Your Fullname">
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fa fa-user"></i>
                        </span>
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>

                <div class="wrap-input100 validate-input m-b-10" data-validate = "Username is Email">
                        <input class="input100" id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Your Email">
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fas fa-at"></i>
                        </span>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>

                <div class="wrap-input100 validate-input m-b-10" data-validate = "Username is Password">
                        <input class="input100" id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Your Password">
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fas fa-key"></i>
                        </span>
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                </div>

                <div class="wrap-input100 validate-input m-b-10" data-validate = "Username is Confirm Password">
                        <input class="input100" id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm Your Password">
                        <span class="focus-input100"></span>
                        <span class="symbol-input100">
                            <i class="fas fa-key"></i>
                        </span>
                </div>

                <div class="container-login100-form-btn p-t-10">
                    <button type="submit" class="login100-form-btn">
                    {{ __('Register') }}
                    </button>
                </div>

                <div class="text-center w-full p-t-25 p-b-30">
                    <a href="{{ route('login') }}" class="txt1">
                        Login
                    </a>
                </div>
            </form>
        </div>
    </form>
</div>
@endsection
