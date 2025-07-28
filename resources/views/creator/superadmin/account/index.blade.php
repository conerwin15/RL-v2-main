@extends('layouts.app')

@section('content')

<div class="piaggio-alert">
    <div id="changePasswordAlert"></div>
</div>

</main>
<form method="POST" class="mt-0" id="upload_picture_form" enctype="multipart/form-data" >
    @csrf
    <div class="white-wrapper  profile-wrap wrapper2">
        <div class="container-fluid max-width">
            <h5>{{ __('lang.my-profile') }}</h5>
        </div>
        <div class="container-fluid max-width">
            <div class="pic-wrapper">
               
                @php $image = $user->image == '' ? asset('assets/images/avatar_default.png') :  asset('storage' . Config::get('constant.PROFILE_PICTURES') . $user->image);  @endphp
                <img src="{{$image}}" class="profile-pic" id="output">
                   
                    <label for="change-pictrue">
                        <input type="file" name="profile-picture" id="change-pictrue">
                        <i class="fa fa-camera" aria-hidden="true"></i>
                    </label>
            </div>
            <div class="ml-3">
                <p><b>{{ $user->name }} </b> <span class="btn-theme btn-sm ">{{ucfirst( $user->roles[0]->name )}}</span> <br>
                    <small class="text-gray">
                    {{ __('lang.joined-on') }} {{ $user->created_at->format('M Y')}} 
                    </small>
                </p>
            </div>
        </div>
        <div class="" id="imageError" style="color:red;"></div>
        
    </div>
    <div class="wrapper2">
        <div class="container-fluid max-width">
            <div class="view-btn" style="display:none;">
                <button type="submit" class="btn-theme mb-2" id="upload_picture"> {{ __('lang.upload') }}</button>
                <button type="button" class="btn-theme-border mb-2" onClick="window.location.reload  ()">{{ __('lang.cancel') }}</button>
            </div>
        </div>
    </div>
</form>
<div class="wrapper2">
    <div class="container-fluid max-width">
        <p class="color">{{ __('lang.general-information') }}</p>
    </div>
</div>


<div class="white-wrapper wrapper2">
    
    <div class="container-fluid max-width user-info">
    <div class="row mb-5 pt-3">
        <div class="col-sm-6">
            <div class="d-flex">
                <label>{{ __('lang.name') }}:</label>
                <div class="user-label">{{ $user->name }}</div>
            </div>
            <div class="d-flex">
                <label>{{ __('lang.email-address') }}:</label>
                <div class="user-label">{{ $user->email }}</div>
            </div>
        </div>

        <div class="col-sm-6 mb-3">
            <div class="d-flex">
                <label>{{ __('lang.change-password') }}:</label>
                <div class="user-label">********</div>
                <button type="submit" data-toggle="modal" data-backdrop="static" data-keyboard="false"
                    data-target="#changePassword" style="border:0px; color:#3490dc;">
                    {{ __('lang.change') }}
                </button>
            </div>
            <div class="d-flex">
                    <label>{{ __('lang.choose-language') }}:</label>
                    <div class="user-label col-sm-6 mb-3">
                        <select class="select form-control" onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
                            <option value="{{url('/en')}}" {{ session('locale') === 'en' ? 'selected' : ''}}>{{ __('lang.english') }}</option>
                            <option value="{{url('/vi')}}" {{ session('locale') === 'vi' ? 'selected' : ''}}>{{ __('lang.vietnamese') }}</option>
                        </select>
                    </div>
            </div>
        </div>
        </div>
    </div>

</div>
</div>

<div class="modal fade custom-model" id="changePassword" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h5 class="modal-title mb-5 color">{{ __('lang.change-password') }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <!-- form -->

                <form action="{{ url('superadmin/change-password/') }}" method="POST"
                    id="changePasswordForm">
                    @csrf
                    <div class="inputicon2">
                    <svg  width="10"  viewBox="0 0 13.552 26.009"><path d="M21.275,7.775A6.775,6.775,0,1,0,12,14.068v9.889a.257.257,0,0,0,.075.182l2.791,2.791a.257.257,0,0,0,.363,0l2.692-2.692a.257.257,0,0,0,.007-.356l-1.977-2.145a.257.257,0,0,1,.023-.37l1.936-1.64a.257.257,0,0,0,.038-.352L15.822,16.59a.257.257,0,0,1-.053-.156V14.428a6.774,6.774,0,0,0,5.506-6.652Zm-6.775,0a2.033,2.033,0,1,1,2.033-2.033A2.033,2.033,0,0,1,14.5,7.775Z" transform="translate(-7.724 -0.995)" fill="#0097c4"/></svg>
                        <!-- <label>{{ __('lang.current_password') }}: <span
                                class="form-required">*</span></label> -->

                        <input type="password" name="old_password" class="form-control" placeholder="{{ __('lang.old-password') }}"
                            minlength="6" required>
                    </div>
<br>
                    <div class="errorMsg" id="oldPassError"> </div>
                    <div class="inputicon2">
                    <svg  width="10"  viewBox="0 0 13.552 26.009"><path d="M21.275,7.775A6.775,6.775,0,1,0,12,14.068v9.889a.257.257,0,0,0,.075.182l2.791,2.791a.257.257,0,0,0,.363,0l2.692-2.692a.257.257,0,0,0,.007-.356l-1.977-2.145a.257.257,0,0,1,.023-.37l1.936-1.64a.257.257,0,0,0,.038-.352L15.822,16.59a.257.257,0,0,1-.053-.156V14.428a6.774,6.774,0,0,0,5.506-6.652Zm-6.775,0a2.033,2.033,0,1,1,2.033-2.033A2.033,2.033,0,0,1,14.5,7.775Z" transform="translate(-7.724 -0.995)" fill="#0097c4"/></svg>
                     <!-- <label>{{ __('lang.new_password') }}: <span
                                class="form-required">*</span></label> -->
                        <input type="password" name="new_password" class="form-control" placeholder="{{ __('lang.new-password') }}"
                            minlength="6" required>

                    </div> <br>
                        <div class="errorMsg" id="newPassError"> </div>
                       

                        <div class="inputicon2">
                    <svg  width="10"  viewBox="0 0 13.552 26.009"><path d="M21.275,7.775A6.775,6.775,0,1,0,12,14.068v9.889a.257.257,0,0,0,.075.182l2.791,2.791a.257.257,0,0,0,.363,0l2.692-2.692a.257.257,0,0,0,.007-.356l-1.977-2.145a.257.257,0,0,1,.023-.37l1.936-1.64a.257.257,0,0,0,.038-.352L15.822,16.59a.257.257,0,0,1-.053-.156V14.428a6.774,6.774,0,0,0,5.506-6.652Zm-6.775,0a2.033,2.033,0,1,1,2.033-2.033A2.033,2.033,0,0,1,14.5,7.775Z" transform="translate(-7.724 -0.995)" fill="#0097c4"/></svg>
                        <!-- <label>{{ __('lang.confirm_password') }}: <span
                                class="form-required">*</span></label> -->
                        <input type="password" name="confirm_password" class="form-control"
                            placeholder="{{ __('lang.confirm-password') }}" minlength="6" required>
                    </div>
                        <div class="errorMsg" id="confirmPassError"> </div>
<br>
                    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                        <div class="text-center">
                            <button type="button"
                                class="btn-theme changePassword">{{ __('lang.submit') }}</button>
                            <button type="button" class="btn-theme-border" data-dismiss="modal">{{ __('lang.cancel') }}</button>
                        </div>

                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

@section('scripts')

<script>
    $('#change-pictrue').on('change', function (event) {
       
       $('#imageError').html(" ");
       $( ".profile-pic" ).css({ border: "0px" });
       $('#upload_picture').prop( "disabled", false );

       var imageExtensions = ["jpg", "jpeg", "png", "gif"];
       $('.view-btn').show()
       var reader = new FileReader();
       reader.onload = function () {
           var output = document.getElementById('output');
           output.src = reader.result;
       };
       var extension = (event.target.files[0].name.split('.').pop().toLowerCase());
       if (imageExtensions.lastIndexOf(extension) != -1) {
        reader.readAsDataURL(event.target.files[0]);
       } else {
           $( ".profile-pic" ).css({ border: "1px red solid"});
           $('#imageError').html("{{ __('lang.password-updated') }}");
           $('#upload_picture').prop( "disabled", true );
       } 
   })
   
    /********** add change password *********/
    $('.changePassword').on('click', function () {
        var confirmResponse = confirm("Are you sure ?");
        if (confirmResponse == true) {

            $('#oldPassError').html(" ");
            $('#newPassError').html(" ");
            $('#confirmPassError').html(" ");
            var formData = $("#changePasswordForm").serialize();

            var ajaxurl = app_url + "/" + logged_user + "/change-password";

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: formData,
                success: function (data) {
                    if (data.success == true) {

                        $('#changePassword').modal('hide');

                        $('#changePasswordAlert').append(
                            
                                                `<div  class="alert alert-success">
                                                <p><strong>` + "{{ __('lang.password-updated') }}" + `</strong></p>
                                                </div>
                                                `)
                        $('body').find('.piaggio-alert .alert').css('animation',
                            'alertIN  ease-in-out .35s forwards , alertOut  ease-in-out  .35s 4s forwards'
                            )
                        setTimeout(() => {
                            $.ajax({
                                type: 'POST',
                                url: app_url + "/auth/logout",
                                data :{"_token": "{{ csrf_token() }}" },
                                success: function () {
                                    location.reload();
                                }
                            });
                           
                        }, 2000);
                    } else {
                        if (data.messsage != '') {
                            $('#oldPassError').html(data.messsage);
                        }

                        if(typeof data.messsage.old_password !== 'undefined') {
                            $('#oldPassError').html(data.messsage.old_password);
                        }

                        if(typeof data.messsage.new_password !== 'undefined') {
                            $('#newPassError').html(data.messsage.new_password);
                        }

                        if (typeof data.messsage.confirm_password !== 'undefined') {
                            $('#confirmPassError').html(data.messsage.confirm_password[0]);
                        }
                    }
                },
                error: function (reject) {
                    if (reject.status === 422) {
                        var data = $.parseJSON(reject);
                        $("#oldPassError").html(data.errors.name[0]);

                    }
                }
            });
        } else {
            $('#changePassword').modal('hide');
        }
    });

</script>
@endsection
@endsection
