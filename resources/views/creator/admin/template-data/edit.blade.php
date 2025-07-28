@extends('layouts.app')


@section('content')
<div class="dash-title container-fluid mt-2">
    <div>
        <a href="{{ url('/admin/email-templates') }}"><b>{{ __('lang.email-template') }} &gt;</b></a>
        <span class="bradcrumb">{{ __('lang.edit-email-template') }}</span>
    </div>
</div>

<div style="margin-left:2%">
    @if($errors->any())
        {!! implode('', $errors->all('<div class="errorMsg">:message</div>')) !!}
    @endif
</div>
<form action="{{ url('admin/email-templates/' . $templateData->id) }}" method="POST"
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
                    <label>{{__('lang.select-region')}}: <span class="form-required">*</span></label>
                    <button id="region-checkbox-select" onclick="showHideDropdown(event, 'regions-checkbox')" class="select form-control" type="button" id="" style=" padding-right: 20px;" required>
                        {{$dispalyRegion}} <span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>
                    </button>

                    <div class="shadow rounded menu region-checkbox-div" id="regions-checkbox" style="OVERFLOW-Y:scroll; position: absolute; background: white; z-index: 1; WIDTH:84%; HEIGHT: 300px; display:none; padding: 5 0 0 5; margin-top: 2px">
                            <span class="d-block menu-option dashboard-checkbox">
                                <label>
                                    <input  type="checkbox" class="region-checkbox-all" id="region-checkbox-all"  name="regions[]" value="-1" {{in_array('-1', $templateRegion) ?  'checked="checked" ' : -1}} data-name="{{__('lang.all')}}" >&nbsp; {{__('lang.all')}}
                                </label>
                            </span>

                        @foreach($regions as $region)
                            <span class="d-block menu-option dashboard-checkbox">
                                <label>
                                    <input onclick="doThis('{{__('lang.select-region')}}', 'region-checkbox-select', this.dataset.name, 'region-checkbox')" type="checkbox" class="region-checkbox"  name="regions[]" value="{{ $region->id }}" data-name="{{ $region->name }}" {{in_array('-1', $templateRegion) ?  'checked="checked" ' : (in_array($region->id, $templateRegion) ?  'checked="checked" ' : '') }}>&nbsp;
                                    {{ $region->name }}
                                </label>
                            </span>

                        @endforeach
                    </div>
                    @if($errors->has('regions'))
                        <div class="errorMsg" id="country_idError">{{ $errors->first('regions') }}</div>
                    @endif
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

    $('#region-checkbox-all').change(function() {
        if($(this).is(":checked")) {
            $('.region-checkbox').prop('checked', true);
            $('.region-checkbox').prop('disabled', true);
        } else {
            $('.region-checkbox').prop('checked', false);
            $('.region-checkbox').prop('disabled', false);
        }
    });
</script>

@endsection
@endsection
