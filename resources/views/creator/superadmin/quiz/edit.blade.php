@extends('layouts.app')

@section('content')


<div class="dash-title container-fluid mt-2">
    <div>
        <a href="{{ url('/superadmin/quizzes') }}"><b>{{ __('lang.quizzes') }} &gt;</b></a>
        <span class="bradcrumb">{{ __('lang.edit-quiz') }}</span>
    </div>
</div>

<div class="container-fluid">
    <div class="white-wrapper ">
        <div class="flex align-items-center">      
            <form action="{{ url('/superadmin/quiz/status') }}" method="POST"  class="mb-0 mr-1">
                @csrf
                <input type="hidden" name="quiz_id" value="{{$quiz->id}}">
                @if($quiz->is_active == 1)
                <input type="hidden" name="status" value="0">
                <button type='submit' class='btn-theme btn-lg'>{{__('lang.deactivate')}}</button>
                @else
                <input type="hidden" name="status" value="1">
                <button type='submit' class='btn-theme btn-lg'>{{__('lang.activate')}}</button>
                @endif
            </form> &nbsp; &nbsp;
        </div>
       
 
        <form action="{{ url('superadmin/quizzes/' . $quiz->id) }}" method="POST"
            id="edit_quiz">
            @csrf
            @method('PUT')
                <div class="container-fluid">
                
                        <div class="row">
                            <input type="hidden" name="id" value="{{ $quiz->id }}">

                            <div class="col-6 col-md-2">
                                <label>{{ __('lang.country') }}:</label>
                                <button id="country-checkbox-select" onclick="showHideDropdown(event, 'country-checkbox')" class="select form-control" type="button" id="" style=" padding-right: 20px;" required>
                                {{$displayCountry}} <span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>
                                </button>

                                <div class="shadow rounded menu" id="country-checkbox" style="OVERFLOW-Y:scroll; position: absolute; background: white; z-index: 1; WIDTH:84%; HEIGHT: 300px; display:none; padding: 5 0 0 5; margin-top: 2px">
                                    
                                        <span class="d-block menu-option dashboard-checkbox">
                                            <label>
                                                <input  type="checkbox" class="country-checkbox"  name="country[]" value="{{$quiz->country_id}}" data-name="{{__('lang.all')}}"  {{  ($quiz->country_id == -1) ?  'checked="checked" disabled' : '' }}>&nbsp;
                                                {{__('lang.all')}}
                                            </label>
                                        </span>
                                    @foreach($countries as $country)   
                                        <span class="d-block menu-option dashboard-checkbox">
                                            <label>
                                                <input onclick="doThis('{{__('lang.select-country')}}', 'country-checkbox-select', this.dataset.name, 'country-checkbox')" type="checkbox" class="country-checkbox"  name="country[]" value="{{ $country->id }}" data-name="{{ $country->name }}"  {{ ($quiz->country_id == -1) ? 'checked="checked" disabled' : (in_array($country->id, explode(',', $quiz->country_id)) ?  'checked="checked" disabled' : '') }}>&nbsp;
                                                {{ $country->name }}
                                            </label>
                                        </span>
                                    
                                    @endforeach    
                                </div>

                            </div>


                            <div class="col-6 col-md-2">
                                <label>{{ __('lang.job-role') }}:</label>
                                <button id="jobrole-checkbox-select" onclick="showHideDropdown(event, 'jobrole-checkbox')" class="select form-control" type="button"  style="padding-right: 20px;" >
                                {{$dispalyJobRole}} <span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>
                                </button>

                                <div class="shadow rounded menu" id="jobrole-checkbox" style="OVERFLOW-Y:scroll; position: absolute; background: white; z-index: 1; width: 84%; HEIGHT: 300px; display:none; padding: 5 0 0 5; margin-top: 2px; ">
                                    @foreach($jobRoles as $jobRole)   
                                        <span class="d-block menu-option" style="color:#328CB3;">
                                            <label>
                                                <input type="checkbox" class="jobrole-checkbox" onclick="doThis('{{__('lang.select-job-role')}}', 'jobrole-checkbox-select', this.dataset.name, 'jobrole-checkbox')" data-name="{{ $jobRole->name }}" 
                                                name="job_role[]" value="{{ $jobRole->id }}" {{ in_array($jobRole->id, explode(',', $quiz->job_role_id)) ?  'checked="checked" disabled' : '' }}>&nbsp;
                                                {{ $jobRole->name }}
                                            </label>
                                        </span>
                                    @endforeach  
                                </div>

                            </div>
                        </div> 

                    <br>
                <div class="row">
                    <div class="col-6 col-md-2">
                        <label>{{ __('lang.region') }}:</label>
                        <button  id="region-checkbox-select" onclick="showHideDropdown(event, 'regions-checkbox')" class="select form-control" type="button" id="region" style=" padding-right: 20px;" {{in_array(-1, explode(',', $quiz->country_id)) ? 'disabled' : ''}}>
                        {{$dispalyRegion}} <span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>
                        </button>

                        <div class="shadow rounded menu region-checkbox-div" id="regions-checkbox" style="OVERFLOW-Y:scroll; position: absolute; background: white; z-index: 1; WIDTH:84%; HEIGHT: 300px; display:none; padding: 5 0 0 5; margin-top: 2px">
                            @foreach($regions as $region)   
                                        <span class="d-block menu-option dashboard-checkbox">
                                            <label>
                                                <input onclick="doThis('{{__('lang.select-region')}}', 'region-checkbox-select', this.dataset.name, 'region-checkbox')" type="checkbox" class="region-checkbox"  name="region[]" value="{{ $region->id }}" data-name="{{ $region->name }}" {{ in_array($region->id, explode(',', $quiz->region_id)) ?  'checked="checked" disabled' : '' }}>&nbsp;
                                                {{ $region->name }}
                                            </label>
                                        </span>
                            @endforeach  
                        </div>
                    </div>   

                    <div class="col-6 col-md-2">
                        <label>{{ __('lang.group') }}:</label>
                        <button id="group-checkbox-select" onclick="showHideDropdown(event, 'group-checkbox')" class="select form-control" type="button" id="" style=" padding-right: 20px;" required>
                        {{$dispalyGroup}} <span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>
                        </button>

                        <div class="shadow rounded menu" id="group-checkbox" style="OVERFLOW-Y:scroll; position: absolute; background: white; z-index: 1; width: 84%; HEIGHT: 300px; display:none; padding: 5 0 0 5; margin-top: 2px;">
                            @foreach($groups as $group)   
                                    <span class="d-block menu-option" style="color:#328CB3;">
                                        <label>
                                            <input type="checkbox" class="group-checkbox" onclick="doThis('{{__('lang.select-group')}}', 'group-checkbox-select', this.dataset.name, 'group-checkbox')" data-name="{{ $group->name }}" 
                                            name="group_name[]" value="{{$group->id}}" {{ in_array($group->id, explode(',', $quiz->group_id)) ?  'checked="checked" disabled' : '' }} >&nbsp;
                                        {{ $group->name }}
                                        </label>
                                    </span>
                                
                            @endforeach    
                        </div>
                    </div>
                </div>


    
                        <div class="col-12 col-md-7">
                            <br>
                            <div class="form-group">
                                <label>{{ __('lang.status') }}:</label>
                                @if($quiz->is_active == 1)
                                    <span class="text-success"><i class="fa fa-check" aria-hidden="true"></i> {{__('lang.active')}} 
                                    </span>
                                @else
                                    <span class="text-danger"><i class="fa fa-times-circle" aria-hidden="true"></i> {{__('lang.inactive')}} 
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="col-xs-12 col-md-7">
                            <div class="form-group">
                                <label>{{ __('lang.name') }}:</label>
                                <input type="text" name="name" class="form-control" id="name"
                                    placeholder="{{ __('lang.name') }}"
                                    value="{{ old('name') ? old('name') : $quiz->name }}" required>
                                    @if($errors->has('name'))
                                        <div class="errorMsg" id="nameError">{{ $errors->first('name') }}
                                        </div>
                                    @endif
                            </div>        
                        </div>

                        <div class="col-xs-12 col-md-7">
                            <div class="form-group">
                                <label>{{ __('lang.description') }}:</label>
                                <textarea class="form-control" rows="4" name="description" placeholder="{{ __('lang.description') }}" maxlength = "1200" required> {{$quiz->description}}</textarea>
                                @if($errors->has('description'))
                                    <div class="errorMsg" id="quizTextError">{{ $errors->first('description') }}</div>
                                @endif

                            </div>
                        </div>

                        <div class="col-xs-12 col-sm-12 col-md-12 text-right">
                            <button type="submit" class="btn-theme">{{ __('lang.submit') }}</button>
                            <a href="{{url('/superadmin/quizzes')}}" style="text-decoration:none;"><button type="button" name="reset" class="btn-theme-border">{{ __('lang.cancel') }}</button></a>
                        </div>
                    </div>
                </div>
            </div>

        </form>

    </div>
</div>

<script>

var countries;    
$(".country-checkbox").change(function() {
   countries = [];

$(".country-checkbox").each(function() {
    if ($(this).is(":checked")) {
        countries.push($(this).val());
    } 
});

var regionArray = {!! json_encode($quizRegions) !!};

var ajaxurl = app_url + "/superadmin/country/" + countries + "/region" ;

$.ajax({
  type: 'get',
  url: ajaxurl,
    success: function (data) {
        $("#regions-checkbox").html(" ");
        if(data){
            $.each(data,function(key, value) {

               if( $.inArray( value.id.toString(), regionArray) != -1)
               {
                    $('#regions-checkbox').append(
                            `<span class="d-block menu-option" style="color:#328CB3;"><label><input type="checkbox" onclick="doThis('{{__('lang.select-region')}}', 'region-checkbox-select', this.dataset.name, 'regions-checkbox')" class="regions-checkbox" name="region[]" value="${value.id}" data-name="${value.name}" checked disabled>&nbsp;
                            ${value.name}</label></span>`   
                    );
               } else {
                    $('#regions-checkbox').append(
                            `<span class="d-block menu-option" style="color:#328CB3;"><label><input type="checkbox" onclick="doThis('{{__('lang.select-region')}}', 'region-checkbox-select', this.dataset.name, 'regions-checkbox')" class="regions-checkbox" name="region[]" value="${value.id}" data-name="${value.name}">&nbsp;
                            ${value.name}</label></span>`   
                    );
               }
               

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

</script>  
@endsection
