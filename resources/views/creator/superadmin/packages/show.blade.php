@extends('layouts.app')

@section('content')

<div class="dash-title container-fluid mt-2">
	<div>
		<a href="{{ url('/superadmin/packages') }}"><b>{{ __('lang.packages') }} &gt;</b></a>
		<span class="bradcrumb">{{ __('lang.show-package') }}</span>
	</div>
</div>
<div>
	<div class="row">
		<div class="col-7">
		<div class="container-fluid ">
			<div class="white-wrapper" >
				<div style = "margin-left:4.5rem;">
				<div class="form-group col-sm-10">
					<div class="form-group row">
						<img src="{{$path.'/'. $package->image}}" style="width:100%;">
					</div>
				</div>

				<div class="col-xs-12 col-sm-10">
					<div class="form-group" style="text-align: center;">
						<label><b>
								<h5>{{ucwords($package->name)}}</h5>
							</b></label>
					</div>
				</div>

				<div class="col-12 col-sm-10">
					<div class="form-group" style="display: flex; align-items: baseline;">
						<label style=" overflow-y: auto; max-height: 150px;">{!! $package->description
							!!}</label>
					</div>
				</div>

				<div class="col-xs-12 col-sm-10">
					<div class="form-group">
						<label> <strong>{{__('lang.price')}}:</strong></label> &nbsp;&nbsp; <span
							class="original-price">${{$package->price}}</span>
						<!-- <span>${{$package->price-$package->discount_price}}</span><br> -->
					</div>
				</div>

		<div class="col-xs-12 col-sm-7">
			<div class="form-group">
				<label><strong>{{ __('lang.discounted_price') }}:</strong></label>&nbsp;&nbsp; ${{$package->discount_price }}
				
				<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#priceHistorySection" aria-expanded="false" aria-controls="priceHistorySection">
					View Price History
				</button>
			</div>
		</div>
		<div class="collapse" id="priceHistorySection">
            <h5 class="ml-4">Price History</h5>
            <ul>
                @foreach($package->priceHistories as $priceHistory)
                    <li>
                        <strong>{{ $priceHistory->price_type }}:</strong> ${{ $priceHistory->updated_price }}
                        ({{__('lang.changed-by')}}: {{ $priceHistory->changedBy->name ?? 'Unknown' }}, {{__('lang.date')}}: {{ $priceHistory->created_at->format('d-m-Y H:i:s') }})
                    </li>
                @endforeach
            </ul>
			</div>
			</div>
			</div>
		</div>
	</div>
	</div>

<style>
	.white-wrapper {
		width:600px;
		margin-top:5rem;
		margin-left:5rem;
	}
	.original-price {
    text-decoration: line-through;
  } */
	</style>

	@section('scripts')
	<!-- Add this in your HTML file -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

	<script>
		$(document).ready(function () {
			$('[data-toggle="collapse"]').collapse();
		});
		document.addEventListener('DOMContentLoaded', function () {
			var timestamps = document.querySelectorAll('.timestamp');

			timestamps.forEach(function (timestamp) {
				var localTime = moment.utc(timestamp.innerText).local().format('D-M-YYYY H:mm:ss');
				timestamp.innerText = localTime;
			});
		});
	</script>
	@endsection

	@endsection