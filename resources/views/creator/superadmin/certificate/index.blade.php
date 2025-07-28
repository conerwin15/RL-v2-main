@extends('layouts.app')

@section('content')

    <div class="dash-title container-fluid">
        <b>{{ __('lang.certificate') }}</b>
        <div class="d-lg-flex align-items-center justify-content-end ">
        <form class="d-lg-flex mb-0 justify-content-end align-items-center" method="GET" >
				@if(isset($_GET['name']))
					<input type="text"  placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-controller form-control mb-0"  id="search" name="name" value="{{$_GET['name']}}">
				@else
					<input type="text"  placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-controller form-control mb-0" id="search" name="name">
				@endif				
				<button type="submit" class="ml-2 btn-theme">{{__('lang.search')}}</button> 						
		</form>
        <a class="btn-theme ml-2" href="{{ url('superadmin/certificates/create') }}">+ {{__('lang.create_certificate')}}</a>
    
        </div>
    </div>  

        <div class="table mt-4">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{__('lang.no')}}</th>
                        <th>{{__('lang.name')}}</th>
                        <th style="text-align: center;">{{__('lang.action')}}</th>
                    </tr>
                </thead>

                    <tbody>
                </tbody>
            </table>
        </div>
    <br/>

    @section('scripts')
    <script>

    /*********** datatable ***********/
        $(document).ready(function (){
            var ajaxUrl = "{{url('superadmin/certificates')}}" + window.location.search;
            var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            cache : false,
            processData: false,
            ajax: ajaxUrl,

                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false },
                    { "data": "name" ,
                        "render": function ( data)
                         {
                            return '<img src="{{ asset('assets/images/pdf.png') }}" width="40px"> ' + data;
                         }
                    },
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                'searching': false,
                'lengthChange': false,
                'order': [1, 'asc'],
                "createdRow": function( row ) {
                    $(row).find('td:eq(2)').addClass('flex');
                }

            });
        });
    </script>

    @endsection
@endsection