@extends('layouts.app')

@section('content')
<div class="dash-title container-fluid max-width">
    <div>
        <a href="{{ url($routeSlug .'/forum/threads') }}"><b>{{__('lang.threads')}} &gt;</b></a>
        <span class="bradcrumb">{{__('lang.edit-reply')}}</span>
    </div>
</div>

<div class="container-fluid max-width">
    <div class="white-wrapper">

		<form action="{{ url($routeSlug . '/reply/' . $reply->id. '/update') }}" method="POST" enctype="multipart/form-data" class="col-xs-12 col-sm-12">
			@csrf
			<input type="hidden" name="reply_id" value="{{$reply->id}}">
			<div class="row">
				<div class="col-xs-8 col-sm-8 col-md-8">
					<div class="form-group">
						<strong>{{__('lang.reply')}}:</strong>
						<textarea class="form-control descriptionText" style="height:150px" name="body" placeholder="{{__('lang.title')}}" value="{{ $reply->body }}" required>{{ $reply->body }}</textarea>
						@if($errors->has('body'))
							<div class="errorMsg" id="titleError">{{ $errors->first('body') }}</div>
						@endif
					</div>
				</div>
				<div class="form-group col-sm-5">
					<label> <strong>{{__('lang.upload-picture')}}:</strong></label>
					<div class="featuredimg">
						<div>
							<svg  width="30" height="30" viewBox="0 0 30 30"><g transform="translate(-1 -1)"><path d="M27.92,1H4.08A3.08,3.08,0,0,0,1,4.08V27.92A3.08,3.08,0,0,0,4.08,31H13V29H4.08A1.08,1.08,0,0,1,3,27.92V4.08A1.08,1.08,0,0,1,4.08,3H27.92A1.08,1.08,0,0,1,29,4.08V27.92A1.08,1.08,0,0,1,27.92,29H19v2h8.92A3.08,3.08,0,0,0,31,27.92V4.08A3.08,3.08,0,0,0,27.92,1Z" fill="#0097c4"/><path d="M22.29,15.71a1,1,0,1,0,1.42-1.42L16,6.59l-7.71,7.7a1,1,0,0,0,1.42,1.42L15,10.41V31h2V10.41Z" fill="#0097c4"/></g></svg>
							<img src="" class="preview">
							<input type="file" id="file" name="image"  accept="image/x-png,image/jpeg"/>
							<div class="mt-2">{{__('lang.drop-file')}}</div>
						</div>
					</div>
					<div class="error" id="fileError">
					@if($errors->has('image'))
						<div class="errorMsg" id="descriptionError">{{ $errors->first('image') }}</div>
					@endif
					</div>
				</div>

				<div class="form-group col-sm-3">
				<label> <strong>{{__('lang.image-preview')}}:</strong></label><br>
					<img src="{{($reply->image!= null) ? asset('storage' . Config::get('constant.THREAD_REPLY_IMAGE_STORAGE_PATH') .'/'.$reply->image) : asset('assets/images/avatar_default.png')}}" alt="image" style="width: 50%;"> 
				</div>
				<div class="col-xs-12 col-sm-12 col-md-12">
						<button type="submit" class="btn-theme">{{__('lang.submit')}}</button>
				</div>
			</div>
		</form>
	</div>
</div>

<script src="//cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<script>
	$(document).ready(function() {
		CKEDITOR.replaceAll( 'descriptionText', {
			removeButtons: 'PasteFromWord',
			removePlugins: 'link, sourcearea, horizontalrule, pastetext, pastefromword, blockquote, specialchar',
			addPlugins: 'smiley, emoji',
		});
	});

	$(document).on('change','#file' , function(){
		$(this).next('div').html($(this)[0].files[0].name)
		readURL(this);
	})
</script>

@endsection
