@extends('layouts.app')

@section('content')

    <div class="dash-title container-fluid no-flex">
        <b>{{ __('lang.banners') }}</b>
        <div class="d-lg-flex align-items-center justify-content-end no-flex">
			<form class="d-lg-flex mb-0 justify-content-end align-items-center" method="GET" >
					@if(isset($_GET['name']))
						<input type="text"  placeholder="{{__('lang.search-by-name')}}" class="form-controller form-control mb-0"  id="search" name="name" value="{{$_GET['name']}}">
					@else
						<input type="text"  placeholder="{{__('lang.search-by-name')}}" class="form-controller form-control mb-0" id="search" name="name">
					@endif
					<button type="submit" class="ml-2 btn-theme">{{__('lang.search')}}</button> 						
			</form>
            @if($totalBanner < 5)
			    <a class="btn-theme ml-2" href="{{ url('superadmin/banners/create') }}">+ {{__('lang.create-banner')}}</a>
            @endif
        </div>
    </div>

    <br/>
    <div class="container-fluid">
        <div class="white-wrapper">
            <div class="table">
                <table class="data-table">
                    <thead>
                        <tr>
							<th>{{__('lang.image')}}</th>
                            <th>{{__('lang.no')}}</th>
                            <th>{{__('lang.name')}}</th>
                            <th>{{__('lang.description')}}</th>
                            <th>{{__('lang.created-by')}}</th>
                            <th width="235px" style="text-align:center;">{{__('lang.action')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<script>

$(document).ready(function() {

    var ajaxUrl = "{{url('superadmin/banners')}}" + window.location.search;
    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        cache : false,
        processData: false,
        ajax: ajaxUrl,
        columns: [
			{
                data: 'image',
                "render": function ( data)
				{
				return "<img src='"+data+"'>";
				},
				orderable: false
            },

            {
                data: 'DT_RowIndex',
                name: 'DT_RowIndex',
                orderable: false
            },
            {
                data: 'heading',
                name: 'heading'
            },
            { 'data': 'description' ,
                "render": function (data)
                    {
                    return `<a href="#"> ${data.substring(0,20)}...</a>`;
                    }
            },

            {
                data: 'created_by',
                name: 'created_by'
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
        "createdRow":  function( row ) {
            $(row).find('td:eq(10)').addClass('flex');
            $(row).find('td:eq(2)').addClass('quiz-text');
        }
    });
});
</script>
@endsection