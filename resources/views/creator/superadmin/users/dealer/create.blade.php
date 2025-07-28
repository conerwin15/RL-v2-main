@extends('layouts.app')


@section('content')

<div class="dash-title container-fluid">
    <div>
        <a  href="{{ url('/superadmin/users') }}"><b>{{__('lang.user-management')}} ></b></a> 
        <span class="bradcrumb">{{__('lang.add-dealer')}}</span>
    </div>
</div>


<form action="{{ url('/superadmin/dealers')}}" method="POST" id='add_user'  class="container-fluid">
    @csrf

    <div class="white-wrapper">
        <div class="row mb-5">
            <div class="col-sm-6">
                <div class="row">

                    <div class="col-sm-6 mb-3">
                        <label>{{__('lang.name')}}: <span class="form-required">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="{{__('lang.name')}}" value="{{ old('name') }}" required>
                        @if($errors->has('name'))
                            <div class="errorMsg" id="nameError">{{ $errors->first('name') }}</div>
                        @endif
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label>{{__('lang.email')}}: <span class="form-required">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="{{__('lang.email')}}" value="{{ old('email') }}" required>
                        @if($errors->has('email'))
                            <div class="errorMsg" id="emailError">{{ $errors->first('email') }}</div>
                        @endif
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label>{{__('lang.password')}}: <span class="form-required">*</span></label>
                        <input type="password" name="password" class="form-control" placeholder="{{__('lang.password')}}" value="{{ old('password') }}" required>
                        @if($errors->has('password'))
                            <div class="errorMsg" id="passwordError">{{ $errors->first('password') }}</div>
                        @endif
                    </div>

                    <div class="col-sm-6 mb-3">
                        <label>{{__('lang.confirm-password')}}: <span class="form-required">*</span></label>
                        <input type="password" name="confirm-password" class="form-control" placeholder="{{__('lang.confirm-password')}}" value="{{ old('confirm-password') }}" required>
                        @if($errors->has('confirm-password'))
                            <div class="errorMsg" id="confirm-passwordError">{{ $errors->first('confirm-password') }}</div>
                        @endif
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label>{{__('lang.select-country')}}: <span class="form-required">*</span></label>
                        <select  name="country_id" id="country" onchange="getRegion(this.value, false)" class="select form-control"  required>
                            <option value="" disabled selected> {{__('lang.select-country')}} </option>
                            @foreach ($countries as $country)
                                <option value="{{$country->id}}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('country_id'))
                            <div class="errorMsg" id="country_idError">{{ $errors->first('country_id') }}</div>
                        @endif
                    </div>

                    <div class="col-sm-6 mb-3">
                        <label>{{__('lang.job-role')}}:</label>
                        <select id="jobrole" name="job_role_id" class="select form-control" >
                            <option value="0">{{__('lang.select-job-role')}}</option> 
                            @foreach ($jobRoles as $jobRole)
                            <option value="{{$jobRole->id}}">{{ $jobRole->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-sm-6 mb-3">
                        <label>{{__('lang.region')}}: <span class="form-required">*</span></label>
                        <select  name="region_id" id="region" onchange="getAdmin()" class="select form-control"  required disabled>
                            <option value="" disabled selected >{{__('lang.select-region')}}</option>
                        </select>
                        @if($errors->has('region_id'))
                            <div class="errorMsg" id="region_idError">{{ $errors->first('region_id') }}</div>
                        @endif
                    </div>

                    <div class="col-sm-6 mb-3">
                        <label>{{__('lang.group')}}:</label>
                        <select id="groups" name="group_id" class="select form-control" >
                            <option value="0">{{__('lang.select-group')}}</option>   
                            @foreach ($groups as $group)
                            <option value="{{$group->id}}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-sm-6 mb-3">
                        <label>{{__('lang.remarks')}}: </label>
                        <input type="text" name="remarks" class="form-control" placeholder="{{__('lang.remarks')}}" value="{{ old('remarks') }}">
                        @if($errors->has('remarks'))
                            <div class="errorMsg" id="remarksError">{{ $errors->first('remarks') }}</div>
                        @endif
                    </div>

                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <button type="submit" class="btn-theme" style="min-width:110px">{{__('lang.submit')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection