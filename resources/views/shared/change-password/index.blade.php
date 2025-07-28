
@extends('layouts.app')

@section('content')

<div class="dash-title container-fluid">
    <b>{{__('lang.change-password')}}</b>
</div>

    <div class="white-wrapper">
       
        <form action="{{ url($routeSlug . '/change-password/')}}" method="POST" >
          @csrf
            <div class="col-sm-4 mb-3">
                <label>{{__('lang.current_password')}}: <span class="form-required">*</span></label>
                <input type="password" name="current_password" class="form-control" placeholder="{{ __('lang.old-password') }}" minlength="6" required>
                @if($errors->has('current_password'))
                    <div class="errorMsg" id="oldPassError">{{ $errors->first('current_password') }}</div>
                @endif
            </div>

            <div class="col-sm-4 mb-4">
                <label>{{__('lang.new_password')}}: <span class="form-required">*</span></label>
                <input type="password" name="new_password" class="form-control" placeholder="New password" minlength="6" required>
                @error('new_password')
                <span class="form-group has-error" role="alert">
                        <span class="errorMsg" id="newPassError" >{{ $errors->first('new_password') }}</span>
                </span>
                <br>
            @enderror
            </div>
           

             <div class="col-sm-4 mb-4">
                <label>{{__('lang.confirm_password')}}: <span class="form-required">*</span></label>
                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" minlength="6" required>
                @error('confirm_password')
                <span class="form-group has-error" role="alert">
                        <span class="errorMsg" id="confirmPassError" >{{ $errors->first('confirm_password') }}</span>
                </span>
                <br>
            @enderror
            </div>
           
    
        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
         <button type="submit" class="btn btn-primary">{{__('lang.submit')}}</button>
        </div>
    </form>
        </div>
    </div>

@endsection