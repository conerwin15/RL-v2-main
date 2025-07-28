@extends('layouts.app')

@section('content')

<div class="dash-title container-fluid mt-2">
    <div>
        <a href="{{ url('/superadmin/quizzes') }}"><b>{{ __('lang.quizzes') }} &gt;</b></a>
        <span class="bradcrumb">{{ __('lang.create-quiz') }}</span>
    </div>
</div>

<form action="{{ url('superadmin/quizzes') }}" method="POST" id="create_quiz">
    @csrf

        <div class="container-fluid">
            <div class="white-wrapper ">
                
                <div class="row">

                    <div class="col-sm-2">
                        <label>{{ __('lang.country') }}:</label>
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
                                        <input onclick="doThis('{{__('lang.select-country')}}', 'country-checkbox-select', this.dataset.name, 'country-checkbox')" type="checkbox" class="country-checkbox"  name="country[]" value="{{ $country->id }}" data-name="{{ $country->name }}" >&nbsp;
                                        {{ $country->name }}
                                    </label>
                                </span>
                               
                            @endforeach    
                        </div>

                        @if($errors->has('country'))
                                <div class="errorMsg" id="quizTextError">{{ $errors->first('country') }}</div>
                        @endif
                    </div>


                    <div class="col-sm-2">
                        <label>{{ __('lang.job-role') }}:</label>
                        <button id="jobrole-checkbox-select" onclick="showHideDropdown(event, 'jobrole-checkbox')" class="select form-control" type="button" id="" style="padding-right: 20px;" required>
                        {{__('lang.select-job-role')}} <span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>
                        </button>

                        <div class="shadow rounded menu" id="jobrole-checkbox" style="OVERFLOW-Y:scroll; position: absolute; background: white; z-index: 1; width: 84%; HEIGHT: 300px; display:none; padding: 5 0 0 5; margin-top: 2px; ">
                            @foreach($jobRoles as $jobRole)   
                                <span class="d-block menu-option" style="color:#328CB3;">
                                    <label>
                                        <input type="checkbox" class="jobrole-checkbox" onclick="doThis('{{__('lang.select-job-role')}}', 'jobrole-checkbox-select', this.dataset.name, 'jobrole-checkbox')" data-name="{{ $jobRole->name }}" 
                                        name="jobRole[]" value="{{ $jobRole->id }}">&nbsp;
                                        {{ $jobRole->name }}
                                    </label>
                                </span>
                            @endforeach  
                        </div>
    
                    </div>
                </div>     

                <br>
                <div class="row">

                    <div class="col-sm-2">
                        <label>{{ __('lang.region') }}:</label>
                        <button  id="region-checkbox-select" onclick="showHideDropdown(event, 'regions-checkbox')" class="select form-control" type="button"  style=" padding-right: 20px;"  disabled required>
                        {{__('lang.select-region')}} <span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>
                        </button>

                        <div class="shadow rounded menu region-checkbox-div" id="regions-checkbox" style="OVERFLOW-Y:scroll; position: absolute; background: white; z-index: 1; WIDTH:84%; HEIGHT: 300px; display:none; padding: 5 0 0 5; margin-top: 2px">
                           
                        </div>
                        @if($errors->has('region'))
                                <div class="errorMsg" id="quizTextError">{{ $errors->first('region') }}</div>
                        @endif
                    </div>   

                    <div class="col-sm-2">
                        <label>{{ __('lang.group') }}:</label>
                        <button id="group-checkbox-select" onclick="showHideDropdown(event, 'group-checkbox')" class="select form-control" type="button" id="" style=" padding-right: 20px;" required>
                        {{__('lang.select-group')}} <span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>
                        </button>

                        <div class="shadow rounded menu" id="group-checkbox" style="OVERFLOW-Y:scroll; position: absolute; background: white; z-index: 1; width: 84%; HEIGHT: 300px; display:none; padding: 5 0 0 5; margin-top: 2px;">
                            @foreach($groups as $group)   
                                    <span class="d-block menu-option" style="color:#328CB3;">
                                        <label>
                                            <input type="checkbox" class="group-checkbox" onclick="doThis('{{__('lang.select-group')}}', 'group-checkbox-select', this.dataset.name, 'group-checkbox')" data-name="{{ $group->name }}" 
                                            name="group[]" value="{{$group->id}}"  >&nbsp;
                                        {{ $group->name }}
                                        </label>
                                    </span>
                                
                            @endforeach    
                        </div>
                    </div>
                </div>
                    <div class="col-xs-12 col-sm-7">
                        <br>
                        <div class="form-group">
                            <label>{{ __('lang.name') }}:</label>
                            <input type="text" name="name" class="form-control" id="name"
                                placeholder="{{ __('lang.name') }}"
                                value="{{ old('name') }}" required>
                            @if($errors->has('name'))
                                <div class="errorMsg" id="nameError">{{ $errors->first('name') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="col-12 col-sm-7">
                        <div class="form-group">
                            <label>{{ __('lang.description') }}:</label>
                            <textarea class="form-control" rows="4" name="description" placeholder="{{ __('lang.description') }}" maxlength = "1200" required> {{old('description')}}</textarea>
                            @if($errors->has('description'))
                                <div class="errorMsg" id="quizTextError">{{ $errors->first('description') }}</div>
                            @endif

                        </div>
                    </div> 

                    <div class="col-xs-12 col-sm-12 col-sm-12">
                        <button type="submit" class="btn-theme">{{ __('lang.submit') }}</button>
                        <a href="{{url('/superadmin/quizzes')}}" style="text-decoration:none;"><button type="button" name="reset" class="btn-theme-border">{{ __('lang.cancel') }}</button></a>
                    </div>
                

            </div>
        </div>

</form>

<script>

var countries;    
$(".country-checkbox").change(function() {
    $('#region-checkbox-select') .prop('disabled', false);
   countries = [];

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
                `<span class="d-block menu-option" style="color:#328CB3;"><label><input type="checkbox" onclick="doThis('{{__('lang.select-region')}}', 'region-checkbox-select', this.dataset.name, 'regions-checkbox')" class="regions-checkbox" name="region[]" value="${value.id}" data-name="${value.name}">&nbsp;
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
    }
         
});

</script>    
@endsection
