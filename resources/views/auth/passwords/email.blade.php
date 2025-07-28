@extends('layouts.app')

@section('content')
<style>
    body,main{padding: 0 !important;}
</style>
<div class="auth-bg">
    <div class="auth-box">
        <div class="login-header">
            <img src="{{asset('assets/images/logo.png')}}" alt="Piaggio">
            <div>
            {{ __('Reset Password') }} | <a href="{{ route('login') }}">{{ __('lang.login') }}</a>
            </div>
        </div>
        @if (session('status'))
            <div class="piaggio-alert">
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" style="margin:30px auto 30px auto">
            @csrf
            <div class="inputicon">
                <svg width="18" height="23.333" viewBox="0 0 18 23.333"><g transform="translate(-55.268 -13.799)"><path d="M64.225,221.3c-2.446-.187-4.886-.361-7.325-.572a1.656,1.656,0,0,1-1.631-1.81,13.256,13.256,0,0,1,2.372-7.736,1.937,1.937,0,0,1,1.334-.74c2.464-.149,4.929-.224,7.393-.261.939-.019,1.884.149,2.829.187a2.257,2.257,0,0,1,2.02,1.25,14.089,14.089,0,0,1,2.051,7.065c.006,1.349-.525,1.959-1.847,2.071C69.024,220.943,66.628,221.117,64.225,221.3Z" transform="translate(0 -184.165)" fill="#979797"/><path d="M117.984,24.807a5.5,5.5,0,1,1,5.441-5.491A5.462,5.462,0,0,1,117.984,24.807Z" transform="translate(-53.697 0)" fill="#979797"/></g></svg>
                <!--<label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>-->
                <input id="email" type="email" placeholder="{{ __('E-Mail Address') }}" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                @error('email')
                    <p class="invalid-feedback" role="alert">
                        {{ $message }}
                    </p>
                @enderror
            </div>
            <div class="form-group mt-3">
                <button type="submit"> {{ __('lang.reset-password') }}</button>
            </div>
            <p><em>Reset link will be sent to your Registered Email.</em></p>
        </form>
        
        
    </div>
</div>
@endsection
