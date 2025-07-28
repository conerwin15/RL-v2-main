@extends('layouts.app')

@section('content')

<div class="dash-title container-fluid mt-2">
    <div>
        <a href="{{ url($routeSlug. '/quiz') }}"><b>{{ __('lang.quizzes') }} &gt;</b></a>
        <span class="bradcrumb">{{ __('lang.view-option') }}</span>
    </div>
</div>

<form action="{{ url($routeSlug .'/quiz/question') }}" method="POST" enctype="multipart/form-data" class="container-fluid max-width">
    @csrf
	<div class="container-fluid">
			<div class="white-wrapper">

				<input type="hidden" name="questionId" value="{{$question->id}}">
				<input type="hidden" name="quizId" value="{{$quizId}}">
 				<div class="">
				 	<label><b>{{ __('lang.quiz-question') }} :</b></label>
					 <br>
						{{$question->question_text}}
				</div>

				<br>
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
				<br>
				<div class="rounded menu" id="option-checkbox" >
					<label><b>{{ __('lang.question-option') }} :</b></label>
					@if ($errors->any())
						@foreach ($errors->all() as $error)
							<div class="error">{{$error}}</div>
						@endforeach
					@endif

					<br>
						<div class="table">
							<table>
								<thead>
									<tr>
										<th>{{__('lang.image')}}</th>
										<th>{{__('lang.text')}}</th>
									</tr>
								</thead>
								<tbody>

									@foreach ($question->options as $option)
										<tr>

											<td style="width:500px;">
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

												@if($selections)
													<input type="checkbox" class="option-checkbox"  name="option[]" value="{{ $option->id }}"  {{in_array($option->id, $selections) ? 'checked' : '' }}>&nbsp;
													{{ $option->option_text }}
												@else
													<input type="checkbox" class="option-checkbox"  name="option[]" value="{{ $option->id }}" >&nbsp;
													{{ $option->option_text }}
												@endif
											</td>

										</tr>
									@endforeach
                                        </tbody>
                                    </table>
                                </div>
				</div>

				<div class="col-sm-12">
				    @if($previusQuestionId != null && $firstQuestionId != $question->id )
					   <button type="submit" class="btn-theme" name="action" value="pre">{{ __('lang.previous') }}</button>
					@endif

					@if($question->id < $lastQuestionId)
                    	<button type="submit" class="btn-theme" name="action" value="next">{{ __('lang.save & next') }}</button>
					@endif

					@if($question->id == $lastQuestionId)
						<button type="submit" class="btn-theme" name="action" value="submit">{{ __('lang.submit') }}</button>
					@endif
                </div>
			</div>
	</div>
</form>

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
