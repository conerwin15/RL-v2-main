@extends('layouts.app')

@section('content')
<div class="dash-title container-fluid mt-2">
    <div>
        <a href="{{ url('/superadmin/email-templates') }}"><b>{{ __('lang.email-template') }} &gt;</b></a>
        <span class="bradcrumb">{{ __('lang.edit-email-template') }}</span>
    </div>
</div>

<div style="margin-left:2%">
    @if($errors->any())
        {!! implode('', $errors->all('<div class="errorMsg">:message</div>')) !!}
    @endif
</div>
<form action="{{ url('superadmin/email-templates/' . $templateData->id) }}" method="POST"
    id="email-template">
    @csrf
    @method('PUT')
    <div class="container-fluid">
        <div class="white-wrapper ">
            <strong>{{ __('lang.hint') }} :</strong> <br>
            <span class="text-gray">{{ __('lang.hint-text') }}</span> <br><br>
        
            <div class="row">
                <div class="col-sm-4 flex align-items-center form-group">
                    <label style="width:100px">{{ __('lang.event-type') }}:</label>
                    <select id="events" class="select form-control" name="event_id"
                        data-href="{{ url('/') }}" required>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{$templateData->mailable == $event->mailable_class ? "selected" : ''}}>{{ $event->event }}
                            </option>
                        @endforeach
                    </select>
                    @if($errors->has('event'))
                        <div class="errorMsg" id="eventError">
                            {{ $errors->first('event') }}
                        </div>
                    @endif
                </div>
            </div>

            <label>{{ __('lang.available-variable') }}:</label>
            <ol id="variable" >
                @foreach($variables as $key => $variable)
                    <li class="text-gray"> {{ $variable }} </li>
                @endforeach
            </ol>
            <p><span class="text-gray">{{ __('lang.variable-hint') }} : </span><b>{{ $showVaraibale }}</b></p>

            <div class="row">

                <div class="col-6 col-md-2">
                    <label>{{ __('lang.country') }}:</label>
                    <button id="country-checkbox-select" onclick="showHideDropdown(event, 'country-checkbox')" class="select form-control" type="button" id="" style=" padding-right: 20px;" required>
                        {{$displayCountry}} <span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>
                    </button>

                    <div class="shadow rounded menu" id="country-checkbox" style="OVERFLOW-Y:scroll; position: absolute; background: white; z-index: 1; WIDTH:84%; HEIGHT: 300px; display:none; padding: 5 0 0 5; margin-top: 2px">
                            <span class="d-block menu-option dashboard-checkbox">
                                <label>
                                    <input  type="checkbox" class="country-checkbox-all" id="country-checkbox-all"  name="country[]" value="-1" {{in_array('-1', $templateCountry) ?  'checked="checked" ' : -1}} data-name="{{__('lang.all')}}" >&nbsp; {{__('lang.all')}}
                                </label>
                            </span>

                        @foreach($countries as $country)
                            <span class="d-block menu-option dashboard-checkbox">
                                <label>
                                    <input onclick="doThis('{{__('lang.select-country')}}', 'country-checkbox-select', this.dataset.name, 'country-checkbox')" type="checkbox" class="country-checkbox"  name="country[]" value="{{ $country->id }}" data-name="{{ $country->name }}" {{in_array('-1', $templateCountry) ?  'checked="checked" ' : (in_array($country->id, $templateCountry) ?  'checked="checked" ' : '') }}>&nbsp;
                                    {{ $country->name }}
                                </label>
                            </span>

                        @endforeach
                    </div>

                    @if($errors->has('country'))
                            <div class="errorMsg" id="quizTextError">{{ $errors->first('country') }}</div>
                    @endif
                </div>

                <div class="col-6 col-md-2">
                    <label>{{ __('lang.region') }}:</label>
                    <button  id="region-checkbox-select" onclick="showHideDropdown(event, 'regions-checkbox')" class="select form-control" type="button" id="region" style=" padding-right: 20px;">
                    {{$dispalyRegion}} <span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>
                    </button>

                    <div class="shadow rounded menu region-checkbox-div" id="regions-checkbox" style="OVERFLOW-Y:scroll; position: absolute; background: white; z-index: 1; WIDTH:84%; HEIGHT: 300px; display:none; padding: 5 0 0 5; margin-top: 2px">
                        <span class="d-block menu-option dashboard-checkbox" style="color:#328CB3;"><label>
                            <input type="checkbox" class="regions-checkbox-all" id="region-checkbox-all" name="region[]" value="-1" data-name="all" {{in_array('-1', $templateRegion) ?  'checked="checked" ' : '' }}>&nbsp;
                           {{__('lang.all')}}</label>
                        </span>
                        @foreach($regions as $region)
                                    <span class="d-block menu-option dashboard-checkbox">
                                        <label>
                                            <input onclick="doThis('{{__('lang.select-region')}}', 'region-checkbox-select', this.dataset.name, 'regions-checkbox')" type="checkbox" class="regions-checkbox"  name="region[]" value="{{ $region->id }}" data-name="{{ $region->name }}" {{in_array('-1', $templateRegion) ?  'checked="checked" ' : (in_array($region->id, $templateRegion) ?  'checked="checked" ' : '') }} >&nbsp;
                                            {{ $region->name }}
                                        </label>
                                    </span>
                        @endforeach
                    </div>
                </div>
            </div>    
    </div>
           
        </div>
    </div>
    <div class="container-fluid">
        <div class="white-wrapper ">
            <div class="row">
                <input type="hidden" name="id" value="{{ $templateData->id }}">

                <div class="col-xs-12 col-md-6">
                    <div class="form-group">
                        <label>{{ __('lang.subject') }}:</label>
                        <input type="text" name="subject" class="form-control"
                            placeholder="{{ __('lang.subject') }}"
                            value="{{ $templateData->subject }}" required>

                    </div>
                </div>

                <div class="col-xs-12 col-sm-12 col-md-12">
                    <div class="form-group">
                        <strong>{{ __('lang.template-layout') }}:</strong>
                        <textarea class="form-control" name="template_layout"
                            value="{{ old('template_layout') }}" id="description"
                            required>{!! $templateData->html_template !!}</textarea>
                        @if($errors->has('html_template'))
                            <div class="errorMsg" id="templateLayouttError">
                                {{ $errors->first('html_template') }}</div>
                        @endif
                    </div>
                </div>


                <div class="col-xs-12 col-sm-12 col-md-12 text-right">
                    <button type="submit" class="btn-theme">{{ __('lang.submit') }}</button>
                </div>
            </div>


        </div>
    </div>

</form>
@section('scripts')
<script src="//cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        CKEDITOR.replace( 'description', {
            removeButtons: 'PasteFromWord',
            removePlugins: 'image, sourcearea, specialchar, horizontalrule, pastetext, pastefromword, blockquote, link'  
        });
    });

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
                        $('#regions-checkbox').append(`<span class="d-block menu-option" style="color:#328CB3;"><label><input type="checkbox" class="regions-checkbox-all" id="region-checkbox-all" name="region[]" value="-1" data-name="all">&nbsp;
                           {{__('lang.all')}}</label></span>`);
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

    $('#country-checkbox-all').change(function() {
        if($(this).is(":checked")) {
            $('.country-checkbox').prop('checked', true);
            $('.country-checkbox').prop('disabled', true);
            $('#region-checkbox-select') .prop('disabled', true);
        } else {
            $('.country-checkbox').prop('checked', false);
            $('.country-checkbox').prop('disabled', false);
            $('#region-checkbox-select').prop('disabled', false);
            $('#regions-checkbox').html (" ");
        }
    });

   $("#regions-checkbox").on("change", "#region-checkbox-all", function() {
        if($(this).is(":checked")) {
            $('.regions-checkbox').prop('checked', true);
            $('.regions-checkbox').prop('disabled', true);
        } else {
            $('.regions-checkbox').prop('checked', false);
            $('.regions-checkbox').prop('disabled', false);
        }
    });
</script>

@endsection
@endsection
