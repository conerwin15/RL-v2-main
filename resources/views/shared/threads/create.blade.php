@extends('layouts.app')
 
@section('content')
<div class="dash-title container-fluid max-width">
    <div>
        <a href="{{ url($routeSlug .'/forum/threads') }}"><b>{{__('lang.threads')}} &gt;</b></a> 
        <span class="bradcrumb">{{__('lang.add-thread')}}</span>
    </div>
</div>
    
<div class="container-fluid max-width">
	<div class="white-wrapper">
		<form action="{{ url($routeSlug . '/forum/threads') }}" method="POST" enctype="multipart/form-data" id="add-sales-tips" class="col-xs-12 col-sm-12">
			@csrf
			<div class="row">

				<div class="col-xs-3 col-sm-3 col-md-3">
					<div class="form-group">
						<strong>{{__('lang.discussion-forum-type')}}:</strong>
						<select  name="forum_type" class="select form-control"  id="forum_type" required>
							<option value="0" selected>{{__('lang.public')}}</option>
							<option value="1">{{__('lang.private')}}</option>
						</select>
							@if($errors->has('type'))
								<div class="errorMsg" id="type">{{ $errors->first('type') }}</div>
							@endif
					</div>
				</div>
				@if(Auth::user()->getRoleNames()->first() == 'superadmin')
					<div id="private-div" style="display:none;">
						<div class="col-xs-3 col-sm-3 col-md-3">
							<strong>{{ __('lang.country') }}:</strong>
							<button id="country-checkbox-select" onclick="showHideDropdown(event, 'country-checkbox')" class="select form-control" type="button" id="" style=" padding-right: 20px;" required>
								{{__('lang.select-country')}} <span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>
							</button>

							<div class="shadow rounded menu" id="country-checkbox" style="OVERFLOW-Y:scroll; position: absolute; background: white; z-index: 1; WIDTH:84%; HEIGHT: 300px; display:none; padding: 5 0 0 5; margin-top: 2px">	
								@foreach($countries as $country)
									<span class="d-block menu-option dashboard-checkbox">
										<label>
											<input onclick="doThis('{{__('lang.select-country')}}', 'country-checkbox-select', this.dataset.name, 'country-checkbox')" type="checkbox" class="country-checkbox"  name="country[]" value="{{ $country->id }}" data-name="{{ $country->name }}" >&nbsp;
											{{ $country->name }}
										</label>
									</span>
								@endforeach
							</div>

							@if($errors->has('country'))
									<div class="errorMsg" id="quizTextError">{{ $errors->first('country') }}</div>
							@endif
						</div>
						<div class="col-xs-3 col-sm-3 col-md-3">
							<strong>{{ __('lang.region') }}:</strong>
							<button  id="region-checkbox-select" onclick="showHideDropdown(event, 'regions-checkbox')" class="select form-control" type="button"  style=" padding-right: 20px;"  disabled required>
							{{__('lang.select-region')}} <span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>
							</button>

							<div class="shadow rounded menu region-checkbox-div" id="regions-checkbox" style="OVERFLOW-Y:scroll; position: absolute; background: white; z-index: 1; WIDTH:84%; HEIGHT: 300px; display:none; padding: 5 0 0 5; margin-top: 2px">

							</div>
							@if($errors->has('region'))
									<div class="errorMsg" id="quizTextError">{{ $errors->first('region') }}</div>
							@endif
						</div>
					</div>
				@elseif(Auth::user()->getRoleNames()->first() == 'admin')
				<div id="private-div" style="display:none;">
						<div class="col-xs-3 col-sm-3 col-md-3">
							<strong>{{ __('lang.region') }}:</strong>
							<button  id="region-checkbox-select" onclick="showHideDropdown(event, 'regions-checkbox')" class="select form-control" type="button" id="region" style=" padding-right: 20px;" required>
							{{__('lang.select-region')}} <span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>
							</button>

							<div class="shadow rounded menu region-checkbox-div" id="regions-checkbox" style="OVERFLOW-Y:scroll; position: absolute; background: white; z-index: 1; WIDTH:84%; HEIGHT: 300px; display:none; padding: 5 0 0 5; margin-top: 2px">
								@foreach($regions as $region)
										<span class="d-block menu-option dashboard-checkbox">
											<label>
												<input onclick="doThis('{{__('lang.select-region')}}', 'region-checkbox-select', this.dataset.name, 'region-checkbox')" type="checkbox" class="region-checkbox"  name="region[]" value="{{ $region->id }}" data-name="{{ $region->name }}" }}>&nbsp;
												{{ $region->name }}
											</label>
										</span>
								@endforeach
							</div>
						</div>
				</div>
				@endif
				<div class="col-xs-3 col-sm-3 col-md-3">
					<div class="form-group">
						<strong>{{__('lang.category')}}:</strong>
						<select  name="category" class="select form-control"  required>
							<option value="" disabled selected>{{__('lang.select-category')}}</option>
							@foreach ($threadCategories as $threadCategory)
									<option value="{{$threadCategory->id}}">{{ $threadCategory->name }}</option>
							@endforeach
						</select>
							@if($errors->has('category'))
								<div class="errorMsg" id="category">{{ $errors->first('category') }}</div>
							@endif
					</div>
				</div>
			</div>
				<div class="col-xs-8 col-sm-8 col-md-8">
					<div class="form-group">
						<strong>{{__('lang.title')}}:</strong>
						<textarea class="form-control descriptionText" rows="4" name="title" id="description2" required>{{ old('title') }}</textarea>
						<div class="errorMsg" id="titleError"> </div>
					</div>
				</div>

				<div class="form-group col-sm-8">
						<label> <strong>{{__('lang.upload-picture')}}:</strong></label>
						<div class="featuredimg">
							<div>
								<svg  width="30" height="30" viewBox="0 0 30 30"><g transform="translate(-1 -1)"><path d="M27.92,1H4.08A3.08,3.08,0,0,0,1,4.08V27.92A3.08,3.08,0,0,0,4.08,31H13V29H4.08A1.08,1.08,0,0,1,3,27.92V4.08A1.08,1.08,0,0,1,4.08,3H27.92A1.08,1.08,0,0,1,29,4.08V27.92A1.08,1.08,0,0,1,27.92,29H19v2h8.92A3.08,3.08,0,0,0,31,27.92V4.08A3.08,3.08,0,0,0,27.92,1Z" fill="#0097c4"/><path d="M22.29,15.71a1,1,0,1,0,1.42-1.42L16,6.59l-7.71,7.7a1,1,0,0,0,1.42,1.42L15,10.41V31h2V10.41Z" fill="#0097c4"/></g></svg>
								<img src="" class="preview">
								<input type="file" id="file" name="image"  accept="image/x-png,image/jpeg" required/>
								<div class="mt-2">{{__('lang.drop-file')}}</div>
							</div>
						</div>
						<div class="error" id="fileError">
						@if($errors->has('image'))
							<div class="errorMsg" id="descriptionError">{{ $errors->first('image') }}</div>
						@endif
						</div>
				</div>

				<div class="col-xs-12 col-sm-12 col-md-12">
					<div class="form-group row">
						<div class="col-sm-2">
								<strong>{{__('lang.body')}}:</strong>
						</div>
						<div class="col-sm-3">
							<input class="form-check-input" type="radio" name="description_type" value="0" checked="checked"> <strong>{{__('lang.description')}}:</strong>
						</div>
						<div class="col-sm-5">
							<input class="form-check-input" type="radio" name="description_type" value="1"> <strong>{{__('lang.embedded_link')}}:</strong>
						</div>
					</div>
				</div>
				<br>
				<div class="col-xs-12 col-sm-12 col-md-12" id="description-div">
					<div class="form-group">
					<strong>{{__('lang.description')}}:</strong>
						<textarea class="form-control descriptionText" rows="4" name="body" id="description1">{{ old('body') }}</textarea>
						@if($errors->has('body'))
							<div class="errorMsg" id="descriptionError">{{ $errors->first('body') }}</div>
						@endif
					</div>
				</div>

				<div class="col-xs-12 col-sm-12 col-md-12" style="display:none;" id="embedded-div">
					<div class="form-group">
						<strong>{{__('lang.embedded_link')}}:</strong>
						<textarea class="form-control" rows="4" name="embedded_link" id="embeddedLink">{{ old('embedded_link') }}</textarea>
						@if($errors->has('body'))
							<div class="errorMsg" id="embeddedError">{{ $errors->first('embedded_link') }}</div>
						@endif
					</div>
				</div>

				<div class="col-xs-12 col-sm-12 col-md-12 text-left">
						<button type="submit" class="btn-theme">{{__('lang.submit')}}</button>
				</div>
		</form>
	</div>
</div>
    <br>
	<script src="//cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
	<script>
		$(document).ready(function() {
			CKEDITOR.replaceAll( 'descriptionText', {
				removeButtons: 'PasteFromWord',
				removePlugins: 'link, sourcearea, horizontalrule, pastetext, pastefromword, blockquote, specialchar',
				addPlugins: 'smiley, emoji',
			});
		});

		$("body").on("click","input[name='description_type']",function() {
			var type = $(this).val();
			if(type == 1){
				$("#embedded-div").css('display', 'block');
				$('#description-div').css('display', 'none');
			} else {
				$("#embedded-div").css('display', 'none');
				$('#description-div').css('display', 'block')
			}
		});

		var countries;
		$(".country-checkbox").change(function() {
			$('#region-checkbox-select') .prop('disabled', false);
		countries = [];

		$(".country-checkbox").each(function() {
			if ($(this).is(":checked")) {
				countries.push($(this).val());
			}
		});

		var ajaxurl = app_url + "/superadmin/country/" + countries + "/region" ;

		$.ajax({
		type: 'get',
		url: ajaxurl,
			success: function (data) {
				$("#regions-checkbox").empty();
				if(data){
					$.each(data,function(key, value) {
						$('#regions-checkbox').append(
						`<span class="d-block menu-option" style="color:#328CB3;"><label><input type="checkbox" onclick="doThis('{{__('lang.select-region')}}', 'region-checkbox-select', this.dataset.name, 'regions-checkbox')" class="regions-checkbox" name="region[]" value="${value.id}" data-name="${value.name}">&nbsp;
						${value.name}</label></span>`
						);
					});
				}

			},
			error: function (data) {

			}

		});

		});

		$("#country-checkbox").focusout(function(){
			$(this).hide();
		});

		$("#region-checkbox").focusout(function(){
			$(this).hide();
		});

		$('#country-checkbox-all').change(function() {
			if($(this).is(":checked")) {
				$('.country-checkbox') .prop('checked', true);
				$('.country-checkbox') .prop('disabled', true);
				$('#region-checkbox-select') .prop('disabled', true);
			} else {
				$('.country-checkbox') .prop('checked', false);
				$('.country-checkbox') .prop('disabled', false);
			}

		});

		$('#forum_type').change(function(){
			if($('#forum_type').val() == 1) {
				$("#private-div").css('display', 'contents');
			} else {
				$("#private-div").css('display', 'none');
			}
		});

		$(document).on('change','#file' , function(){
            $(this).next('div').html($(this)[0].files[0].name)
            readURL(this);
        })
	</script>
@endsection
