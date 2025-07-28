@extends('layouts.app')
@section('content')

<div class="piaggio-alert">
    <div id="userAlert"></div>
</div>
<div class="dash-title container-fluid mt-2">
    <div>
        <a href="{{ url('/superadmin/quizzes') }}"><b>{{ __('lang.quizzes') }} &gt;</b></a>
        <span class="bradcrumb">{{ $quiz->name }}</span>
    </div>
	<a class="btn-theme ml-2" href="{{ url('superadmin/quiz-questions/create/'.$id) }}">+ {{__('lang.create-quiz-question')}}</a>
</div>

<div class="container-fluid">
		<div class="white-wrapper">
			<form class="d-lg-flex justify-content-end align-items-center" method="GET">
			
				@if(isset($_GET['search']))
					<input type="text"  placeholder="{{__('lang.search-by-question-placeholder')}}" class="form-controller form-control mb-0"  id="search" name="search" value="{{$_GET['search']}}">
				@else
					<input type="text"  placeholder="{{__('lang.search-by-question-placeholder')}}" class="form-controller form-control mb-0" id="search" name="search">
				@endif				
				<button type="submit" class="ml-2 btn-theme">{{__('lang.search')}}</button> 						
			</form>

			<div class="table">
				<table class="data-table">
					<thead>
						<tr>
							<th>{{__('lang.no')}}</th>
							<th>{{__('lang.image')}}</th>
							<th>{{__('lang.title')}}</th>
							<th width="310px">{{__('lang.action')}}</th>
						</tr>
					</thead>  
					<tbody>
					</tbody> 
				</table>
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
							<img class="quizimage north"  id="showImage" src="" />
						</div>	
						
					</div>
				</div>
			</div>
		</div>

	@section('scripts')
		<script type="text/javascript" src="{{ asset('/assets/js/image-filter.js') }}"></script>
		<script>
			$(document).ready(function (){
				var ajaxUrl = "{{url('superadmin/quizzes/'. $id)}}" + window.location.search;
				var table = $('.data-table').DataTable({
					processing: true,
					serverSide: true,
					cache : false,
					processData: false,
					ajax: ajaxUrl,

						columns: [
							{data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false },
							{ "data": "media" ,
								"render": function (data)
								{
									if(data != null || data != undefined)
									{
										var media = data.split("?");
										if(media[0] == "image") {
											return "<img  class='quiz-images-superadmin quiz-img' src='"+media[1]+"' >";
										} else {
											return "<video controls style='width:217px;'><source  src='"+media[1]+"' type='video/mp4'></video>"; 
										}
									} else {
										return " ";
									}	
								},
								orderable: false

							},
							{data: 'question_text', name: 'question_text'},
							{data: 'action', name: 'action', orderable: false},
						],

						'searching': false,
						'lengthChange': false,
						'order': [2, 'asc'],
						"createdRow": function( row ) {
							$(row).find('td:eq(3)').addClass('flex');
						}
				});
			});
			$(".data-table").on("click", ".quiz-img", function() {
				var image = $(this).attr('src');
				$('#quizModal').on('show.bs.modal', function () {
				$(".quizimage").attr("src", image);
				$(".quizimage").css("width", '100%');
				});

				angle = 0;
				$('#zoomin').prop('disabled', false);
				$('#zoomin').css('color', '#fff');
				$('#zoomout').prop('disabled', false);
				$('#zoomout').css('color', '#fff');
				$('#showImage').css('margin-left', '0px');
				$('#quizModal').modal('show');
			});
		</script>
	@endsection
@endsection
