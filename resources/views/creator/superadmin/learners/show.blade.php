@extends('layouts.app')


@section('content')

<div id="pathAlert"></div> 
    <div class="dash-title container-fluid">
        <div>
            <a href="{{ url('superadmin/learning-paths/') }}"><b>{{ucfirst($learningPath->name)}} &gt;</b></a>
            <span class="bradcrumb">{{ __('lang.manage-learners') }}</span>
        </div> 
        <div>
            <a class="btn-theme" href="{{ url('/superadmin/learning-paths/' . $learningPath->id . '/create-learner') }}">+ {{__('lang.assign-new-learner')}}</a>
            <a class="btn-theme" href="{{ url('/superadmin/learning-paths/' . $learningPath->id . '/learners/remove?' . request()->getQueryString()) }}" onclick="return confirm('{{ __('lang.delete-proceed') }}')">- {{__('lang.remove-all-learner')}}</a>
            <a class="btn-theme" href="{{  url($routeSlug .'/learning-paths') }}"> {{__('lang.back')}}</a>
        </div>	
      	
	</div>
    
    <form class="container-fluid" method="GET">
        <div class="white-wrapper pb-4">
            <div class="row align-items-end">
                <div class="col-sm-12">
                    <h6><b>{{__('lang.filters')}}</b></h6>
                </div>
                <div class="col-sm-2">
                    <label>{{__('lang.select-country')}}: <span class="form-required">*</span></label>
                    <select  name="filter_country" id="country" onchange="getRegion(this.value, true)" class="form-control select"  required>
                        <option value="-1"> {{__('lang.all-countries')}}</option>
                        @foreach ($countries as $country)
                            <option value="{{$country->id}}"  {{ @$_GET['filter_country'] == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-2">
                    <label>{{ __('lang.job-role') }}:</label>
                    <select name="filter_jobrole" class="form-control select" required>
                        <option value="-1"> {{__('lang.all')}} </option>
                        @foreach($jobRoles as $jobRole)
                            <option value="{{ $jobRole->id }}" id="roleId"
                                {{ @$_GET['filter_jobrole'] == $jobRole->id ? 'selected' : '' }}>
                                {{ ucfirst($jobRole->name) }}
                            </option>
                        @endforeach
                    </select>
                </div> 

                <div class="col-sm-2">
                    <label>{{__('lang.region')}}: <span class="form-required">*</span></label>
                    <select  name="filter_region" id="region" onchange="getDealer()" class="form-control select" disabled  required >
                        <option value="-1">{{__('lang.all-regions')}}</option>
                    </select>
                </div>
                
                <div class="col-sm-2">
                    <label>{{ __('lang.role') }}:</label>
                    <select name="filter_role" class="form-control select" required>
                        <option value="-1"> {{__('lang.all')}} </option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" id="roleId"
                                {{ @$_GET['filter_role'] == $role->id ? 'selected' : '' }}>
                                {{ ucfirst(toRoleLabel($role->name)) }}
                            </option>
                        @endforeach
                    </select>
                </div> 

                <div class="col-sm-2">
                    <label>{{ __('lang.group') }}:</label>
                    <select name="filter_group" class="form-control select" required>
                        <option value="-1"> {{__('lang.all')}} </option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" id="roleId"
                                {{ @$_GET['filter_group'] == $group->id ? 'selected' : '' }}>
                                {{ ucfirst($group->name) }}
                            </option>
                        @endforeach
                    </select>
                </div> 

                <div class="col">
                    <label for=""><br></label><br>
                    <button type="submit" class="btn-theme">{{ __('lang.apply-filter') }}</button>
                </div>
            </div>
        </div>
    </form>

    <div class="container-fluid">
		<div class="white-wrapper">
            <div class="d-lg-flex justify-content-between align-items-center" >
                <h6 class="mb-0"><b>{{__('lang.learners')}}</b></h6>
                <form class="d-lg-flex justify-content-end align-items-center col-sm-5 pr-0" method="GET">
                    <input type="text"  placeholder="{{__('lang.search-by-dealer-placeholder')}}" class="form-controller form-control mb-0"  id="search" name="dealer" value="{{isset($_GET['dealer']) ? $_GET['dealer'] : ''}}"> &nbsp;
                    @if(isset($_GET['name']))
                        <input type="text"  placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-controller form-control mb-0" id="search" name="name" value="{{$_GET['name']}}">
                    @else
                        <input type="text"  placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-controller form-control mb-0" id="search" name="name">
                    @endif

                    <button type="submit" class="btn-theme ml-2">{{__('lang.search')}}</button> 
                </form>
            </div>
            
            <div class="table">
                <table class="data-table" width="100%">
                    <thead>
                        <tr>
                        <th>{{__('lang.no')}}</th>
                        <th>{{__('lang.learner-name')}}</th>
                        <th>{{ __('lang.country') }}</th>
                        <th>{{ __('lang.dealer') }}</th>
                        <th>{{ __('lang.job-role') }}</th>
                        <th>{{ __('lang.region') }}</th>
                        <th>{{ __('lang.role') }}</th>
                        <th>{{ __('lang.group') }}</th>
                        <th>{{__('lang.assign-by')}}</th>
                        <th>{{__('lang.assign-on')}}</th>
                        <th width="100px">{{__('lang.progress')}}</th>
                        <th width="165px">{{__('lang.action')}}</th>
                        </tr>
                    </thead>   
                    <tbody>
                    </tbody> 
                
                </table>
            </div>
        </div>
    </div>
    <br>
@section('scripts')
    <script>
    /*********** datatable ***********/
        $(document).ready(function() {

            var ajaxUrl = "{{url('superadmin/learners/' . $id)}}" + window.location.search;
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                cache : false,
                processData: false,

                ajax:  ajaxUrl,

                columns: [

                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'country',
                        name: 'country'
                    },
                    {
                        data: 'dealer',
                        name: 'dealer'
                    },
                    {
                        data: 'jobRole',
                        name: 'jobRole'
                    },
                    {
                        data: 'region',
                        name: 'region'
                    },
                    {
                        data: 'role',
                        name: 'role'
                    },
                    {
                        data: 'group',
                        name: 'group'
                    },
                    {
                        data: 'assigned_by',
                        name: 'assigned_by'
                    },
                    {
                        data: 'assigned_on',
                        name: 'assigned_on'
                    },
                    {
                        data: 'progress',
                        name: 'progress'
                    },
                    {   data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ],
                "searching": false,
                "bLengthChange": false,
                'order': [1, 'asc'],
                "createdRow":  function(row) {
                    $(row).find('td:eq(10)').addClass('flex');
                }
            });
        });
    </script>
@endsection
@endsection