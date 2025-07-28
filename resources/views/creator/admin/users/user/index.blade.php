@extends('layouts.app')

@section('content')

<div class="piaggio-alert">
        <div id="userAlert"></div>
</div>

<div class="dash-title container-fluid max-width">
    <div>     
        <b>{{__('lang.user-management')}}</b>  <b> &gt; </b>
         <span class="bradcrumb">{{__('lang.show-user')}} </span>
    </div>
        <div class="dropdown custom-drop">
            <button type="button" style="background:#388fb5 !important;border-radius:4px;color:#fff;min-width:170px" class="btn-theme dropdown-toggle  {{ request()->is('users') ? 'active' : '' }}" id="dropdownMenuOffset"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="10,20">
                {{__('lang.add-new-user')}} 
                <svg viewBox="0 0 22.243 12.621" style="width:10px;margin-right:-5px;">
                    <path d="M26.5,11.5l-9,9-9-9" transform="translate(-6.379 -9.379)"style="stroke:#fff !important;fill:none" stroke-linecap="round" stroke-linejoin="round"
                        stroke-miterlimit="10" stroke-width="3" />
                </svg>
            </button>
            <div class="dropdown-menu title-menu" aria-labelledby="dropdownMenuOffset">
                <li><a class="dropdown-item" href="{{ url('/admin/dealers/create') }}">{{__('lang.add-dealer')}}</a></li>
                <li><a class="dropdown-item" href="{{ url('/admin/customers/create') }}">{{__('lang.add-staff')}}</a></li>
            </div>
        </div>
</div>  

<br />
<a  style="float:right;" class="" href="{{ url('/admin/learning-progress/export?type=excel&region='. $export_regions . '&group=' . $export_groups . '&jobRole=' . $export_jobRoles. '&role=' . $export_roles) }}">
<i class="fa fa-file-excel-o" aria-hidden="true"></i>
{{ __('lang.export-data') }}</a>
<br>
<div class="container-fluid max-width">
    <form  method="GET" class="white-wrapper pb-4 pt-3">
    <div class="row">
        <div class="col-sm-12">
            <h6><b>{{__('lang.filters')}}</b></h6>
        </div>
        <div class="col-sm-2">
            <label>{{ __('lang.select-job-role') }}:</label>
            <select name="filter_jobRole" class="form-control select" required>
                <option value="0"> {{__('lang.all')}} </option>
                @foreach($jobRoles as $jobRole)
                    <option value="{{ $jobRole->id }}" id="jobRoleId"
                        {{ @$_GET['filter_jobRole'] == $jobRole->id ? 'selected' : '' }}>
                        {{ $jobRole->name }}</option>
                @endforeach
            </select>
        </div> 

        <div class="col-sm-2">
            <label>{{__('lang.region')}}: </label>
            <select  name="filter_region" id="region"  class="form-control select"  required >
                <option value="0">{{__('lang.all')}}</option>
                @foreach($regions as $region)
                    <option value="{{ $region->id }}" id=""
                        {{ @$_GET['filter_region'] == $region->id ? 'selected' : '' }}>
                        {{ $region->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-sm-2">
            <label>{{ __('lang.role') }}:</label>
            <select name="filter_role" class="form-control select" required>
                <option value="0"> {{__('lang.all')}} </option>
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
                <option value="0"> {{__('lang.all')}} </option>
                @foreach($groups as $group)
                    <option value="{{ $group->id }}" id="groupId"
                        {{ @$_GET['filter_group'] == $group->id ? 'selected' : '' }}>
                        {{ $group->name }}</option>
                @endforeach
            </select>
        </div> 

        <div class="col">
            <label for=""><br></label><br>
            <button type="submit" class="btn-theme">{{ __('lang.apply-filter') }}</button>
        </div>
    </div>
    </form>
</div>

<div class="container-fluid max-width">
<div class="white-wrapper pt-0">
    <div class="pt-3">
        <form class="d-lg-flex justify-content-end align-items-center" method="GET" >
            <input type="text"  placeholder="{{__('lang.search-by-dealer-placeholder')}}" class="form-controller form-control mb-0"  id="search" name="dealer" value="{{isset($_GET['dealer']) ? $_GET['dealer'] : ''}}"> &nbsp;  &nbsp;
            <input type="text"  placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-controller form-control mb-0"  id="search" name="name" value="{{isset($_GET['search']) ? $_GET['search'] : ''}}">			
            <button type="submit" class="ml-2 btn-theme">{{__('lang.search')}}</button> 						
        </form>
    </div>
    
<div class="table">
    <h6 class="col-3"><b>  {{__('lang.learners')}} </b></h6>    
    <table class="data-table">
        <thead>
            <tr>
                <th>

                    {{ __('lang.no') }}</th>
                <th>{{ __('lang.name') }}</th>
                <th>{{ __('lang.email-address') }}</th>
                <th>{{ __('lang.dealer') }}</th>
                <th>{{ __('lang.job-role') }}</th>
                <th>{{ __('lang.region') }}</th>
                <th>{{ __('lang.role') }}</th>
                <th>{{ __('lang.group') }}</th>
                <th>{{ __('lang.created-by') }}</th>
                <th>{{ __('lang.created-on') }}</th>
                <th width="110px" class="th-action">{{ __('lang.action') }}</th>
            </tr>
        </thead>

        <tbody>
        </tbody>
    </table>
    </div>
</div>    
</div>    

<x-change-password-modal />
    @section('scripts')
    <script>

    /*********** datatable ***********/
        $(document).ready(function (){
            var ajaxUrl = "{{url('admin/users')}}" + window.location.search;
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                cache : false,
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
                        data: 'email',
                        name: 'email'
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
                        data: 'created_by',
                        name: 'created_by'
                    },
                    {
                        data: 'created_on',
                        name: 'created_on'
                    },
                    {   data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ], 
                    'searching': false,
                    'lengthChange': false,
                    'order': [1, 'asc'],
                    "createdRow": function( row ) {
                        $(row).find('td:eq(10)').addClass('flex');
                    }
    
            });
        });
        </script>

    @endsection
@endsection