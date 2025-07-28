<div class="modal fade custom-model show" id="changePasswordModal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h5 class="modal-title  mb-2 color">{{ __('lang.change-password') }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <!-- form -->
                <form action="" method="POST" id="changePasswordForm" class="mt-5">
                    @csrf
                    @method('PUT')
                    <div class="inputicon2">
                        <svg width="10" viewBox="0 0 13.552 26.009">
                            <path
                                d="M21.275,7.775A6.775,6.775,0,1,0,12,14.068v9.889a.257.257,0,0,0,.075.182l2.791,2.791a.257.257,0,0,0,.363,0l2.692-2.692a.257.257,0,0,0,.007-.356l-1.977-2.145a.257.257,0,0,1,.023-.37l1.936-1.64a.257.257,0,0,0,.038-.352L15.822,16.59a.257.257,0,0,1-.053-.156V14.428a6.774,6.774,0,0,0,5.506-6.652Zm-6.775,0a2.033,2.033,0,1,1,2.033-2.033A2.033,2.033,0,0,1,14.5,7.775Z"
                                transform="translate(-7.724 -0.995)" fill="#0097c4" /></svg>
                        <input type="password" id="new_password" name="new_password" class="form-control" placeholder="{{ __('lang.new-password') }}"
                            minlength="6" required>
                    </div>
                    <div class="errorMsg" id="newPassError"> </div>
                        <br>

                    <div class="inputicon2">
                        <svg width="10" viewBox="0 0 13.552 26.009">
                            <path
                                d="M21.275,7.775A6.775,6.775,0,1,0,12,14.068v9.889a.257.257,0,0,0,.075.182l2.791,2.791a.257.257,0,0,0,.363,0l2.692-2.692a.257.257,0,0,0,.007-.356l-1.977-2.145a.257.257,0,0,1,.023-.37l1.936-1.64a.257.257,0,0,0,.038-.352L15.822,16.59a.257.257,0,0,1-.053-.156V14.428a6.774,6.774,0,0,0,5.506-6.652Zm-6.775,0a2.033,2.033,0,1,1,2.033-2.033A2.033,2.033,0,0,1,14.5,7.775Z"
                                transform="translate(-7.724 -0.995)" fill="#0097c4" /></svg>
                        <input type="password" id="confirm_password"  name="confirm_password" class="form-control"
                            placeholder="{{ __('lang.confirm-password') }}" minlength="6" required>  
                    </div>
                    <div class="errorMsg" id="confirmPassError"> </div> <br>
                    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                        <div class="text-center">
                            <button type="button"
                                class="btn-theme changePasswordUser">{{ __('lang.submit') }}</button>
                            <button type="button" class="btn-theme-border" data-dismiss="modal">{{ __('lang.cancel') }}</button>
                        </div>

                    </div>
                </form>
            </div>

        </div>
    </div>
</div>