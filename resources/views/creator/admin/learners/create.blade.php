@extends('layouts.app')

@section('content')
<div class="dash-title container-fluid">
    <b>{{__('lang.assign-new-learner')}}</b>
</div>

<form class="container-fluid" method="GET">
    <div class="white-wrapper pb-4">
        <div class="row align-items-end">
            <div class="col-sm-12">
                <h6><b>{{ __('lang.filters') }}</b></h6>
            </div>

            <div class="col-sm-2">
                <label>{{ __('lang.select-job-role') }}:</label>
                <select name="filter_jobrole" class="select form-control"  required>
                    <option value="-1"> {{ __('lang.all') }} </option>
                    @foreach($jobRoles as $jobRole)
                        <option value="{{ $jobRole->id }}" id="jobRoleId"
                            {{ @$_GET['filter_jobrole'] == $jobRole->id ? "selected" : '' }}>
                            {{ $jobRole->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-sm-2">
                <label>{{ __('lang.region') }}:</label>
                <select name="filter_region" class="select form-control"  required>
                    <option  disabled> {{ __('lang.select') }}  {{ __('lang.region') }} </option>
                    <option value="-1"> {{ __('lang.all') }} </option>
                    @foreach($regionNames as $regionName)
                        <option value="{{ $regionName->id }}" 
                            {{ @$_GET['filter_region'] == $regionName->id ? "selected" : '' }}>
                            {{ $regionName->name }}
                        </option>
                    @endforeach
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
                            {{ $group->name }}</option>
                    @endforeach
                </select>
            </div>
    
            <div class="col-sm-2">
                <button type="submit" class="btn-theme">{{ __('lang.apply-filter') }}</button>
            </div>
        </div>
    </div>
</form>

<div class="container-fluid">

    <div class="white-wrapper">

        <div class="d-lg-flex justify-content-between align-items-right" >
            <form class="d-lg-flex justify-content-end align-items-center col-sm-12 pr-0" method="GET">
                <input type="text"  placeholder="{{__('lang.search-by-dealer-placeholder')}}" class="form-controller form-control mb-0"  id="search" name="dealer" value="{{isset($_GET['dealer']) ? $_GET['dealer'] : ''}}"> &nbsp;
                @if(isset($_GET['search']))
                    <input type="text"  placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-controller form-control mb-0" id="search" name="search" value="{{$_GET['search']}}">
                @else
                    <input type="text"  placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-controller form-control mb-0" id="search" name="search">
                @endif

                <button type="submit" class="btn-theme ml-2">{{__('lang.search')}}</button> 
            </form>
        </div>
        <form action="{{ url('admin/learners') }}" method="POST" class="container-fluid">
            @csrf
            <input type="hidden" name="learning_path_id" value="{{ $learningPathId }}" id="learning_path_id">
            <input type="hidden" name="filter_group" value="{{ $selectedGroup }}">
            <input type="hidden" name="filter_jobrole" value="{{ $selectedJobRole }}">
            <input type="hidden" name="filter_role" value="{{ $selectedRole }}">
            <input type="hidden" name="filter_region" value="{{ request()->get('filter_region')  }}">
            <input type="hidden" name="assignAll" id="assign" value="">
            
                <h6 class="mb-3"><b> {{__('lang.learners')}} </b></h6>
                <div class="table">
                    <table>
                        <thead>
                            <tr>
                                <th width="7%" >
                                    {{__('lang.select-learners')}}
                                </th>
                                <th>{{__('lang.id')}}</th>
                                <th>{{__('lang.learner-name')}}</th>
                                <th>{{__('lang.email')}}</th>
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
                                <td colspan="11" style="text-align: center;">{{__('lang.no-record')}} </td>
                            </tr>

                        @endif 
                        </tbody> 
                    
                    </table>
                </div>
                

                <div class="text-center">
                    
                        <button type="submit"  id="assignAll" class="btn-theme">{{__('lang.assign-all')}}</button>
                        <button type="submit" class="btn-theme">{{__('lang.assign-selected-learners')}}</button>
                    </div>
            </div>
        </form>
    </div>    
</div>    
    {{ $learners->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}

    


@endsection
