@extends('layouts.app')

@section('content')

    <div class="dash-title container-fluid max-width">
        <b>{{__('lang.email-template')}}</b>
        <div>
            <a class="btn-theme" href="{{ url('/superadmin/email-templates/create') }}" >+ {{__('lang.create-email-template')}}</a>
        </div> 
    </div> 
    
	<div class="container-fluid max-width">
	<div class="white-wrapper ">
        <div class="d-lg-flex justify-content-between align-items-center">
            <h6>{{__('lang.all')}} {{__('lang.email-template')}}</h6>
            <form class="col-sm-6 d-lg-flex justify-content-end align-items-center" method="GET" >
                @if(isset($_GET['search']))
                    <input type="text"  placeholder="{{__('lang.search-by-template-name')}}" class="form-controller form-control mb-0"  id="search" name="search" value="{{$_GET['search']}}">
                @else
                    <input type="text"  placeholder="{{__('lang.search-by-template-name')}}" class="form-controller form-control mb-0" id="search" name="search">
                @endif				
                <button type="submit" class="ml-2 btn-theme">{{__('lang.search')}}</button> 						
            </form>   
        </div>

        <div class="table mt-4">
            <h6 class="col-3"><b>  {{__('lang.learners')}} </b></h6>
            <table class="data-table display">
                <thead>
                    <tr>
                        <th>
                            <input type="hidden" id="search_by_name" name="search_by_name" value="{{@$_GET['search']}}">   
                        {{ __('lang.no') }}</th>
                        <th>{{ __('lang.subject') }}</th>
                        <th class="th-action">{{ __('lang.action') }}</th>
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
        $(document).ready(function (){
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                cache : false,
                processData: false,
                ajax: {
                    url: "{{url('superadmin/email-templates')}}",
                    data: function (d) {
                        d.search = $('#search_by_name').val()
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false },
                    { data: 'subject', name: 'subject'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},

                ],
                'searching': false,
                'lengthChange': false,
                "createdRow": function( row ) {
                    $(row).find('td:eq(2)').addClass('flex');
                }

            });
        });
        </script>
    @endsection
@endsection