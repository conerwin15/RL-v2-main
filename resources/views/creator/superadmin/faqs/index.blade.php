@extends('layouts.app')

@section('content')

<div class="dash-title container-fluid mt-3">
    <b class="col-sm-2">{{ __('lang.faqs') }}</b>
	<form class="d-lg-flex col-sm-10 justify-content-end align-items-center" method="GET">
			<div class="col-sm-4">
				<select  name="category" class="form-control select mb-0">
						<option value="" disabled selected> {{__('lang.select-category')}} </option>
							<option value="-1" > {{__('lang.all')}} </option>
							@foreach ($faqCategories as $faqCategory)
							<option value="{{$faqCategory->id}}"  {{ @$_GET['category'] == $faqCategory->id ? 'selected' : '' }}>{{ ucfirst($faqCategory->faq_category) }}</option>
							@endforeach
                </select>
			</div> 
            @if(isset($_GET['name']))
                <input type="text" placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-control" id="search" name="name"
                    value="{{ $_GET['name'] }}">
            @else
                <input type="text" placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-control" id="search" name="name">
            @endif
            <button type="submit" class="btn-theme ml-2">{{ __('lang.search') }}</button>
			
			<div style="width:150px">
			<a class="btn-theme ml-2" href="{{ url('superadmin/faqs/create') }}">+
        {{ __('lang.create-faq') }}</a>
			</div>
    </form>
</div>

<br>
<div class="container-fluid">
    <div class="white-wrapper">
        <div class="table mt-4">
            <table  class="data-table display">
                <h6 class="col-3"><b>  {{__('lang.faqs')}} </b></h6>
                    <thead>
                        <tr>

                            <th>{{__('lang.no')}}</th>
                            <th>{{ __('lang.question') }}</th>
                            <th>{{ __('lang.answer') }}</th>
                            <th width="110px" class="th-action">{{ __('lang.action') }}</th>

                        </tr>
                    </thead>
                    <tbody>

                    </tbody>

            </table>
		</div>

    </div>
</div>

	@section('scripts')
		<script>
			/*********** datatable ***********/
			$(document).ready(function() {

				var ajaxUrl = "{{url('superadmin/faqs')}}" + window.location.search;
				var table = $('.data-table').DataTable({
					processing: true,
					serverSide: true,
					cache : false,
					processData: false,

					ajax:  ajaxUrl,

					columns: [

						{
							data: 'DT_RowIndex',
							name: 'DT_RowIndex',
							orderable: false
						},
						{
							data: 'question',
							name: 'question'
						},
						{
							data: 'answer',
							name: 'answer'
						},
						{   data: 'action',
							name: 'action',
							orderable: false,
							searchable: false
						},
					],
					"searching": false,
					"bLengthChange": false,
					'order': [1, 'asc'],
					"createdRow": function( row ) {  // check
						$(row).find('td:eq(3)').addClass('flex');
					}
				});
			});
		</script>
	@endsection
@endsection
