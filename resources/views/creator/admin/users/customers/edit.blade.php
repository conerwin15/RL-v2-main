@extends('layouts.app')

@section('content')
<div class="dash-title container-fluid max-width">
   <div>
    <a  href="{{ url('admin/users') }}"><b>{{ __('lang.user') }} &gt;</b></a> 
        <span class="bradcrumb">{{ __('lang.edit-staff') }}</span>
   </div>
</div>

<form action="{{ url('admin/customers/'. $user->id) }}" method="POST" id='add_user'
    class="container-fluid max-width white-wrapper">
    @csrf
    @method('PUT')

    <div class="col-sm-6">
           
        <div class="row">
            <input type="hidden" name="id" value="{{ $user->id }}">

            <div class="col-md-12">
                <div class="form-group">
                    <label>{{ __('lang.name') }}: <span class="form-required">*</span></label>
                    <input type="text" name="name" class="form-control"
                        placeholder="{{ __('lang.name') }}" value="{{ $user->name }}" required>
                    @if($errors->has('name'))
                        <div class="errorMsg" id="nameError">{{ $errors->first('name') }}</div>
                    @endif
                </div>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <label>{{ __('lang.email') }}:<span class="form-required">*</span></label>
                    <input type="email" name="email" class="form-control"
                        placeholder="{{ __('lang.email') }}" value="{{ $user->email }}" required>
                    @if($errors->has('email'))
                        <div class="errorMsg" id="emailError">{{ $errors->first('email') }}</div>
                    @endif
                </div>
            </div>

            <div class="col-sm-6 mb-3">
                <label>{{__('lang.select-dealer')}}: <span class="form-required">*</span></label>
                <select  name="dealer_id" id="dealer" class="select form-control"  required>
                    <option value="" disabled selected>{{__('lang.select-dealer')}}</option>
                    @foreach ($dealers as $dealer)
                            <option  value="{{$dealer->id}}" {{ $user->dealer_id == $dealer->id ? "selected" : ''}}>{{$dealer->name}}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <label>{{ __('lang.job-role') }}:</label>
                    <select id="jobrole" name="job_role_id" class="select form-control">
                        <option value="0">{{ __('lang.select-job-role') }}</option>
                        @foreach($jobRoles as $jobRole)
                            <option value="{{ $jobRole->id }}"
                                {{ $user->job_role_id == $jobRole->id ? "selected" : "" }}>
                                {{ $jobRole->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-sm-6  mb-3">
                <label>{{__('lang.region')}}:</label>
                <select id="region" name="region_id"  class="select form-control">
                    <option value="0" disabled> {{__('lang.select-region')}}</option>   
                    @foreach ($regions as $region)
                        <option value="{{$region->id}}" {{ ($region->id == $user->region_id) ? 'selected' : ''}}>{{ $region->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-sm-6">
                <div class="form-group">
                    <label>{{ __('lang.group') }}:</label>
                    <select id="group" name="group_id" class="select form-control">
                        <option value="0">{{ __('lang.select-group') }}</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}"
                                {{ $user->group_id == $group->id ? "selected" : "" }}>
                                {{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-sm-6 mb-3">
                            <label>{{__('lang.remarks')}}: </label>
                            <input type="text" name="remarks" class="form-control" placeholder="{{__('lang.remarks')}}" value="{{ $user->remarks }}">
                            @if($errors->has('remarks'))
                                <div class="errorMsg" id="remarksError">{{ $errors->first('remarks') }}</div>
                            @endif
            </div>
        </div>
    </div>    

        <div class="col-xs-12 col-sm-12 col-md-12 text-left">
            <button type="submit" class="btn-theme">{{ __('lang.submit') }}</button>
        </div>
    
</form>


@endsection
