@extends('layouts.app')
 
@section('content')
<div class="dash-title container-fluid max-width">
    <div>
         <b>{{__('lang.import-users')}} </b>
    </div>
</div>
   
	<div class="container-fluid max-width">
        <div class="white-wrapper">
			<form action="{{url('superadmin/import-users/dealer')}}" method="POST" enctype="multipart/form-data" class="col-xs-12 col-sm-6">
				@csrf
				<div class="row">

					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="form-group">
							<strong>{{__('lang.import-dealers')}}:</strong>
							<input type="file" name="dealer_file" class="form-control" placeholder="{{__('lang.import-dealers')}}" accept=".xlsx, .xls, .csv" required>
							@if($errors->has('dealer_file'))
								<div class="errorMsg" >{{ $errors->first('dealer_file') }}</div>
							@endif
						</div>
					</div>
				
					<div class="col-xs-12 col-sm-12 col-md-12 text-left">
							<button type="submit" class="btn-theme">{{__('lang.import')}}</button>
					</div>
				</div>
			</form>
        </div>
	</div>

    <br>
  
	<div class="container-fluid max-width">
        <div class="white-wrapper">
			<form action="{{url('superadmin/import-users/staff')}}" method="POST" enctype="multipart/form-data" class="col-xs-12 col-sm-6">
				@csrf
				<div class="row">

					<div class="col-xs-12 col-sm-12 col-md-12">
						<div class="form-group">
							<strong>{{__('lang.import-staffs')}}:</strong>
							<input type="file" name="staff_file" class="form-control" placeholder="{{__('lang.import-users')}}"  required>
							@if($errors->has('staff_file'))
								<div class="errorMsg" >{{ $errors->first('staff_file') }}</div>
							@endif
						</div>
					</div>
				
					<div class="col-xs-12 col-sm-12 col-md-12 text-left">
							<button type="submit" class="btn-theme">{{__('lang.import')}}</button>
					</div>
				</div>
			</form>
		</div>
	</div>	


	@if(@$failedFor)

		<div class="container-fluid max-width">
			<div class="white-wrapper">
				<p> <b>{{__('lang.total-import-user')}}</b> : {{@$importRecordCount}} </p>
				<p> <b>{{__('lang.total-failed-user')}}</b> : {{@$failedRecordCount}} </p>
			</div>
		</div>
		<div class="container-fluid">
			<div class="white-wrapper">
				<div class="table">
				<table class="data-table">
					<thead>
						<tr>
							<th>{{__('lang.email')}}</th>
						</tr>
					</thead>
					<tbody>
							@if(count(@$failedFor)>0)
								@foreach($failedFor as $email)
									<tr>
										<td>{{ $email }}</td>
									</tr>
								@endforeach
							@else
									<tr>
										<td style="text-align: center;">{{ __('lang.no-record') }} </td>

									</tr>
							@endif	
					</tbody>
				</table>
		</div>
	@endif
		
@endsection
