@extends('layouts.app')

@section('content')
<style>
    body, main{padding: 0 !important;}
</style>

<div class="piaggio-alert">
    <div id="pathAlert"></div>
</div>

<div class="auth-bg">
    <div class="auth-box">
        <div class="login-header">
            <img src="{{asset('assets/images/logo.png')}}" alt="Piaggio">
        </div>
        <div id="userError" class="errorMsg" style="text-align:center;"></div>

        <form method="POST" id="customer_register_form">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
            <div class="form-group">
                <div class="inputicon mb-4">
                    <svg width="18" height="23.333" viewBox="0 0 18 23.333"><g transform="translate(-55.268 -13.799)"><path d="M64.225,221.3c-2.446-.187-4.886-.361-7.325-.572a1.656,1.656,0,0,1-1.631-1.81,13.256,13.256,0,0,1,2.372-7.736,1.937,1.937,0,0,1,1.334-.74c2.464-.149,4.929-.224,7.393-.261.939-.019,1.884.149,2.829.187a2.257,2.257,0,0,1,2.02,1.25,14.089,14.089,0,0,1,2.051,7.065c.006,1.349-.525,1.959-1.847,2.071C69.024,220.943,66.628,221.117,64.225,221.3Z" transform="translate(0 -184.165)" fill="#979797"/><path d="M117.984,24.807a5.5,5.5,0,1,1,5.441-5.491A5.462,5.462,0,0,1,117.984,24.807Z" transform="translate(-53.697 0)" fill="#979797"/></g></svg>
                    <input type="text"  id="name" name="name" class="form-control" placeholder="{{__('lang.name')}}" value="{{ old('name') }}" required>
                    <span class="errorMsg" id="nameErrorText" ></span>
                </div>
                @error('name')
                    <span class="form-group has-error" role="alert">
                         <span class="errorMsg" id="nameError" >{{ $errors->first('name') }}</span>
                    </span>
                    <br>
                @enderror
            </div>

            <div class="form-group">
                <div class="inputicon mb-4">
                    <svg width="18" height="23.333" viewBox="0 0 18 23.333"><g transform="translate(-55.268 -13.799)"><path d="M64.225,221.3c-2.446-.187-4.886-.361-7.325-.572a1.656,1.656,0,0,1-1.631-1.81,13.256,13.256,0,0,1,2.372-7.736,1.937,1.937,0,0,1,1.334-.74c2.464-.149,4.929-.224,7.393-.261.939-.019,1.884.149,2.829.187a2.257,2.257,0,0,1,2.02,1.25,14.089,14.089,0,0,1,2.051,7.065c.006,1.349-.525,1.959-1.847,2.071C69.024,220.943,66.628,221.117,64.225,221.3Z" transform="translate(0 -184.165)" fill="#979797"/><path d="M117.984,24.807a5.5,5.5,0,1,1,5.441-5.491A5.462,5.462,0,0,1,117.984,24.807Z" transform="translate(-53.697 0)" fill="#979797"/></g></svg>
                    <input type="email" id="email" name="email" class="form-control" placeholder="{{__('lang.email')}}" value="{{ old('email') }}" required>
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
                <div class="inputicon mb-4">
                    <svg width="13.552" height="26.009" viewBox="0 0 13.552 26.009"><path d="M21.275,7.775A6.775,6.775,0,1,0,12,14.068v9.889a.257.257,0,0,0,.075.182l2.791,2.791a.257.257,0,0,0,.363,0l2.692-2.692a.257.257,0,0,0,.007-.356l-1.977-2.145a.257.257,0,0,1,.023-.37l1.936-1.64a.257.257,0,0,0,.038-.352L15.822,16.59a.257.257,0,0,1-.053-.156V14.428a6.774,6.774,0,0,0,5.506-6.652Zm-6.775,0a2.033,2.033,0,1,1,2.033-2.033A2.033,2.033,0,0,1,14.5,7.775Z" transform="translate(-7.724 -0.995)" fill="#979797"/></svg>
                    <input  id="password" type="password" name="password" class="form-control" placeholder="{{__('lang.password')}}" value="{{ old('password') }}" required>
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
                <div class="inputicon mb-4">
                    <svg width="13.552" height="26.009" viewBox="0 0 13.552 26.009"><path d="M21.275,7.775A6.775,6.775,0,1,0,12,14.068v9.889a.257.257,0,0,0,.075.182l2.791,2.791a.257.257,0,0,0,.363,0l2.692-2.692a.257.257,0,0,0,.007-.356l-1.977-2.145a.257.257,0,0,1,.023-.37l1.936-1.64a.257.257,0,0,0,.038-.352L15.822,16.59a.257.257,0,0,1-.053-.156V14.428a6.774,6.774,0,0,0,5.506-6.652Zm-6.775,0a2.033,2.033,0,1,1,2.033-2.033A2.033,2.033,0,0,1,14.5,7.775Z" transform="translate(-7.724 -0.995)" fill="#979797"/></svg>
                    <input  id="confirm-password" type="password" name="confirm-password" class="form-control" placeholder="{{__('lang.confirm-password')}}" value="{{ old('confirm-password') }}" required>
                    <span class="errorMsg" id="confirmPasswordErrorText" ></span>
                </div>
                @error('confirm-password')
                    <span class="form-group has-error" role="alert">
                         <span class="errorMsg" id="confirmPasswordError" >{{ $errors->first('confirm-password') }}</span>
                    </span>
                    <br>
                @enderror
            </div>
            <div class="form-group">
                <button type="submit" >{{ __('lang.sign-up') }}</button>
            </div>
        </form>
        <div class="text-center mt-3 mb-3">Already have account? <a href="{{url('/customer/login')}}">Login </a></div>
    </div>
</div>
<script type="text/javascript" src="http://localhost/reallylesson/public/js/jquery.js"></script>
<script>
    $(document).ready(function(){
        $('#customer_register_form').on('submit', function (e) {
            $('#nameErrorText').html(" ");
            $('#emailErrorText').html(" ");
            $('#passwordErrorText').html(" ");
            $('#confirmPasswordErrorText').html(" ");
            $('#userError').html(" ");
            e.preventDefault();
            if($('#email').val() == "")
            {
                $('#emailErrorText').html("email is required");
            }
            if($('#name').val() == "")
            {
                $('#nameErrorText').html("name is required");
            }
            if($('#password').val() == "")
            {
                $('#passwordErrorText').html("password is required");
            }
            if($('#password').val().length < 8)
            {
                $('#passwordErrorText').html("password must 8 characters long");
                return false;
            }
            if($('#confirm-password').val() == "")
            {
                $('#confirmPasswordErrorText').html("confirm password is required");
                return false;
            }
            if( $('#password').val() != $('#confirm-password').val())
            {
                $('#confirmPasswordErrorText').html("The password and confirm-password must match");
                return false;
            }
            var formData = new FormData(this);
            var formData = {
                name:$('#name').val(),
                email: $('#email').val(),
                password:$('#password').val(),
                confirm_password:$('#confirm-password').val(),
                _token:$('#token').val()
            }

            // reallybot login
            var ajaxurl = "https://v2.reallybot.com/api/users/add-user";

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: JSON.stringify(formData),
                contentType: "application/json",
                success: function (response) {
                    if (response.success == true) {
                        $.ajax({
                            url: app_url + "/" +'customer/register',
                            type:'POST',
                            data:formData,
                            success:function(data){
                                console.log(data);
                                if (data.success == true) {
                                    $('#pathAlert').append(`<div  class="alert alert-success"> User Created successfully.</div> `);
                                    $('body').find('.piaggio-alert .alert').css('animation' , 'alertIN ease-in-out .35s forwards , alertOut ease-in-out .35s 4s forwards')
                                    setTimeout(() => {
                                        window.location = app_url + "/staff/learning-paths";
                                    }, 3000);
                                } else {
                                    $('#emailErrorText').html(data.message.email);
                                }
                            },
                            error: function (data) {
                                $('#nameErrorText').html(" ");
                                $('#emailErrorText').html(" ");
                                $('#passwordErrorText').html(" ");
                                $('#confirmPasswordErrorText').html(" ");
                                $('#userError').html(" ");
                            }
                        });
                    } else {
                        $('#userError').html(response.message);
                    }
                } 
            });
        });
    });
</script>
@endsection
