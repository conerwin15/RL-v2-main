@extends('layouts.app')

@section('content')
<div class="dash-title container-fluid">
    <div>     
        <b>{{__('lang.user-management')}}</b>  <b> &gt; </b>
         <span class="bradcrumb">{{__('lang.show-user')}} </span>
    </div>
</div>


<div class="container-fluid">
    <form  method="GET" class="white-wrapper">
        <div class="row">
            <div class="col-sm-12">
                <h6><b>{{__('lang.filters')}}</b></h6>
            </div>
            <div class="col-sm-3">
                <label>{{ __('lang.select-job-role') }}:</label>
                <select name="filter_jobrole" class="form-control select" required>
                    <option value="-1"> {{__('lang.all')}} </option>
                    @foreach($jobRoles as $jobRole)
                        <option value="{{ $jobRole->id }}" id="jobRoleId"
                            {{ @$_GET['filter_jobrole'] == $jobRole->id ? 'selected' : '' }}>
                            {{ $jobRole->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-sm-3">
                <label>{{ __('lang.group') }}:</label>
                <select name="filter_group" class="form-control select" required>
                    <option value="-1"> {{__('lang.all')}} </option>
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

<div class="container-fluid max-width">    
        <form class="flex justify-content-end align-items-cener mt-4" method="GET">
                    @if(isset($_GET['name']))
                        <input type="text" placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-controller form-control" id="search" name="name" value="{{ $_GET['name'] }}">
                    @else
                        <input type="text" placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-controller form-control" id="search" name="name">
                    @endif
           
             &nbsp; &nbsp;<button type="submit" class="btn-theme">{{ __('lang.search') }}</button>
        </form>   

        <div  class="white-wrapper">
            <h6 class="mb-3"><b>{{ __('lang.all-staff') }}</b></h6>
            <div  class="table">

                <table class="table data-table">
                    <thead>
                        <tr>
                            <th>{{ __('lang.no') }}</th>
                            <th>{{ __('lang.name') }}</th>
                            <th>{{ __('lang.email-address') }}</th>
                            <th>{{ __('lang.job-role') }}</th>
                            <th>{{ __('lang.group') }}</th>
                            <th>{{ __('lang.created-by') }}</th>
                            <th>{{ __('lang.created-on') }}</th>
                            <th style="width: 22%">{{ __('lang.action') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
</div>
@section('scripts')
    <script>

    /*********** datatable ***********/
        $(document).ready(function (){
            var ajaxUrl = "{{url('dealer/staff')}}" + window.location.search;
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
                        data: 'jobRole',
                        name: 'jobRole'
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
                        $(row).find('td:eq(7)').addClass('flex');
                    }
            });
        });
        </script>

    @endsection
@endsection
