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
            <strong>{{ __('lang.reset-password') }}</strong>
            </div>
        </div>

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div class="errorMsg" id="emailError">{{ $error }}</div>
            @endforeach
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" id="email" value="{{ $email ?? old('email') }}" >

            <div class="form-group">
                <div class="inputicon">
                    <svg  width="13.552" height="26.009" viewBox="0 0 13.552 26.009"><path d="M21.275,7.775A6.775,6.775,0,1,0,12,14.068v9.889a.257.257,0,0,0,.075.182l2.791,2.791a.257.257,0,0,0,.363,0l2.692-2.692a.257.257,0,0,0,.007-.356l-1.977-2.145a.257.257,0,0,1,.023-.37l1.936-1.64a.257.257,0,0,0,.038-.352L15.822,16.59a.257.257,0,0,1-.053-.156V14.428a6.774,6.774,0,0,0,5.506-6.652Zm-6.775,0a2.033,2.033,0,1,1,2.033-2.033A2.033,2.033,0,0,1,14.5,7.775Z" transform="translate(-7.724 -0.995)" fill="#979797"/></svg>
                    <input id="password" type="password" placeholder="{{ __('lang.new-password') }}" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>

            <div class="form-group">
                <div class="inputicon">
                    <svg  width="13.552" height="26.009" viewBox="0 0 13.552 26.009"><path d="M21.275,7.775A6.775,6.775,0,1,0,12,14.068v9.889a.257.257,0,0,0,.075.182l2.791,2.791a.257.257,0,0,0,.363,0l2.692-2.692a.257.257,0,0,0,.007-.356l-1.977-2.145a.257.257,0,0,1,.023-.37l1.936-1.64a.257.257,0,0,0,.038-.352L15.822,16.59a.257.257,0,0,1-.053-.156V14.428a6.774,6.774,0,0,0,5.506-6.652Zm-6.775,0a2.033,2.033,0,1,1,2.033-2.033A2.033,2.033,0,0,1,14.5,7.775Z" transform="translate(-7.724 -0.995)" fill="#979797"/></svg>
                    <input id="password-confirm" type="password" placeholder="{{ __('lang.confirm-password') }}" class="form-control" name="password_confirmation" required autocomplete="new-password">
                </div>
            </div>
            <div class="form-group mb-5">
                <button type="submit" class="btn btn-primary">{{ __('lang.reset-password') }}</button>
            </div>
        </form>
    </div>
</div>

@endsection
