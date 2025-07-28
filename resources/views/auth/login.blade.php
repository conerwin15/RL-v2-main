@extends('layouts.app')

@section('content')
<style>
    body, main{padding: 0 !important;}
</style>
<div class="auth-bg">
    <div class="auth-box">
        <div class="login-header">
            <img src="{{asset('assets/images/logo.png')}}" alt="Piaggio">
            <div>
                <span>{{ __('lang.choose-language') }}:</span>
                <select onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
                    <option value="{{url('/en')}}" {{ session('locale') === 'en' ? 'selected' : ''}}>{{ __('lang.english') }}</option>
                    <option value="{{url('/vi')}}" {{ session('locale') === 'vi' ? 'selected' : ''}}>{{ __('lang.vietnamese') }}</option>
                </select>
            </div>
        </div>
        <form method="POST" action="{{ route('login') }}" id="login_form">
            @csrf

            <div class="form-group">
                <div class="inputicon mb-4">
                    <svg width="18" height="23.333" viewBox="0 0 18 23.333"><g transform="translate(-55.268 -13.799)"><path d="M64.225,221.3c-2.446-.187-4.886-.361-7.325-.572a1.656,1.656,0,0,1-1.631-1.81,13.256,13.256,0,0,1,2.372-7.736,1.937,1.937,0,0,1,1.334-.74c2.464-.149,4.929-.224,7.393-.261.939-.019,1.884.149,2.829.187a2.257,2.257,0,0,1,2.02,1.25,14.089,14.089,0,0,1,2.051,7.065c.006,1.349-.525,1.959-1.847,2.071C69.024,220.943,66.628,221.117,64.225,221.3Z" transform="translate(0 -184.165)" fill="#979797"/><path d="M117.984,24.807a5.5,5.5,0,1,1,5.441-5.491A5.462,5.462,0,0,1,117.984,24.807Z" transform="translate(-53.697 0)" fill="#979797"/></g></svg>
                    <input id="email" type="email" placeholder="{{ __('lang.email-address') }}" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}"  autocomplete="email" autofocus>
                  
                    @error('email')
                    <span class="form-group has-error" role="alert">
                         <span class="errorMsg" id="emailError" >{{ $errors->first('email') }}</span>
                    </span>
                    <br>
                @enderror

            </div>

            <div class="form-group">
                <div class="inputicon">
                    <svg width="13.552" height="26.009" viewBox="0 0 13.552 26.009"><path d="M21.275,7.775A6.775,6.775,0,1,0,12,14.068v9.889a.257.257,0,0,0,.075.182l2.791,2.791a.257.257,0,0,0,.363,0l2.692-2.692a.257.257,0,0,0,.007-.356l-1.977-2.145a.257.257,0,0,1,.023-.37l1.936-1.64a.257.257,0,0,0,.038-.352L15.822,16.59a.257.257,0,0,1-.053-.156V14.428a6.774,6.774,0,0,0,5.506-6.652Zm-6.775,0a2.033,2.033,0,1,1,2.033-2.033A2.033,2.033,0,0,1,14.5,7.775Z" transform="translate(-7.724 -0.995)" fill="#979797"/></svg>
                    <input id="password" type="password" placeholder="{{ __('lang.password') }}" class="form-control @error('password') is-invalid @enderror" name="password"  autocomplete="current-password">

                    @error('password')
                    <span class="form-group has-error" role="alert">
                         <span class="errorMsg" id="passwordError" >{{ $errors->first('password') }}</span>
                    </span>
                    <br>
                @enderror

                </div>

            </div>
            
            <!--
            <div class="form-group ">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                    <label class="form-check-label" for="remember">
                        {{ __('Remember Me') }}
                    </label>
                </div>
            </div>
            -->
            
            <div class="form-group">
                <button type="submit" >
                    {{ __('lang.login') }}
                </button>

                @if (Route::has('password.request'))
                    <p class="text-center mt-3 mb-3">
                        <!-- <a class="btn btn-link" href="{{ route('password.request') }}"> -->
                        <a class="btn-link" href="#forgot-password"  data-toggle="modal">
                            {{ __('lang.forgot-password') }}
                        </a>
                    </p>
                @endif
            </div>
            <div class="text-center mt-3 mb-3"> <a href="{{url('/auth/google/')}}" class=”btn bth-lg-primaty btn-block><strong><img src="https://developers.google.com/identity/images/btn_google_signin_dark_normal_web.png"></strong></a></div>
        </form>
        
    </div>
</div>


	<!-- Modal -->
    <div class="modal fade custom-model" id="forgot-password" role="dialog">
      	<div class="modal-dialog">
          	<div class="modal-content">
              	<div class="modal-body">
					<h5 class="modal-title mb-2 color"> {{ __('lang.reset-password') }}</h5>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
                    @if (session('status'))
            <div class="piaggio-alert">
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" style="max-width:350px;margin:30px auto 30px auto">
            @csrf
            <p class="text-center">{{ __('lang.reset-password-text') }}</p>
            <div class="inputicon">
                <svg width="18" height="23.333" viewBox="0 0 18 23.333"><g transform="translate(-55.268 -13.799)"><path d="M64.225,221.3c-2.446-.187-4.886-.361-7.325-.572a1.656,1.656,0,0,1-1.631-1.81,13.256,13.256,0,0,1,2.372-7.736,1.937,1.937,0,0,1,1.334-.74c2.464-.149,4.929-.224,7.393-.261.939-.019,1.884.149,2.829.187a2.257,2.257,0,0,1,2.02,1.25,14.089,14.089,0,0,1,2.051,7.065c.006,1.349-.525,1.959-1.847,2.071C69.024,220.943,66.628,221.117,64.225,221.3Z" transform="translate(0 -184.165)" fill="#979797"/><path d="M117.984,24.807a5.5,5.5,0,1,1,5.441-5.491A5.462,5.462,0,0,1,117.984,24.807Z" transform="translate(-53.697 0)" fill="#979797"/></g></svg>
                <!--<label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>-->
                <input id="email" type="email" placeholder="{{ __('lang.e-mail-address') }}" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                @error('email')
                    <p class="invalid-feedback" role="alert">
                        {{ $message }}
                    </p>
                @enderror

            </div>
          
            <div class="form-group mt-3 text-center">
                <button type="submit" class="btn-theme"> {{ __('lang.reset-password') }}</button>
                <button type="reset" class="btn-theme"> {{ __('lang.cancel') }}</button>
            </div>
            <p><em>{{ __('lang.reset-link-text') }}</em></p>
        </form>
          
                  	
          		</div>

      		</div>
  		</div>
	</div>


@endsection
