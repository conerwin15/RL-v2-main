@extends('layouts.app')

@section('content')

<div class="piaggio-alert">
        <div id="pathAlert"></div>
</div>

	<div class="dash-title container-fluid">
		<b>{{__('lang.learning-paths')}} </b>
		<a class="btn-theme" href="{{ url($routeSlug .'/learning-paths/create') }}">+ {{__('lang.add-learning_path')}}</a>
	</div>

	<div class="container-fluid">
		<div class="white-wrapper">
            <form class="d-lg-flex justify-content-end align-items-center" method="GET" >
                @if(isset($_GET['name']))
                <input type="text"  placeholder="{{__('lang.search-by-path-name-placeholder')}}" class="form-controller form-control mb-0"  id="search" name="name" value="{{$_GET['name']}}">
                @else
                <input type="text"  placeholder="{{__('lang.search-by-path-name-placeholder')}}" class="form-controller form-control mb-0" id="search" name="name">
                @endif
                <button type="submit" class="ml-2 btn-theme">{{__('lang.search')}}</button>
            </form>
			<div class="table">
				<table class="data-table">
					<thead>
						<tr>
							<th>{{__('lang.image')}}</th>
							<th>{{__('lang.id')}}</th>
							<th>{{__('lang.learning-path-name')}}</th>
							<th>{{__('lang.no-of-learners')}}</th>
							<th>{{__('lang.created-by')}}</th>
							<th>{{__('lang.created-on')}}</th>
                            <th>{{__('lang.uploaded_by')}}</th>
							<th width="280px" class="th-action">{{__('lang.action')}}</th>
						</tr>
					</thead>  
					<tbody>
					</tbody> 
				</table>
			</div>
		</div>
	</div>
</div>

  @section('scripts')
    
  <script>

    /*********** datatable ***********/
        $(document).ready(function (){
            var ajaxUrl = "{{url($routeSlug .'/learning-paths')}}" + window.location.search;
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                cache : false,
                ajax:  ajaxUrl,

                columns: [

                    { "data": "image" ,
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
                    {
                        data: 'learners',
                        name: 'learners'
                    },
                    {
                        data: 'created_by',
                        name: 'created_by'
                    },
                    {
                        data: 'created_on',
                        name: 'created_on'
                    },
                    {
                        data: 'uploaded_by',
                        name: 'uploaded_by'
                    },
                    {   data: 'action', 
                        name: 'action', 
                        orderable: false, 
                        searchable: false
                    },
                ], 
                    'searching': false,
                    'lengthChange': false,
                    'order': [2, 'asc'],
                    "createdRow": function( row, data ) {
                        link = "<a href='learning-paths/"+data.id+"/'>"+data.name+"</a>";
                        $(row).find('td:eq(2)').html(link);
                        $(row).find('td:eq(6)').addClass('flex');
                    }
    
            });
        });
  </script>

  @endsection
@endsection