@extends('layouts.app')

@section('content')

<script>

    function doThis(title, buttonId, value, checkboxId) {
        let allSelected = $('.' + checkboxId + ':checked');
        let selectedLength = allSelected.length;
        if(selectedLength > 0) {
            if(selectedLength == 1) {
                $('#' + buttonId).html(allSelected[0].dataset.name + '<span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>');
            } else {
                $('#' + buttonId).html(selectedLength + ' Selected' + '<span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>');
            }
        } else {
            $('#' + buttonId).html(title + '<span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>');
        }
    }
</script>

    <div class="dash-title container-fluid no-flex">
        <b>{{ __('lang.quizzes') }}</b>
        <div class="d-lg-flex align-items-center justify-content-end no-flex">
        <form class="d-lg-flex mb-0 justify-content-end align-items-center" method="GET" >
				
				@if(isset($_GET['name']))
					<input type="text"  placeholder="{{__('lang.search-by-quiz-placeholder')}}" class="form-controller form-control mb-0"  id="search" name="name" value="{{$_GET['name']}}">
				@else
					<input type="text"  placeholder="{{__('lang.search-by-quiz-placeholder')}}" class="form-controller form-control mb-0" id="search" name="name">
				@endif				
				<button type="submit" class="ml-2 btn-theme">{{__('lang.search')}}</button> 						
		</form>
        <a class="btn-theme ml-2" href="{{ url('superadmin/quizzes/create') }}">+ {{__('lang.create-quiz')}}</a>
        
        </div>
    </div>  

    <br/>

        <form method="GET" class="container-fluid" >
            <div class="white-wrapper pb-4">
                <div class="row align-items-end">
                    <div class="col-sm-8">
                        <h6><b>{{__('lang.filters')}}</b></h6>
                    </div>

                    <div class="col-sm-4 row">
                        <!-- <a class="" href="{{ url('/superadmin/answer-scores/export?type=excel&country='.$export_countries. '&region='. $export_regions . '&group=' . $export_groups . '&jobRole=' . $export_jobRoles) }}">
                            
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                            {{ __('lang.export-score-with-answer') }}
                        </a> -->
                        &nbsp; &nbsp; &nbsp;
                        <!-- <a class="" href="{{ url('/superadmin/scores/export?type=excel&country='.$export_countries. '&region='. $export_regions . '&group=' . $export_groups . '&jobRole=' . $export_jobRoles) }}">
                            
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                            {{ __('lang.export-score') }}
                        </a> -->
                    </div>

                    <!-- country -->
                    <div class="col-sm-2">
                        <button id="country-checkbox-select" onclick="showHideDropdown(event, 'country-checkbox')" class="select form-control" type="button" id="" style=" padding-right: 20px;" required>
                            {{__('lang.select-country')}} <span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>
                        </button>

                        <div class="shadow rounded menu" id="country-checkbox" style="OVERFLOW-Y:scroll; position: absolute; background: white; z-index: 1; WIDTH:84%; HEIGHT: 300px; display:none; padding: 5 0 0 5; margin-top: 2px">
                            
                            <span class="d-block menu-option dashboard-checkbox">
                                    <label>
                                        <input  type="checkbox" class="country-checkbox-all" id="country-checkbox-all"  name="country[]" value="-1" data-name="{{__('lang.all')}}" >&nbsp; {{__('lang.all')}}
                                    </label>
                            </span> 
                            @foreach($countries as $country)   
                                <span class="d-block menu-option dashboard-checkbox">
                                    <label>
                                        <input onclick="doThis('{{__('lang.select-country')}}', 'country-checkbox-select', this.dataset.name, 'country-checkbox')" type="checkbox" class="country-checkbox"  name="filter_country[]" value="{{ $country->id }}" data-name="{{ $country->name }}" {{ ($export_countries == 0) ? '' : (in_array($country->id, explode(',', $export_countries)) ?  'checked="checked"' : '') }}>&nbsp;
                                        {{ $country->name }}
                                    </label>
                                </span>
                                @if(in_array($country->id, explode(',', $export_countries))) 
                                    <script>
                                        doThis('{{__('lang.select-country')}}', 'country-checkbox-select', "{{ $country->name }}" , 'country-checkbox');
                                    </script>
                                @endif
                            @endforeach    
                        </div>

                    </div> &nbsp; &nbsp;

                    <!-- region -->
                    <div class="col-sm-2">
                        <button  id="region-checkbox-select" onclick="showHideDropdown(event, 'regions-checkbox')" class="select form-control" type="button" id="region" style=" padding-right: 20px;" required>
                        {{__('lang.select-region')}} <span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>
                        </button>

                        <div class="shadow rounded menu region-checkbox-div" id="regions-checkbox" style="OVERFLOW-Y:scroll; position: absolute; background: white; z-index: 1; WIDTH:84%; HEIGHT: 300px; display:none; padding: 5 0 0 5; margin-top: 2px">
                            @foreach($regions as $region)   
                                    <span class="d-block menu-option dashboard-checkbox">
                                        <label>
                                            <input onclick="doThis('{{__('lang.select-region')}}', 'region-checkbox-select', this.dataset.name, 'region-checkbox')" type="checkbox" class="region-checkbox"  name="filter_region[]" value="{{ $region->id }}" data-name="{{ $region->name }}" {{ ($export_regions == 0) ? '' : (in_array($region->id, explode(',', $export_regions)) ?  'checked="checked"' : '') }}>&nbsp;
                                            {{ $region->name }}
                                        </label>
                                    </span>
                                    @if(in_array($region->id, explode(',', $export_regions))) 
                                        <script>
                                            doThis('{{__('lang.select-region')}}', 'region-checkbox-select', "{{ $region->name }}" , 'region-checkbox');
                                        </script>
                                    @endif
                            @endforeach    
                        </div>

                    </div> &nbsp; &nbsp;

                    <!-- jobrole-->
                    <div class="col-sm-2">
                        <button id="jobrole-checkbox-select" onclick="showHideDropdown(event, 'jobrole-checkbox')" class="select form-control" type="button" id="" style="padding-right: 20px;" required>
                        {{__('lang.select-job-role')}} <span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>
                        </button>

                        <div class="shadow rounded menu" id="jobrole-checkbox" style="OVERFLOW-Y:scroll; position: absolute; background: white; z-index: 1; width: 84%; HEIGHT: 300px; display:none; padding: 5 0 0 5; margin-top: 2px; ">
                            @foreach($jobRoles as $jobRole)   
                                <span class="d-block menu-option" style="color:#328CB3;">
                                    <label>
                                        <input type="checkbox" class="jobrole-checkbox" onclick="doThis('{{__('lang.select-job-role')}}', 'jobrole-checkbox-select', this.dataset.name, 'jobrole-checkbox')" data-name="{{ $jobRole->name }}" 
                                        name="filter_jobRole[]" value="{{ $jobRole->id }}" {{ ($export_jobRoles == 0) ? '' : (in_array($jobRole->id, explode(',', $export_jobRoles)) ?  'checked="checked"' : '')}}>&nbsp;
                                        {{ $jobRole->name }}
                                    </label>
                                </span>
                                @if(in_array($jobRole->id, explode(',', $export_jobRoles))) 
                                    <script>
                                        doThis('{{__('lang.select-job-role')}}', 'jobrole-checkbox-select', "{{ $jobRole->name }}" , 'jobrole-checkbox');
                                    </script>
                                @endif
                            @endforeach    
                        </div>

                    </div> &nbsp; &nbsp;

                    <!-- group -->

                    <div class="col-sm-2">
                        <button id="group-checkbox-select" onclick="showHideDropdown(event, 'group-checkbox')" class="select form-control" type="button" id="" style=" padding-right: 20px;" required>
                        {{__('lang.select-group')}} <span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>
                        </button>

                        <div class="shadow rounded menu" id="group-checkbox" style="OVERFLOW-Y:scroll; position: absolute; background: white; z-index: 1; width: 84%; HEIGHT: 300px; display:none; padding: 5 0 0 5; margin-top: 2px;">
                        @foreach($groups as $group)   
                                <span class="d-block menu-option" style="color:#328CB3;">
                                    <label>
                                        <input type="checkbox" class="group-checkbox" onclick="doThis('{{__('lang.select-group')}}', 'group-checkbox-select', this.dataset.name, 'group-checkbox')" data-name="{{ $group->name }}" 
                                        name="filter_group[]" value="{{$group->id}}" {{ ($export_groups == 0) ? '' : (in_array($group->id, explode(',', $export_groups)) ?  'checked="checked"' : '') }}  >&nbsp;
                                    {{ $group->name }}
                                    </label>
                                </span>
                                @if(in_array($group->id, explode(',', $export_groups))) 
                                    <script>
                                        doThis('{{__('lang.select-group')}}', 'group-checkbox-select', "{{ $group->name }}", 'group-checkbox');
                                    </script>
                                @endif
                            @endforeach    
                        </div>

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
            <div class="table">

                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{__('lang.no')}}</th>
                            <th>{{__('lang.name')}}</th>
                            <th>{{__('lang.country')}}</th>
                            <th>{{__('lang.region')}}</th>
                            <th>{{__('lang.job-role')}}</th>
                            <th>{{__('lang.group')}}</th>
                            <th>{{__('lang.description')}}</th>
                            <th>{{__('lang.status')}}</th>
                            <th>{{__('lang.created-on')}}</th>
                            <th>{{__('lang.status-updated-on')}}</th>
                            <th width="420px" style="text-align:center;">{{__('lang.action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<script>

var countries;    
$(".country-checkbox").change(function() {

countries = [];

$('#region').prop('disabled', false);

$(".country-checkbox").each(function() {
    if ($(this).is(":checked")) {
        countries.push($(this).val());
    } 
});

var ajaxurl = app_url + "/superadmin/country/" + countries + "/region" ;

$.ajax({
  type: 'get',
  url: ajaxurl,
    success: function (data) {
        $("#regions-checkbox").empty();
        if(data){
            $.each(data,function(key, value) {
                $('#regions-checkbox').append(
                `<span class="d-block menu-option" style="color:#328CB3;"><label><input type="checkbox" onclick="doThis('{{__('lang.select-region')}}', 'region-checkbox-select', this.dataset.name, 'regions-checkbox')" class="regions-checkbox" name="filter_region[]" value="${value.id}" data-name="${value.name}">&nbsp;
                ${value.name}</label></span>`
                );
            });
        }
        
    },
    error: function (data) {
    
    }

});

});

$("#country-checkbox").focusout(function(){
  $(this).hide();
});

$("#region-checkbox").focusout(function(){
  $(this).hide();
});

$("#group-checkbox").focusout(function(){
  $(this).hide();
});

$("#jobrole-checkbox").focusout(function(){
  $(this).hide();
});

$('#country-checkbox-all').change(function() {
    if($(this).is(":checked")) {
        $('.country-checkbox') .prop('checked', true);
        $('.country-checkbox') .prop('disabled', true);
        $('#region-checkbox-select') .prop('disabled', true);
    } else {
        $('.country-checkbox') .prop('checked', false);
        $('.country-checkbox') .prop('disabled', false);
        $('#region-checkbox-select') .prop('disabled', false);
    }
         
});

$(document).ready(function() {

    var ajaxUrl = "{{url('superadmin/quizzes')}}" + window.location.search;
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
                data: 'region',
                name: 'region'
            },
            {
                data: 'jobRole',
                name: 'jobRole'
            },
            {
                data: 'group',
                name: 'group'
            },
            { 'data': 'description' ,
                "render": function (data)
                    {
                    return `<a href="#" data-toggle="tooltip" title= "${data}" class="quiz-tooltip-text"> ${data.substring(0,30)}...</a>`;
                    }
            },
            { 'data': 'status' ,
                "render": function (data)
                    {
                    if(data == 'Activated')
                    {
                        return '<span class="text-success">'+data+'</span>';
                    } else {
                        return '<span class="text-danger">'+data+'</span>';
                    }
                    }
            },
            {
                data: 'created_at',
                name: 'created_at'
            },
            {
                data: 'updated_at',
                name: 'updated_at'
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
        "createdRow":  function( row ) {
            $(row).find('td:eq(10)').addClass('flex');
            $(row).find('td:eq(2)').addClass('quiz-text');
        }
    });
});
</script>
@endsection