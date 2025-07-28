@extends('layouts.app')
 
@section('content')
<div class="dash-title container-fluid">
    <div>
        <a  href="{{ url('/superadmin/sales-tips') }}"><b>{{__('lang.sales-tips')}} ></b></a> 
        <span class="bradcrumb">{{__('lang.add-sales-tip')}}</span>
    </div>
</div>
  
<form action="{{ url('/superadmin/sales-tips') }}" method="POST" enctype="multipart/form-data" id="add-news-promotions" class="container-fluid">
    @csrf
    <div class="white-wrapper">
        <div class="col-sm-7 p-0">
            <div class="row">
                <div class="col-sm-12 mb-3">
                    <label>{{ __('lang.title') }}:</label>
                    <input type="text" name="title" class="form-control" placeholder="{{ __('lang.title') }}" value="{{ old('title') }}" required>
                    @if($errors->has('title'))
                        <div class="errorMsg" id="titleError">{{ $errors->first('title') }}</div>
                    @endif
                </div>
                <div class="col-sm-12 mb-3">
                    <label>{{ __('lang.description') }}:</label>
                    <textarea class="form-control" rows="4"  name="description"  id="description" required>{{ old('description') }}</textarea>
                    @if($errors->has('description'))
                    <div class="errorMsg" id="descriptionError">
                        {{ $errors->first('description') }}
                    </div>
                    @endif
                    <span style="color:red">* {{ __('lang.max-char-lenth-mark-featured') }} </span>
                </div>

                <div class="col-sm-6 mb-3">
                    <label>{{ __('lang.media') }}:</label>
                    <div class="featuredimg">
                        <div>
                            <svg width="30" height="30" viewBox="0 0 30 30"><g transform="translate(-1 -1)"><path d="M27.92,1H4.08A3.08,3.08,0,0,0,1,4.08V27.92A3.08,3.08,0,0,0,4.08,31H13V29H4.08A1.08,1.08,0,0,1,3,27.92V4.08A1.08,1.08,0,0,1,4.08,3H27.92A1.08,1.08,0,0,1,29,4.08V27.92A1.08,1.08,0,0,1,27.92,29H19v2h8.92A3.08,3.08,0,0,0,31,27.92V4.08A3.08,3.08,0,0,0,27.92,1Z" fill="#0097c4"></path><path d="M22.29,15.71a1,1,0,1,0,1.42-1.42L16,6.59l-7.71,7.7a1,1,0,0,0,1.42,1.42L15,10.41V31h2V10.41Z" fill="#0097c4"></path></g></svg>
                            <input type="file" name="media" class="form-control attachment" placeholder="{{ __('lang.media') }}">
                            <div class="mt-2"> {{ __('lang.drop-media') }}  </div>
                        </div>
                    </div>
                    <div class="" id="mediaError" style="color:red;"></div>
                    @if($errors->has('media'))
                        <div class="errorMsg" id="videoError">{{ $errors->first('media') }}</div>
                    @endif

                </div>

                <div class="col-sm-6 mb-3">
                    <label for="">{{ __('lang.preview') }}</label>
                    <div id="preview"> </div>
                </div>


                <div class="col-sm-6 mb-3">
                    <label>{{ __('lang.country') }}:</label> 
                    <select name="filter_country" id="country" class="select form-control countries" onchange="getRegion(this.value , true)" required>
                        <option value="-1"> {{ __('lang.all') }} </option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" id="countryId"
                                {{ @$_GET['filter_country'] == $country->id ? "selected" : '' }}>
                                {{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-6 mb-3">
                    <label>{{ __('lang.job-role') }}:</label>
                    <select id="jobrole" name="filter_jobrole" class="select form-control" >
                        <option value="-1">{{ __('lang.select-job-role') }}</option>
                        @foreach($jobRoles as $jobRole)
                        <option value="{{ $jobRole->id }}">{{ $jobRole->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-6 mb-3">
                    <label>{{ __('lang.region') }}:</label>
                    <select  name="region_id" id="region" class="select form-control"  required disabled>
                        <option>{{__('lang.select-region')}}</option>
                    </select>
                </div>   

                <div class="col-sm-6 mb-3">
                    <label>{{ __('lang.group') }}:</label>
                    <select id="groups" name="filter_group" class="select form-control" >
                        <option value="-1">{{ __('lang.select-group') }}</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
        
                <div class="col-sm-12">
                    <button type="submit" class="btn-theme" id="submit">{{ __('lang.submit') }}</button>
                    <span class="errorMsg"> {{ __('lang.note') }}: {{ __('lang.news-sales-note') }} </span>
                </div>
            </div>
        </div>
    </div>
</form>

<script src="//cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<script>

    $(document).ready(function() {
        $('#mediaError').html("{{ __('lang.media-error') }}");
        CKEDITOR.replace( 'description', {
            removeButtons: 'PasteFromWord',
            removePlugins: 'image, sourcearea, specialchar, horizontalrule, pastetext, pastefromword, blockquote, link'  
        });
    });
    $(document).on("change", ".attachment", function(evt) {
        $("#preview").html(" ");
        $('#mediaError').html(" ");
        $( ".featuredimg" ).css({ border: "1px dashed #ccc" });
        $('#submit').prop( "disabled", false );

        var $source =  URL.createObjectURL(this.files[0]);
        var extension = (this.files[0].name).split('.').pop().toLowerCase();
        var imageExtensions = {!! json_encode(Config::get("constant.SUPPORTED_IMAGE_FORAMTS")) !!};
        var videoExtensions = {!! json_encode(Config::get("constant.SUPPORTED_VIDEO_FORAMTS")) !!};
        var pdfExtenstions = {!!  json_encode(Config::get("constant.SUPPORTED_DOCUMENT_FORMATS")) !!};
        
        if (imageExtensions.lastIndexOf(extension) != -1) {
            $("#preview").append("<img src='"+ $source +"' style='width:100%;height:150px'>");
        } else if(videoExtensions.lastIndexOf(extension) != -1){
            $("#preview").append("<video src='"+ $source +"' controls style='width:100%;height:150px'></video>");
        } else if(pdfExtenstions.lastIndexOf(extension) != -1) {
            $("#preview").append('<iframe src="'+ $source +'" style="width:400px; height:150px;" frameborder="0"></iframe>');
        } else {
            $( ".featuredimg" ).css({ border: "1px red solid"});
            $('#mediaError').html("{{ __('lang.media-error') }}");
            $('#submit').prop( "disabled", true );
        }

    });
</script>
@endsection
