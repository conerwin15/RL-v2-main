@extends('layouts.app')

@section('content')
<div class="dash-title container-fluid">
    <div>
        <a  href="{{ url('/superadmin/users') }}"><b>{{__('lang.dealer')}} ></b></a> 
        <span class="bradcrumb">{{__('lang.edit-dealer')}}</span>
    </div>
</div>


<form action="{{  url('/superadmin/dealers/' . $user->id) }}" method="POST" id='add_user' class="container-fluid">
    @csrf
    @method('PUT')


    <input type="hidden" name="id" value="{{$user->id}}">
    <div class="white-wrapper">
        <div class="row mb-5">
            <div class="col-sm-6">
                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label>{{__('lang.name')}}: <span class="form-required">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="{{__('lang.name')}}" value="{{ $user->name }}" required>
                        @if($errors->has('name'))
                                <div class="errorMsg" id="nameError">{{ $errors->first('name') }}</div>
                        @endif
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label>{{__('lang.email')}}:<span class="form-required">*</span></label>
                        <input type="email" name="email" class="form-control" placeholder="{{__('lang.email')}}" value="{{ $user->email }}" required>
                        @if($errors->has('email'))
                            <div class="errorMsg" id="emailError">{{ $errors->first('email') }}</div>
                        @endif
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label>{{__('lang.select-country')}}: <span class="form-required">*</span></label>
                        <select  name="country_id" id="country" class="form-control select" onchange="getRegion(this.value, false)"  required>
                            <option value="" disabled selected> {{__('lang.select-country')}} </option>
                            @foreach ($countries as $country)
                                <option value="{{$country->id}}" {{$user->country_id == $country->id ? "selected" : ''}}>{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-sm-6 mb-3">
                        <label>{{__('lang.job-role')}}:</label>
                        <select id="jobrole" name="job_role_id" class="form-control select" >
                            <option value="0">{{__('lang.select-job-role')}}</option> 
                            @foreach ($jobRoles as $jobRole)
                                <option value="{{$jobRole->id}}" {{$user->job_role_id == $jobRole->id ? "selected" : ""}}>{{ $jobRole->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <input type="hidden" value="{{$user->region_id}}" id="selectregion">
                    <div class="col-sm-6 mb-3">
                        <label>{{__('lang.region')}}: <span class="form-required">*</span></label>
                        <select  name="region_id" id="region" class="form-control select"   onchange="getAdmin()" required>
                            <option value="" disabled selected>{{__('lang.select-region')}}</option>
                            @foreach ($regions as $region)
                                <option value="{{$user->region_id}}" {{$user->region_id == $region->id ? "selected" : ''}}>{{ $region->name }}</option>
                            @endforeach
                        </select>
                    </div>   

                    <div class="col-sm-6 mb-3">
                        <label>{{__('lang.group')}}:</label>
                        <select id="group" name="group_id" class="form-control select" >
                            <option value="0">{{__('lang.select-group')}}</option>     
                            @foreach ($groups as $group)
                                <option value="{{$group->id}}" {{$user->group_id == $group->id ? "selected" : ""}}>{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-sm-6 mb-3">
                        <label>{{__('lang.remarks')}}: </label>
                        <input type="text" name="remarks" class="form-control" placeholder="{{__('lang.remarks')}}" value="{{ $user->remarks }}">
                        @if($errors->has('remarks'))
                            <div class="errorMsg" id="remarksError">{{ $errors->first('remarks') }}</div>
                        @endif
                    </div>

                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <button type="submit" class="btn-theme">{{__('lang.submit')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


@endsection