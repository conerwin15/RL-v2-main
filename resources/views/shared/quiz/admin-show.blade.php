@extends('layouts.app')

@section('content')

<div class="dash-title container-fluid mt-2">
    <div>
        <a href="{{ url('admin/quiz') }}"><b>{{ __('lang.quizzes') }} &gt;</b></a>
        <span class="bradcrumb">{{ $quiz->name }}</span>
    </div>
</div>

	@if(count($questions) > 0 )
        @foreach ($questions as $question)

        <div class="container-fluid">
            <div class="white-wrapper">
                <div class="">
                    <label><b>{{ __('lang.quiz-question') }} :</b></label>
                    <hr>
                        <div class="row">
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    {{$question->question_text}}
                                </div>

                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    @if(!is_null($question->media))

                                        @if($question->media_type == "image")
                                            <div><img  class="quiz-img" style="width:110px;" src="{{ asset('storage' . $viewStoragePath . $question->media) }}" ></div>
                                            <br>
                                        @else
                                            <video controls style="width:217px;">
                                                <source  src="{{ asset('storage' . $viewStoragePath . $question->media) }}" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                            <br>
                                        @endif
                                    @endif
                                </div>
                        </div>

                        <br>

                        <div class="flex discussion2">

                            <div class="flex-col-12">
                                <p class="option-all">{{__('lang.all-options')}}</p>

                                <div class="table">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th>{{__('lang.image')}}</th>
                                                <th>{{__('lang.text')}}</th>
                                                <th>{{__('lang.status')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @if(count($question->options)>0)
                                            @foreach ($question->options as $option)
                                                <tr>
                                                    <td style="width:300px;">
                                                        @if($option->media != NULL)
                                                            @php   $ext = explode('.', $option->media); @endphp

                                                            @if(in_array(strtolower($ext[1]), $imageFormat))

                                                                <img   class="quiz-images-superadmin quiz-img" src="{{ asset('storage' . $optionViewPath . $option->media) }}" >

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
                                                        <p class=" mr-2">{{ $option->option_text }} </p>
                                                    </td>
                                                    <td>
                                                            @if($option->is_correct == 1)
                                                                <span class="text-success"> {{__('lang.correct')}}  &nbsp;</span>
                                                            @else
                                                            <span class="text-danger"> {{__('lang.incorrect')}} &nbsp; </span>
                                                            @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                        <tr>
                                            <td colspan="4" style="text-align: center;">{{__('lang.no-record')}} </td>
                                        </tr>

                                        @endif
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>

                </div>
            </div>
        </div>

        @endforeach

    @else
       <br>
       <div style="text-align: center;">{{__('lang.no-record')}} </div>
    @endif

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

    @section('scripts')
	    <script type="text/javascript" src="{{ asset('/assets/js/image-filter.js') }}"></script>
	@endsection
@endsection
