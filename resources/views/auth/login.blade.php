@extends('layouts.authentication.master')
@section('title', 'Login Peer Observation')

@section('css')
@endsection

@section('style')
@endsection

@section('content')
<div class="container-fluid p-0">
    <div class="row m-0">
        <div class="col-12 p-0">
            <div class="login-card">
                <div>
                    <div><a class="logo" href="{{ route('home') }}">
                            <img class="img-fluid" style="max-width: 160px;" src="{{asset('assets/images/logo.png')}}"
                                alt="looginpage"></a>
                    </div>
                    <div class="login-main">
                        <form class="theme-form" method="POST" action="{{ route('login') }}">
                            @csrf
                            <h4>Login to your account</h4>
                            <div class="form-group">
                                <label class="col-form-label">{{ __('Email or username') }}</label>
                                <input id="username" type="text"
                                    class="form-control @error('username') is-invalid @enderror" name="username"
                                    value="{{ old('username') }}" required autofocus autocomplete="off">

                                @error('username')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label class="col-form-label">Password</label>
                                <div class="form-input position-relative">
                                    <input class="form-control" type="password" name="password" required="">
                                    <div class="show-hide"><span class="show"> </span></div>
                                </div>
                            </div>

                            <div class="form-group mb-3 mt-4">
                                <div class="checkbox p-0">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                        {{ old('remember') ? 'checked' : '' }}>
                                    <label class="text-muted" for="remember">{{ __('Remember Me') }}</label>
                                </div>
                                @if (Route::has('password.request'))
                                <a class="link" href="{{ route('password.request') }}">
                                    {{ __('Forgot Password?') }}
                                </a>
                                @endif
                            </div>
                            <div class="text-end mt-3">
                                <button class="btn btn-primary btn-block w-100" type="submit">Login</button>
                            </div>
                        </form>
                        <h6 class="text-muted mt-4 or">Or login with</h6>
                        <div class="social mt-4">
                            <div class="btn-showcase">
                                <button class="btn btn-light btn-block w-100" onclick="Klas2Login()">
                                    <img style="max-width: 20px;" src="{{asset('assets/images/logo/logo-icon.png')}}">
                                    SSO Klas2</button></div>
                        </div>
                        <!-- <p class="mt-4 mb-0 text-center">Don't have account?<a class="ms-2"
                                    href="{{ route('register') }}">Create Account</a></p> -->

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@php
    $callback_url = route('sso_klas2');
    $token = md5($callback_url.gmdate('Y/m/d'));
    $url = "http://klas2.jgu.ac.id/sso/";
    //$url = "http://localhost/JGU/sso/test.php"; //for test only
    $link = $url."?login_to=".route('login')."&login_name=Peer Observation&callback_url=$callback_url&token=$token&ip=".$_SERVER['REMOTE_ADDR'];
@endphp
@section('script')
<script>
    function Klas2Login() {
        window.open("{!!$link!!}", "LOGIN SSO JGU","location=no, titlebar=no, toolbar=no, fullscreen=yes, resizable=no, scrollbars=yes");
    }
</script>
@endsection
