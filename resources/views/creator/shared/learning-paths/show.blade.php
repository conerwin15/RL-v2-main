@extends('layouts.app')

@section('content')

<div class="dash-title container-fluid">
	@if($source)
	<div>
        <a href="{{ url('superadmin/package/manage/'.$packageId) }}"><b>{{ __('lang.manage-learning-paths')}}</b></a>  <b> &gt; </b>
        <span class="bradcrumb">{{ __('lang.learning-path')}}</span>
    </div>
	@else
	<div>
        <a href="{{ url($routeSlug .'/learning-paths') }}"><b>{{ __('lang.learning-paths') }}</b></a>  <b> &gt; </b>
        <span class="bradcrumb">{{$learningPath->name}} </span>
    </div>
	@endif

</div>
<div class="container-fluid">
	<div class="row">
		<div class="col-sm-11">
			<div class="border-box">
				<div class="col-sm-4">
					<img src="{{asset('storage' . Config::get('constant.LEARNING_PATH_STORAGE') .'/'. $learningPath->featured_image)}}" class="img-fluid"/>
				</div>
				<div class="col-sm-8">
					<b class="font16 mr-1">{{$learningPath->name}}</b>
					<br>
					<span class="gray">{!! $learningPath->description !!}</span>
					<br>
					<span><span class="color">{{ __('lang.language') }}:</span> {{$learningPath->language == 1 ? 'English' : 'Vietnamese' }}</span>
					<br>
					<span><span class="color">{{ __('lang.type') }}:</span> {{$learningPath->type == "LP" ? 'Multi Module': 'ReallyBot' }}</span>
					<br>
					<span><span class="color">{{ __('lang.category') }}:</span> {{ $learningPath->category_id == '' ? 'N/A' : $learningPath->categories->name }}</span>
					<br>
					<span><span class="color">{{ __('lang.sub-category') }}:</span> {{ $learningPath->sub_category_id == '' ? 'N/A' :$learningPath->subCategories->name}}</span>
					<br>
					<span><span class="color">{{ __('lang.level') }}:</span> {{$learningPath->level}}</span>
					<br>
					<span><span class="color">{{ __('lang.price') }}:</span> ${{$learningPath->price}}</span>
					<br>
					<span><span class="color">{{ __('lang.suitable_for') }}:</span> {{$learningPath->suitable_for}}</span>
					<br>
					<span><span class="color">{{ __('lang.duration') }}:</span> {{$learningPath->duration}}</span>
					<br>
					@if($learningPath->requirements != " ")
						<span><span class="color">{{ __('lang.requirements') }}:</span> {{$learningPath->requirements}}</span>
						<br>
					@endif
					<span><span class="color">{{ __('lang.instructor') }}:</span> {{$learningPath->instructor}}</span>
					<br>
					<span><span class="color">{{ __('lang.uploaded_by') }}:</span> {{$learningPath->uploaded_by}}</span>
					<br>
					<span><span class="color">{{ __('lang.tags_Keywords') }}:</span> {{$learningPath->tags_Keywords}}</span>
					@if($learningPath->certificate)
					<span class="gray ml-1 mr-1">|</span></span>
					<span><span class="color">{{ __('lang.certificate') }}:</span>
						<a href="{{url($routeSlug .'/certificate/'. $learningPath->certificate->id . '/preview')}}" class="nobtn text-gray ml-1 pl-0 mr-0" target="_blank"><i class="fa fa-eye" aria-hidden="true"></i> </a>
						<a href="{{url($routeSlug .'/learning-paths/'. $learningPath->id. '/edit')}}" class="nobtn text-gray ml-0 p-0"><i class="fa fa-pencil mr-1" aria-hidden="true"></i> </a>
					</span>
					@endif
				</div>
			</div>
		</div>
	</div>

	<div class="row mb-5">
		@if($learningPath->is_only_bot == 1)
			<div class="col-sm-3"></div>
			<div class="col-sm-4">
				<div class="row align-items-center resource-wrapper pr-3">
					<div class="col-sm-2 resource-count">
						<span style="top:28px !important;"> <svg  width="28" viewBox="0 0 39 31.173"><g transform="translate(626.999 -1382.353)"><path d="M-590.362,1385.86v19.579h-7.092v6.645l-5.7-6.645H-620.5V1385.86Z" transform="translate(1.862 0.933)" fill="#fd5b6a"/><path d="M-592.886,1385.86v15.473a1.771,1.771,0,0,1-1.773,1.777H-620.5v2.329h17.344l5.7,6.645v-6.645h7.091V1385.86h-2.524Z" transform="translate(1.862 0.933)" fill="#f89963"/><path d="M-620.5,1385.86v19.579h2.794v-15.654a1.771,1.771,0,0,1,1.773-1.777h25.571v-2.148Z" transform="translate(1.862 0.933)" fill="#fff"/><path d="M-590.362,1393.208v.638m0,1.809v9.789h-7.092v6.645l-5.7-6.645H-620.5v-19.579h30.137v4.895" transform="translate(1.862 0.935)" fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-dashoffset="0.4" stroke-width="1"/><g transform="translate(-596.362 1409.077) rotate(180)"><path d="M30.137,26.224V6.645H23.045V0l-5.7,6.645H0V26.224Z" transform="translate(0 0)" fill="#f6ec54"/><path d="M27.613,26.224V10.75a1.771,1.771,0,0,0-1.773-1.777H0V6.645H17.344L23.046,0V6.645h7.091V26.224H27.613Z" transform="translate(0 0.001)" fill="#f8d459"/><path d="M0,19.578V0H2.794V15.654A1.771,1.771,0,0,0,4.567,17.43h25.57v2.148H0Z" transform="translate(0 6.646)" fill="#fff"/><path d="M30.137,26.224V6.645H23.045V0l-5.7,6.645H0V26.224Z" transform="translate(0 0)" fill="none" stroke="#000" stroke-linecap="square" stroke-linejoin="round" stroke-dashoffset="0.4" stroke-width="1"/><g transform="translate(6.957 14.675)"><ellipse cx="1.773" cy="1.777" rx="1.773" ry="1.777" fill="#01d1ff"/><ellipse cx="1.773" cy="1.777" rx="1.773" ry="1.777" transform="translate(5.318)" fill="#01d1ff"/><ellipse cx="1.773" cy="1.777" rx="1.773" ry="1.777" transform="translate(10.637)" fill="#01d1ff"/></g><path d="M.443,3.553A1.768,1.768,0,0,1,0,3.494,1.776,1.776,0,0,0,1.33,1.777,1.776,1.776,0,0,0,0,.059,1.769,1.769,0,0,1,.443,0a1.777,1.777,0,0,1,0,3.553Zm5.318,0a1.768,1.768,0,0,1-.443-.059,1.774,1.774,0,0,0,0-3.435,1.775,1.775,0,1,1,.443,3.494Zm5.318,0a1.768,1.768,0,0,1-.443-.059,1.774,1.774,0,0,0,0-3.435,1.775,1.775,0,1,1,.443,3.494Z" transform="translate(8.286 14.672)" fill="#00a5ff"/></g></g></svg></span>
					</div>
					<div class="col-sm-10">
						<p class="mb-1">
							<iframe id="iframe_show" name="iframe_show" title="Scorm Player" src="{{$learningPath->iframe_link}}" style="width:700px; height:500px;"></iframe>
						</p>
					</div>
				</div>
			</div>
		@else
			<div class="col-sm-3"></div>
			<div class="col-sm-8">
				@foreach ($learningPath->resources as $index => $resource)
				<div class="row align-items-center resource-wrapper pr-3">
					<div class="col-sm-2 resource-count">
						<span>
						@if($resource->type == 'chatbot_link')
									<svg  width="28" viewBox="0 0 39 31.173"><g transform="translate(626.999 -1382.353)"><path d="M-590.362,1385.86v19.579h-7.092v6.645l-5.7-6.645H-620.5V1385.86Z" transform="translate(1.862 0.933)" fill="#fd5b6a"/><path d="M-592.886,1385.86v15.473a1.771,1.771,0,0,1-1.773,1.777H-620.5v2.329h17.344l5.7,6.645v-6.645h7.091V1385.86h-2.524Z" transform="translate(1.862 0.933)" fill="#f89963"/><path d="M-620.5,1385.86v19.579h2.794v-15.654a1.771,1.771,0,0,1,1.773-1.777h25.571v-2.148Z" transform="translate(1.862 0.933)" fill="#fff"/><path d="M-590.362,1393.208v.638m0,1.809v9.789h-7.092v6.645l-5.7-6.645H-620.5v-19.579h30.137v4.895" transform="translate(1.862 0.935)" fill="none" stroke="#000" stroke-linecap="round" stroke-linejoin="round" stroke-dashoffset="0.4" stroke-width="1"/><g transform="translate(-596.362 1409.077) rotate(180)"><path d="M30.137,26.224V6.645H23.045V0l-5.7,6.645H0V26.224Z" transform="translate(0 0)" fill="#f6ec54"/><path d="M27.613,26.224V10.75a1.771,1.771,0,0,0-1.773-1.777H0V6.645H17.344L23.046,0V6.645h7.091V26.224H27.613Z" transform="translate(0 0.001)" fill="#f8d459"/><path d="M0,19.578V0H2.794V15.654A1.771,1.771,0,0,0,4.567,17.43h25.57v2.148H0Z" transform="translate(0 6.646)" fill="#fff"/><path d="M30.137,26.224V6.645H23.045V0l-5.7,6.645H0V26.224Z" transform="translate(0 0)" fill="none" stroke="#000" stroke-linecap="square" stroke-linejoin="round" stroke-dashoffset="0.4" stroke-width="1"/><g transform="translate(6.957 14.675)"><ellipse cx="1.773" cy="1.777" rx="1.773" ry="1.777" fill="#01d1ff"/><ellipse cx="1.773" cy="1.777" rx="1.773" ry="1.777" transform="translate(5.318)" fill="#01d1ff"/><ellipse cx="1.773" cy="1.777" rx="1.773" ry="1.777" transform="translate(10.637)" fill="#01d1ff"/></g><path d="M.443,3.553A1.768,1.768,0,0,1,0,3.494,1.776,1.776,0,0,0,1.33,1.777,1.776,1.776,0,0,0,0,.059,1.769,1.769,0,0,1,.443,0a1.777,1.777,0,0,1,0,3.553Zm5.318,0a1.768,1.768,0,0,1-.443-.059,1.774,1.774,0,0,0,0-3.435,1.775,1.775,0,1,1,.443,3.494Zm5.318,0a1.768,1.768,0,0,1-.443-.059,1.774,1.774,0,0,0,0-3.435,1.775,1.775,0,1,1,.443,3.494Z" transform="translate(8.286 14.672)" fill="#00a5ff"/></g></g></svg>
								@endif
								@if($resource->type == 'media_link')
								<svg width="26" viewBox="0 0 37 30.393"><path d="M41,15.059V33.341a6.059,6.059,0,0,1-6.052,6.052H10.059A6.065,6.065,0,0,1,4,33.334V15.059A6.065,6.065,0,0,1,10.059,9H34.941A6.065,6.065,0,0,1,41,15.059ZM20.848,31.22l7.658-4.625a2.841,2.841,0,0,0,0-4.863l-7.658-4.559A2.834,2.834,0,0,0,16.554,19.6v9.217a2.8,2.8,0,0,0,1.44,2.471,2.861,2.861,0,0,0,1.394.37,2.8,2.8,0,0,0,1.46-.443ZM19.487,19.439l7.658,4.625a.192.192,0,0,1,0,.33L19.487,29.02a.192.192,0,0,1-.291-.2V19.6a.185.185,0,0,1,.1-.172.178.178,0,0,1,.093,0A.139.139,0,0,1,19.487,19.439Zm-8.219,1.454V16.929a1.321,1.321,0,0,0-2.643,0v3.964a1.321,1.321,0,0,0,2.643,0Z" transform="translate(-4 -9)" fill="#1976d2"/></svg>
								@endif
								@if($resource->type == 'course_link')
									<svg width="20"  viewBox="0 0 29 35.283"><path d="M15.95,0H0V35.283H29V13.05H15.95Z" fill="#40b29b"/><path d="M9,48H29.3v2.417H9Z" transform="translate(-4.65 -24.8)" fill="#8dd8c5"/><path d="M9,59H29.3v2.417H9Z" transform="translate(-4.65 -30.483)" fill="#8dd8c5"/><path d="M9,37H29.3v2.417H9Z" transform="translate(-4.65 -19.117)" fill="#8dd8c5"/><path d="M9,26H29.3v2.417H9Z" transform="translate(-4.65 -13.433)" fill="#8dd8c5"/><path d="M9,15h7.25v2.417H9Z" transform="translate(-4.65 -7.75)" fill="#8dd8c5"/><path d="M33,27H46.05v1.45H33Z" transform="translate(-17.05 -13.95)" fill="#257c65"/><path d="M33,13.091H46.05L33,.08Z" transform="translate(-17.05 -0.041)" fill="#30917a"/></svg>
								@endif
						</span>
					</div>
					<div class="col-sm-10 resource-box yellow-border">
						<p class="mb-1">
							@if($resource->type == 'chatbot_link')
									<a href="{{ $resource->link }}" target="_blank">{{$resource->title}} </a>
								@endif
								@if($resource->type == 'media_link')
									<a  href="{{ $resource->link }}" target="_blank">{{$resource->title}} </a>
								@endif
								@if($resource->type == 'course_link')
								<a href="{{url($routeSlug .'/learning-paths/preview/' . $resource->scorm_package_id)}}" target="_blank">{{$resource->title}} </a>
								@endif
						</p>
						<small>Created On: {{ $resource->created_at->format('d/m/Y') }} </small> <br>
						<div class="text-right">
							@if($resource->type == 'course_link')
							<div class="row ml-0 mr-0 justify-content-between align-items-center">
								<div></div>
								<div>
									<a href="{{url($routeSlug .'/learning-paths/' . $resource->id . '/responses')}}" class="btn-theme btn-sm">{{ __('lang.learner-responses') }}</a>
								</div>
							</div>
							@endif
						</div>
					</div>
				</div>
				@endforeach
			</div>
		@endif
	</div>
	<div class="d-flex  justify-content-center mb-2"> <h4 style="color:green;"> Packages that this Learning Path is in </h4> </div>
	<div class="d-flex align-items-center justify-content-center">
	<table class="table table-hover table-fixed " style ="width:700px;">
	<thead>
    <tr>
      <th>S.No</th>
      <th>Image</th>
      <th>Name</th>
    </tr>
  </thead>
		<tbody class="table-body">
		@foreach ($learningPath->packageLearningPaths as $index => $packageLearningPath)
			<tr>
				<td style="width:9%">{{ $index + 1 }}</td>
				<td style="width:9%"><img src="{{($packageLearningPath->learningPackage->image != null) ? asset('storage' . Config::get('constant.LEARNING_PACKAGE') . $packageLearningPath->learningPackage->image) : asset('assets/images/avatar_default.png')}}" alt="Sample photo" width="100px;"></td>
				<td  style="width:35%">{{ $packageLearningPath->learningPackage->name }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
	</div>

	<style>
		.table-body{
			width:500px;
		}

	</style>
@endsection
