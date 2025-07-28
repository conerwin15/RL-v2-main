@extends('layouts.app')

@section('content')

<div class="dash-title container-fluid mt-2">
    <div>
        <a href="{{ url('/superadmin/quizzes') }}"><b>{{ __('lang.quiz-question') }} &gt;</b></a>
        <span class="bradcrumb">{{ __('lang.create-option') }}</span>
    </div>
</div>

<div class="container-fluid">
    <div class="white-wrapper ">

        <form action="{{ url('/superadmin/quiz-question/option/add') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <input type="hidden" name="question_id" value="{{$id}}">
                <div class="col-12 col-md-7">
                    <label>{{ __('lang.text') }}: ({{ __('lang.max-char-lenth-quiz-option') }})</label>
                    <textarea class="form-control" rows="4" name="text" placeholder="{{ __('lang.text') }}" maxlength="300">{{ old('text') }}</textarea>
                    @if($errors->has('text'))
                        <div class="errorMsg" id="textError">{{ $errors->first('text') }}</div>
                    @endif
                </div>
       
            </div>
                <div class="row">
                    <div class="col-md-4 col-5">
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

                    <div class="col-sm-4 mb-5">
                        <br>
                        <label for="">{{ __('lang.preview') }}</label>
                        <div id="preview">
                        </div>
                    </div> 

                    <div class="col-12 col-md-5">
                        <div class="form-group">
                            <div class="row">
                                <div class="col">
                                <br>
                                <label>{{ __('lang.is-correct') }}:</label>
                                    <select name="status" class="select form-control"  required>
                                        <option  value="1"> {{ __('lang.yes') }} </option>
                                        <option value="0"> {{ __('lang.no') }} </option>
                                    </select>
                                </div>
                            </div>
                            
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <button type="submit" class="btn-theme">{{ __('lang.add-option') }}</button>
                    </div>
                </div>

        </form>

        <hr class="mt-1" style="margin-left:-15px;margin-right:-15px;">
        <div class="flex discussion2">
            <div class="flex-col-1 pt-2">
            </div>
            <div class="flex-col-12">
                <p class="option-all">{{__('lang.all-options')}}</p> 

                <div class="table">
                    <table>
                        <thead>
                            <tr>
                                <th>{{__('lang.image')}}</th>
                                <th>{{__('lang.text')}}</th>
                                <th>{{__('lang.status')}}</th>
                                <th width="200px">{{__('lang.action')}}</th>
                            </tr>
                        </thead>  
                        <tbody>
                        @if(count($options)>0)
                            @foreach ($options as $option)
                                <tr>
                                    <td>
                                        @if($option->media != NULL)    
                                            @php   $ext = explode('.', $option->media); @endphp

                                            @if(in_array(strtolower($ext[1]), $imageFormat))
                                        
                                                <img  class="quiz-images quiz-img" src="{{ asset('storage' . $optionViewPath . $option->media) }}" >
                                        
                                            @else
                                                <video controls style="width:217px;">
                                                    <source  src="{{ asset('storage' . $optionViewPath . $option->media) }}" type="video/mp4">
                                                    Your browser does not support the video tag.
                                                </video>    
                                            @endif
                                        @else
                                            
                                        @endif  
                                    </td>
                                    <td> 
                                        <b class=" mr-2 ckeditor-text">{{ $option->option_text }} </b>
                                    </td>
                                    <td> 
                                            @if($option->is_correct == 1)
                                                <span class="text-success"> {{__('lang.correct')}}  &nbsp;</span>
                                            @else
                                            <span class="text-danger"> {{__('lang.incorrect')}} &nbsp; </span> 
                                            @endif
                                    </td>

                                    <td>
                                    <form action="{{ url('/superadmin/quiz-question/option/delete/')}}" method="POST" >
                                        <a  href="{{ url('superadmin/quiz-question/' .$id. '/option/' . $option->id . '/edit') }}" style="margin-left:5%;"><i class="fa fa-pencil" aria-hidden="true"></i> </a>   
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="option_id" value="{{$option->id}}">
                                        <button type="submit" class="text-danger nobtn ml-2 mr-2" onclick="return confirm('{{ __('lang.delete-confirmation-text') }}')" class="text-danger"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                    </form>
                                    </td>

                                </tr>
                            @endforeach
                        @else
                        <tr>
                            <td colspan="5" style="text-align: center;">{{__('lang.no-record')}} </td>
                        </tr>

                        @endif        
                        </tbody> 
                    </table>
			    </div>

            </div>
        </div>

    </div>
</div>
   
    <!---- Image modal ------->
        <div id="quizModal" class="modal fade custom-model" id="addCountry" role="dialog">
            <p class="quiz-p">
                <button type="button" id="zoomin" class="quiz-zoom-button" onclick="zoomin()"><i class="fa fa-search-plus quiz-image-icon" aria-hidden="true"></i></button>
                <button type="button" id="zoomout" class="quiz-zoom-button" onclick="zoomout()"><i class="fa fa-search-minus quiz-image-icon" aria-hidden="true"></i></button>
                <button type="button" id="left_rotate" class="quiz-zoom-button" onclick="rotateImage(this.id)"><i class="fa fa-undo quiz-image-icon" aria-hidden="true"></i></button>
                <button type="button" id="right_rotate" class="quiz-zoom-button" onclick="rotateImage(this.id)"><i class="fa fa-repeat quiz-image-icon" aria-hidden="true"></i></button>
                <button type="button" id="full_screen" class="quiz-zoom-button" onclick="openFullscreen()"><i class="fa fa-arrows-alt quiz-image-icon" aria-hidden="true"></i></button>
                <button type="button" class="close" data-dismiss="modal" style="opacity: 1 !important; float: none !important;"><i class="fa fa-times-circle quiz-image-icon" aria-hidden="true"></i></button>
			</p>
			<div class="modal-dialog">
				<div class="modal-content" style="width: 100% !important; background-color:transparent;border:none;">
					<div class="modal-body">
						<div id="img-container">
							<img class="quizimage"  id="showImage" src="" />
						</div>	
						
					</div>
				</div>
			</div>
		</div>

<script type="text/javascript" src="{{ asset('/assets/js/image-filter.js') }}"></script>
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
