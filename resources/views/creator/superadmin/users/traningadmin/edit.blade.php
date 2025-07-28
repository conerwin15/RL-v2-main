@extends('layouts.app')

@section('content')

<style>
    .menu-option {
        padding: 6px 20px 6px;
    }
</style>  

<div class="dash-title container-fluid">
    <div>
        <a  href="{{ url('/superadmin/users') }}"><b>{{__('lang.admin')}} ></b></a> 
        <span class="bradcrumb">{{__('lang.edit-admin')}}</span>
    </div>
</div>


<form action="{{  url('/superadmin/trainingadmins/' . $user->id) }}" method="POST" id='add_user' class="container-fluid">
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
                        <select  name="country_id" id="country" class="select form-control" onchange="getRegionAdmin(this.value, false)"  required>
                            <option value="" disabled selected> {{__('lang.select-country')}} </option>
                            @foreach ($countries as $country)
                                <option value="{{$country->id}}" {{$user->country_id == $country->id ? "selected" : ''}}>{{ $country->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-sm-6 mb-3">
                        <label>{{__('lang.job-role')}}:</label>
                        <select id="jobrole" name="job_role_id" class="select form-control" >
                            <option value="0">{{__('lang.select-job-role')}}</option> 
                            @foreach ($jobRoles as $jobRole)
                                <option value="{{$jobRole->id}}" {{$user->job_role_id == $jobRole->id ? "selected" : ""}}>{{ $jobRole->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <input type="hidden" value="{{$user->region_id}}" id="selectregion">
                    <div class="col-sm-6 mb-3">
                        <label>{{__('lang.region')}}: <span class="form-required">*</span></label>
                     
                        <div>
                            <button onclick="dropDown(event);" class="select form-control" type="button" style="text-align: inherit;" id="region-box" required>
                            {{__('lang.select-region')}} <span class="custom-dropdown-symbol"> &#9013; </span>
                            </button>

                            <div class="shadow rounded menu" id="region-checkbox" style="OVERFLOW-Y:scroll; position: absolute; background: white; z-index: 1; WIDTH:90%; HEIGHT: 300px;">
                                @foreach ($regions as $region)
                                    <span class="d-block menu-option">
                                        <label><input type="checkbox" name="region_id[]" value="{{$region->id}}"  {{ in_array($region->id, $userRegion) ? "checked" : ''}} >&nbsp;
                                            {{ $region->name }}
                                        </label>
                                    </span>
                                @endforeach
                                <!-- add region by js -->
                            </div>         

                        </div>
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

                    <div class="col-md-12 text-left">
                        <button type="submit" class="btn-theme">{{__('lang.submit')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection