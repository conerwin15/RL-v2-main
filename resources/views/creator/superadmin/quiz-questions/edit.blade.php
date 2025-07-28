@extends('layouts.app')

@section('content')

<div class="dash-title container-fluid mt-2">
    <div>
        <a href="{{ url('/superadmin/quizzes') }}"><b>{{ __('lang.quiz') }} &gt;</b></a>
        <span class="bradcrumb">{{ __('lang.edit-quiz-question') }}</span>
    </div>
</div>

<form action="{{  url('/superadmin/quiz-questions/' . $quizQuestion->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="container-fluid">
        <div class="white-wrapper ">
            <div class="col-sm-7 p-0">
                <div class="row">
                    <input type="hidden" name="id" value="{{$quizQuestion->id}}">
                    <div class="col-12 col-md-12">
                        <label>{{ __('lang.text') }}:</label>
                        <textarea class="form-control" rows="4" name="text" placeholder="{{ __('lang.text') }}" maxlength="1200" required>{{ $quizQuestion->question_text }}</textarea>
                        @if($errors->has('text'))
                            <div class="errorMsg" id="textError">{{ $errors->first('text') }}</div>
                        @endif
                    </div>
                    
                    <div class="col-6 col-md-6">
                        <br>   
                        <label>{{ __('lang.media') }}:</label>
                        <div class="featuredimg">
                            <div>
                                <svg width="30" height="30" viewBox="0 0 30 30"><g transform="translate(-1 -1)"><path d="M27.92,1H4.08A3.08,3.08,0,0,0,1,4.08V27.92A3.08,3.08,0,0,0,4.08,31H13V29H4.08A1.08,1.08,0,0,1,3,27.92V4.08A1.08,1.08,0,0,1,4.08,3H27.92A1.08,1.08,0,0,1,29,4.08V27.92A1.08,1.08,0,0,1,27.92,29H19v2h8.92A3.08,3.08,0,0,0,31,27.92V4.08A3.08,3.08,0,0,0,27.92,1Z" fill="#0097c4"></path><path d="M22.29,15.71a1,1,0,1,0,1.42-1.42L16,6.59l-7.71,7.7a1,1,0,0,0,1.42,1.42L15,10.41V31h2V10.41Z" fill="#0097c4"></path></g></svg>
                                <input type="file" name="media" class="form-control media" placeholder="{{ __('lang.media') }}">
                                <div class="mt-2"> {{ __('lang.drop-question-media') }} </div>
                            </div>
                        </div>
                        <div class="" id="mediaError" style="color:red;"></div>
                        @if($errors->has('media'))
                            <div class="errorMsg" id="videoError">{{ $errors->first('media') }}</div>
                        @endif
                    </div>

                    <div class="col-sm-6 mb-6">
                        <br>
                        <label for="">{{ __('lang.preview') }}</label>
                        <div id="preview">
                            @if($quizQuestion->media != '')
                                @php   $ext = explode('.', $quizQuestion->media); @endphp       
                                @if(in_array(strtolower($ext[1]), $imageFormat))
                                    <img src="{{ asset('storage' . $viewStoragePath . $quizQuestion->media) }}"
                                        style='width:315px;height:150px'> &nbsp; &nbsp; &nbsp;
                                @else
                                    <video controls style="width: 100%;">
                                        <source
                                            src="{{ asset('storage' . $viewStoragePath . $quizQuestion->media) }}"
                                            type="video/mp4">
                                            {{ __('lang.video-support-message') }}
                                    
                                    </video>
                                @endif
                            @endif
                        
                        </div>
                    </div>
        
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <br>
                        <button type="submit" class="btn-theme" id="submit">{{ __('lang.submit') }}</button>
                    </div>
                </div>

            </div>    


        </div>
    </div>

</form>

<script>
    $(document).on("change", ".media", function(evt) {
        $("#preview").html(" ");
        $('#mediaError').html(" ");
        $( ".featuredimg" ).css({ border: "1px dashed #ccc" });
        $('#submit').prop( "disabled", false );

        var $source =  URL.createObjectURL(this.files[0]);
        var extension = (this.files[0].name).split('.').pop().toLowerCase();
        var imageExtensions = {!! json_encode(Config::get("constant.SUPPORTED_IMAGE_FORAMTS")) !!};
        var videoExtensions = {!! json_encode(Config::get("constant.SUPPORTED_VIDEO_FORAMTS")) !!};
        
        if (imageExtensions.lastIndexOf(extension) != -1) {
            $("#preview").append("<img src='"+ $source +"' style='width:100%;height:150px'>");
        } else if(videoExtensions.lastIndexOf(extension) != -1){
            $("#preview").append("<video src='"+ $source +"' controls style='width:100%;height:150px'></video>");
        } else {
            $( ".featuredimg" ).css({ border: "1px red solid"});
            $('#mediaError').html("{{ __('lang.quiz-media-error') }}");
            $('#submit').prop( "disabled", true );
        }
    });
 </script> 
   
@endsection
