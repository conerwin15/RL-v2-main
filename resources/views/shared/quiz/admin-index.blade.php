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
                    <th>{{__('lang.status')}}</th>
                    <th>{{__('lang.status-updated-on')}}</th>
                    <th>{{__('lang.created-on')}}</th>
                    <th width="280px">{{__('lang.action')}}</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

<script>
    $(document).ready(function() {

        var ajaxUrl = "{{url('admin/quiz')}}" + window.location.search;
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
                { 'data': 'status' ,
                    "render": function (data)
                        {
                        if(data == 'Activated')
                        {
                            return '<span class="text-success">'+data+'</span>';
                        } else {
                            return '<span class="text-danger">'+data+'</span>';
                        }
                        }
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'updated_at',
                    name: 'updated_at'
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
                $(row).find('td:eq(7)').addClass('flex');
                $(row).find('td:eq(4)').addClass('flex');
            }
        });
    });
</script>
@endsection