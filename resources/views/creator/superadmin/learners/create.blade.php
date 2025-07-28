@extends('layouts.app')

@section('content')
<div class="dash-title container-fluid">
    <b>{{__('lang.assign-new-learner')}}</b>
</div>

    <div class="pull-right">
            <a class="btn-theme" href="{{  url($routeSlug .'/learning-paths') }}"> {{__('lang.back')}}</a>
    </div>
    <br>
<form class="container-fluid" method="GET">
    <div class="white-wrapper pb-4">
        <div class="row align-items-end">
            <div class="col-sm-12">
                <h6><b>Filters</b></h6>
            </div>
            <div class="col-sm-2">
                <label>{{__('lang.select-country')}}:</label>
                <select name="filter_country" id="country" class="select countries form-control" onchange="getRegion(this.value, true)" required>
                    <option value="-1"> {{__('lang.all-countries')}} </option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" id="countryId"
                            {{ @$_GET['filter_country'] == $country->id ? "selected" : '' }}>
                            {{ $country->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-sm-2">
                <label>{{ __('lang.job-role') }}:</label>
                <select name="filter_jobrole" class="select form-control"  required>
                    <option value="-1"> {{ __('lang.all') }} </option>
                    @foreach($jobRoles as $jobRole)
                        <option value="{{ $jobRole->id }}" id="jobRoleId"
                            {{ @$_GET['filter_jobrole'] == $jobRole->id ? "selected" : '' }}>
                            {{ $jobRole->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-sm-2">
                <label>{{ __('lang.region') }}:</label>
                <select name="filter_region" class="select form-control" id="region" disabled  required>
                    <option value="-1">{{__('lang.all-regions')}}</option>
                </select>
            </div>
            
            <div class="col-sm-2">
                <label>{{ __('lang.role') }}:</label>
                <select name="filter_role" class="select form-control" required>
                    <option value="-1"> {{ __('lang.all') }} </option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" id="roleId"
                            {{ @$_GET['filter_role'] == $role->id ? "selected" : '' }}>
                            {{ ucfirst(toRoleLabel($role->name)) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-sm-2">
                <label>{{ __('lang.group') }}:</label>
                <select name="filter_group"  class="select form-control"  required>
                    <option value="-1"> {{ __('lang.all') }} </option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" id="groupId"
                            {{ @$_GET['filter_group'] == $group->id ? "selected" : '' }}>
                            {{ $group->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-sm-2">
                <button type="submit" class="btn-theme">{{ __('lang.apply-filter') }}</button>
            </div>
        </div>
    </div>
</form>

    <form class="d-lg-flex justify-content-end align-items-center" method="GET" >
                <input type="text"  placeholder="{{__('lang.search-by-dealer-placeholder')}}" class="form-controller form-control mb-0"  id="search" name="dealer" value="{{isset($_GET['dealer']) ? $_GET['dealer'] : ''}}"> &nbsp;
				@if(isset($_GET['search']))
					<input type="text"  placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-controller form-control mb-0"  id="search" name="search" value="{{$_GET['search']}}">
				@else
					<input type="text"  placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-controller form-control mb-0" id="search" name="search">
				@endif				
				<button type="submit" class="ml-2 btn-theme">{{__('lang.search')}}</button> 						
    </form>
    <form action="{{ url('/superadmin/learners') }}" method="POST" class="container-fluid">
        @csrf
        <input type="hidden" name="learning_path_id" value="{{ $learningPathId }}" id="learning_path_id">
        <input type="hidden" name="filter_country" value="{{ $selectedCountry }}">
        <input type="hidden" name="filter_region" value="{{ $selectedRegion }}">
        <input type="hidden" name="filter_group" value="{{ $selectedGroup }}">
        <input type="hidden" name="filter_jobrole" value="{{ $selectedJobRole }}">
        <input type="hidden" name="filter_role" value="{{ $selectedRole }}">
        <input type="hidden" name="assignAll" id="assign" value="">
		
        <div class="white-wrapper">
            <h6 class="mb-3"><b>  {{__('lang.learners')}} </b></h6>
            <div class="table">
                <table>
                    <thead>
                        <tr>
                            <th width="7%">
                                <!-- <label for="assignAll"> -->
                                    <!-- <input type='checkbox'  id="assignAll" class="mr-1"> -->
                                    {{__('lang.select-learners')}}
                                <!-- </label> -->
                            </th>
                            <th>{{__('lang.id')}}</th>
                            <th>{{__('lang.learner-name')}}</th>
                            <th>{{__('lang.email')}}</th>
                            <th>{{__('lang.country')}}</th>
                            <th>{{__('lang.dealer')}}</th>
                            <th>{{__('lang.job-role')}}</th>
                            <th>{{__('lang.region')}}</th>
                            <th>{{__('lang.role')}}</th>
                            <th>{{__('lang.group')}}</th>
                            <th>{{__('lang.created-by')}}</th>
                            <th>{{__('lang.created-on')}}</th>
                        </tr>
                    </thead>   
                    <tbody>
                        @if(count($learners)>0)
                        @foreach ($learners as $user)
                            <tr>
                                <td class="ceck-kbox">
                                    <input type='checkbox' name='assign_learners[]' class='check_learner' value="{{ $user->id }}"> 
                                </td>
                                <td>{{++ $index}}</td>
                                <td>{{$user->name}}</td>
                                <td>{{$user->email}}</td>
                                <td>{{$user->country_id ? $user->country->name : "N/A"}}</td>
                                <td>{{ ($user->dealer_id == null) ? 'N/A' : ucfirst($user->getNameById($user->dealer_id)) }}</td>
                                <td>{{$user->jobRole ? ucwords($user->jobRole->name) : "N/A"}}</td>
                                <td>{{$user->region_id ? $user->region->name : "N/A"}}</td>
                                <td>{{ ucfirst(toRoleLabel($user->getRoleNames()->first())) }}</td>
                                <td>{{$user->group_id ? ucwords($user->group->name) : "N/A"}}</td>
                                <td>{{ucfirst($user->createdBy->name)}}</td>
                                <td>{{date('d M Y', strtotime($user->created_at))}}</td>
                                
                            </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="12" style="text-align: center;">{{__('lang.no-record')}} </td>
                        </tr>

                    @endif 
                    </tbody> 
                
                </table>
            </div>
            

            <div class="text-center">
                
                    <button type="submit"  id="assignAll" class="btn-theme">{{__('lang.assign-all')}}</button>
                    <button type="submit" class="btn-theme"> {{__('lang.assign-selected-learners')}} </button>
                </div>
        </div>
    </form>
    {{ $learners->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}

    


@endsection
