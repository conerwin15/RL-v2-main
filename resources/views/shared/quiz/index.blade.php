@extends('layouts.app')

@section('content')

    <div class="dash-title container-fluid">
        <b>{{ __('lang.quizzes') }}</b>
        <div class="d-lg-flex align-items-center justify-content-end ">
        <form class="d-lg-flex mb-0 justify-content-end align-items-center" method="GET" >

				@if(isset($_GET['name']))
					<input type="text"  placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-controller form-control mb-0"  id="search" name="name" value="{{$_GET['name']}}">
				@else
					<input type="text"  placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-controller form-control mb-0" id="search" name="name">
				@endif
				<button type="submit" class="ml-2 btn-theme">{{__('lang.search')}}</button>
		</form>

        </div>
    </div>

    <br/>
    <div class="table">

        <table class="data-table">
            <thead>
                <tr>
                    <th>{{__('lang.no')}}</th>
                    <th>{{__('lang.name')}}</th>
                    <th>{{__('lang.description')}}</th>
                    <th>{{__('lang.score')}}</th>
                    <th>{{__('lang.completed-on')}}</th>
                    <th width="280px" class="th-action">{{__('lang.action')}}</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    @section('scripts')
    <script>
          /*********** datatable ***********/
    $(document).ready(function() {

        var ajaxUrl = "{{url($routeSlug.'/quiz')}}" + window.location.search;
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
                    data: 'name',
                    name: 'name'
                },
                { 'data': 'description' ,
                        "render": function (data)
                         {
                            return `<a href="#" data-toggle="tooltip" title= "${data}" class="quiz-tooltip-text"> ${data.substring(0,30)}...</a>`;
                         }
                },
                {
                    data: 'score',
                    name: 'score'
                },
                {
                    data: 'created_on',
                    name: 'created_on'
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
                $(row).find('td:eq(5)').addClass('flex');
            }
        });
    });
    </script>
    @endsection
@endsection
