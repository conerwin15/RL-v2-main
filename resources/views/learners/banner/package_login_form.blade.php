@extends('layouts.app')

@section('content')
<style>
    body, main{padding: 0 !important;}
</style>

<div class="auth-bg">
    <div class="auth-box">
        <div class="login-header">
            <img src="{{asset('assets/images/logo.png')}}" alt="Piaggio">
        </div>
        <div id="userError" class="errorMsg" style="text-align:center;"></div>
        <form method="POST"  id="customer_login_form">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <div class="form-group">
                <div class="inputicon mb-4">
                    <svg width="18" height="23.333" viewBox="0 0 18 23.333"><g transform="translate(-55.268 -13.799)"><path d="M64.225,221.3c-2.446-.187-4.886-.361-7.325-.572a1.656,1.656,0,0,1-1.631-1.81,13.256,13.256,0,0,1,2.372-7.736,1.937,1.937,0,0,1,1.334-.74c2.464-.149,4.929-.224,7.393-.261.939-.019,1.884.149,2.829.187a2.257,2.257,0,0,1,2.02,1.25,14.089,14.089,0,0,1,2.051,7.065c.006,1.349-.525,1.959-1.847,2.071C69.024,220.943,66.628,221.117,64.225,221.3Z" transform="translate(0 -184.165)" fill="#979797"/><path d="M117.984,24.807a5.5,5.5,0,1,1,5.441-5.491A5.462,5.462,0,0,1,117.984,24.807Z" transform="translate(-53.697 0)" fill="#979797"/></g></svg>
                    <input id="email" type="email" placeholder="{{ __('lang.email-address') }}" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}"  autocomplete="email" autofocus required>
                    <span class="errorMsg" id="emailErrorText" ></span>
                </div>
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
                    <input id="password" type="password" placeholder="{{ __('lang.password') }}" class="form-control @error('password') is-invalid @enderror" name="password"  autocomplete="current-password" required>
                    <span class="errorMsg" id="passwordErrorText" ></span>
                </div>

                @error('password')
                    <span class="form-group has-error" role="alert">
                         <span class="errorMsg" id="passwordError" >{{ $errors->first('password') }}</span>
                    </span>
                    <br>
                @enderror
            </div>
            <div class="form-group">
                <button type="submit" >{{ __('lang.login') }}</button>
            </div>
        </form>
        <div class="text-center mt-3 mb-3"> <a href="{{url('/auth/google/')}}" class=”btn bth-lg-primaty btn-block><strong><img src="https://developers.google.com/identity/images/btn_google_signin_dark_normal_web.png"></strong></a></div>
        <!-- <div class="text-center mt-3 mb-3">Don't have account? <a href="{{url('/customer/register')}}">{{ __('lang.sign-up') }}</a></div> -->
    </div>
</div>
<script type="text/javascript" src="http://localhost/reallylesson/public/js/jquery.js"></script>
<script>
    $(document).ready(function(){
        $('#emailErrorText').html(" ");
        $('#passwordErrorText').html(" ");
        $('#userError').html(" ");
        $('#customer_login_form').on('submit', function (e) {
            e.preventDefault();
            var email = $('#email').val();
            var pass = $('#password').val();
            if(email == "")
            {
                $('#emailErrorText').html("email is required");
            }
            if(pass == "")
            {
                $('#passwordErrorText').html("password is required");
            }
            var formData = new FormData(this);
            var formData = {
                email: email,
                password:pass,
                _token:$('#token').val()
            }
            // reallybot login
            var ajaxurl = "https://v2.reallybot.com/api/users/authenticate";
            $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: JSON.stringify(formData),
                    contentType: "application/json",
                    success: function (response) {
                        console.log(response);
                        if (response.success == true && response.message == 'Login successful.') {
                            // portal login
                            $.ajax({
                                url: app_url + "/" +'auth/login',
                                type:'POST',
                                data:formData,
                                success:function(data){
                                    console.log(data);
                                    window.location= app_url + "/staff/learning-paths";
                                },
                                error: function (data) {
                                    console.log(data);
                                }
                            });
                        } else {
                            $('#userError').html(response.message);
                        }
                    },
                    error: function (reject) {

                    }
            });
        });
    });
</script>
@endsection
