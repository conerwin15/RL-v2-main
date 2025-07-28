@extends('layouts.app')

@section('content')
<div class="dash-title container-fluid">
	<b> {{ __('lang.learning-paths') }} ({{$learningPathCount}})</b>

	<form class="" method="GET">
	    <div class="search">
            @if(isset($_GET['search']))
                <input type="text" placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-control border-4 border-gray" id="search" name="search" value="{{ $_GET['search'] }}" style="max-width:100% !important">
            @else
                <input type="text" placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-control border-4 border-gray" id="search" name="search" style="max-width:100% !important">
            @endif
		
			<button>
			<i class="fa fa-search" aria-hidden="true"></i>
			</button>
		</div>
	</form>
</div>
	<div class="container-fluid user-learning-path">
		<div class="row">
			@if(count($userLearningPaths) > 0)
			@foreach ($userLearningPaths as $userLeaningPath)
				@if($userLeaningPath->learningPath != null)
					<div class="col-sm-3 mb-4">
						<div class="white-wrapper">
							<div class="img-wrap">
								<img src="{{asset('storage' . Config::get('constant.LEARNING_PATH_STORAGE').'/'.$userLeaningPath->learningPath->featured_image)}}" style="object-fit: contain;">
							</div>
							<div class="flex justify-content-start">
								<div>
								<p class="mb-1">
									<strong>{{$userLeaningPath->learningPath->name}}</strong>
								</p>
								<p class="mb-1 show-read-more">
									{!! $userLeaningPath->learningPath->description !!}
								</p>
								<p>
									<small class="text-gray mt-2 weight300"><b>{{ __('lang.assign-on') }}</b></small><br>
									<small >{{date('d M Y', strtotime($userLeaningPath->created_at))}}</small></p>
								</div>
							</div>
							<div class="w-footer pt-2 pr-3 mb-0 flex justify-content-between align-items-center">
								<div class="pl-2">
								    {{__('lang.status')}}: 
									@if($userLeaningPath->progress_percentage == null)
										<label class="badge badge-warning">{{ __('lang.incomplete') }}</label>
									@else
										<label class="badge badge-success">{{$userLeaningPath->progress_percentage}}% {{ __('lang.completed') }}</label>
									@endif
									
								</div>
								<a href="{{url($routeSlug. '/learning-paths/' . $userLeaningPath->learningPath->id)}}" class="btn-theme-border" style="padding: 2px 16px;">
									{{__('lang.view')}}
								</a>
							</div>
						</div>
					</div>
				@endif	
			@endforeach
				@else
				<h4 style="text-align: center;">{{__('lang.no-record')}} </h4>
			@endif  
		</div>

        
{!! $userLearningPaths->links('vendor.pagination.bootstrap-4') !!}

<style>
	.show-read-more .more-text{
        display: none;
    }
</style>

<script>
	var maxLength = 50;
	$(".show-read-more").each(function(){
        var myStr = $(this).text();
        if($.trim(myStr).length > maxLength){
            var newStr = myStr.substring(0, maxLength);
            var removedStr = myStr.substring(maxLength, $.trim(myStr).length);
            $(this).empty().html(newStr);
            $(this).append(' <a href="javascript:void(0);" class="read-more">read more...</a>');
            $(this).append('<span class="more-text">' + removedStr + '</span>');
        }
    });
    $(".read-more").click(function(){
        $(this).siblings(".more-text").contents().unwrap();
        $(this).remove();
    });
</script>

@endsection
