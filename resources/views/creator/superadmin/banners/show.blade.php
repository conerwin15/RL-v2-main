@extends('layouts.app')

@section('content')

<div class="dash-title container-fluid mt-2">
    <div>
        <a href="{{ url('/superadmin/banners') }}"><b>{{ __('lang.banners') }} &gt;</b></a>
        <span class="bradcrumb">{{ __('lang.show-banner') }}</span>
    </div>
</div>

	<div class="container-fluid">
		<div class="white-wrapper ">

			<div class="col-xs-12 col-sm-7">
				<div class="form-group">
					<label><strong>{{ __('lang.name') }}:</strong></label>&nbsp;&nbsp; {{ucwords($banner->heading)}}
				</div>
			</div>

			<div class="form-group col-sm-8">
				<div class="form-group row" style="margin-left:2px;">
					<label> <strong>{{__('lang.upload-picture')}}:</strong></label> &nbsp;&nbsp; <img src="{{$path.'/'. $banner->image}}"  style="width:30%;">
				</div>
			</div>
			<div class="col-12 col-sm-7">
				<div class="form-group">
					<label><strong>{{ __('lang.description') }}:</strong></label>&nbsp;&nbsp; {!! $banner->description !!}
				</div>
			</div>
		</div>
	</div>
@endsection
