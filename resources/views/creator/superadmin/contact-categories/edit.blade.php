@extends('layouts.app')
 
@section('content')

    <div class="dash-title container-fluid">
        <b>{{__('lang.edit-contact-category')}}</b>
        <a class="btn-theme" href="{{ url('superadmin/contact-categories') }}">{{__('lang.back')}}</a>
    </div> 

    <br>
   
    <form action="{{ url('superadmin/contact-categories/' . $contactCategory->id) }}" method="POST" enctype="multipart/form-data">
    	@csrf
        @method('PUT')
        <div class="row">
            <input type="hidden" name="id" value="{{$contactCategory->id}}">

            <div class="col-xs-4 col-sm-4 col-md-4">
		        <div class="form-group">
		            <strong>{{__('lang.role')}}:</strong>
                    <div>
                      <select  name="role" class="form-control select" required>
                        <option value="-1"> {{__('lang.all')}} </option>
                          @foreach ($roles as $role)
                         <option value="{{$role->id}}" id="roleId" {{($role->id == $contactCategory->role_id) ? 'selected' : '' }}>{{ ucfirst(toRoleLabel($role->name)) }}</option>
                         @endforeach
                      </select>
                      </div>
		        </div>
		    </div>
            
		    <div class="col-xs-12 col-sm-12 col-md-12">
		        <div class="form-group">
		            <strong>{{__('lang.category-name')}}:</strong>
		            <input type="text" name="name" class="form-control" placeholder="{{__('lang.category-name')}}" value="{{ $contactCategory->category_name }}" required>
                     @if($errors->has('name'))
                        <div class="errorMsg" id="nameError">{{ $errors->first('name') }}</div>
                    @endif
		        </div>
		    </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
		        <div class="form-group">
		            <strong>{{__('lang.email-address')}}:</strong>
		            <input type="email" name="email" class="form-control" placeholder="{{__('lang.email')}}" value="{{ $contactCategory->email }}" required>
                     @if($errors->has('email'))
                        <div class="errorMsg" id="emailError">{{ $errors->first('email') }}</div>
                    @endif
		        </div>
		    </div>

		    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
		            <button type="submit" class="btn-theme ml-2">{{__('lang.submit')}}</button>
		    </div>
		</div>
  </form>
@endsection
