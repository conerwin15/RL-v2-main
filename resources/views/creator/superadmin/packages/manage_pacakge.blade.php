@extends('layouts.app')

@section('content')

    <div class="dash-title container-fluid no-flex">
        <div>
            <a href="{{ url('/superadmin/packages') }}"><b>{{ __('lang.manage-package') }} &gt;</b></a>
            <span class="bradcrumb">{{ __('lang.learning-paths') }}</span>
        </div>
        <div class="d-lg-flex align-items-center justify-content-end no-flex">
			<form class="d-lg-flex mb-0 justify-content-end align-items-center" method="GET" >
					@if(isset($_GET['name']))
						<input type="text"  placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-controller form-control mb-0"  id="search" name="name" value="{{$_GET['name']}}">
					@else
						<input type="text"  placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-controller form-control mb-0" id="search" name="name">
					@endif
					<button type="submit" class="ml-2 btn-theme">{{__('lang.search')}}</button> 						
			</form>
			<a class="btn-theme ml-2" href="{{ url('superadmin/package/'.$id.'/assign/learing-paths') }}">+ {{__('lang.assign-learning-path')}}</a>
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
                            <th>{{__('lang.id')}}</th>
                            <th>{{__('lang.name')}}</th>
                            <th>{{__('lang.description')}}</th>
                            <th width="250px" style="text-align:center;">{{__('lang.action')}}</th>
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
    var ajaxUrl = "{{url('superadmin/package/manage/'. $id)}}" + window.location.search;
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
                data: 'unique_ID',
                name: 'unique_ID',
            },
            {
                data: 'name',
                name: 'name'
            },
            { 'data': 'description' ,
                "render": function (data)
                    {
                    return ` ${data.substring(0,50)}`;
                    }
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
        "createdRow": function( row, data ) {
            link = `<a href=learning-paths/${data.id}/?source=package&id=${@json($id)}>${data.name}</a>`;
            $(row).find('td:eq(2)').html(link);
            $(row).find('td:eq(6)').addClass('flex');
        }
    });
});
</script>
@endsection