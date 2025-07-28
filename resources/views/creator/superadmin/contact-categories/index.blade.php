@extends('layouts.app')

@section('content')
<div class="dash-title container-fluid max-width">
    <b>{{__('lang.add-contact-category')}}</b>
</div>
    <div class="container-fluid max-width">

    
        <div class="white-wrapper ">
            <form  action="{{ url('superadmin/contact-categories') }}" method="POST" enctype="multipart/form-data" class="mb-0">
                @csrf  
                    <div >

                        <div class="flex justify-content-start align-items-end no-flex">
                                <div class="col-sm-3">
                                    <label>{{__('lang.select-role')}}: </label>
                                    <select  name="role" class="form-control select mb-0" required>
                                    <option value="" disabled selected> {{__('lang.select-role')}} </option>
                                        <option value="-1"> {{__('lang.all')}} </option>
                                       @foreach ($roles as $role)
                                        <option value="{{$role->id}}" id="roleId">{{ ucfirst(toRoleLabel($role->name)) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                            <label>{{__('lang.category-name')}}:</label>
                                            <input type="text" name="name" class="form-control  mb-0" placeholder="{{__('lang.add-category-name')}}" value="{{ old('category-name') }}" required>
                                            @if($errors->has('name'))
                                                <div class="errorMsg" id="nameError">{{ $errors->first('name') }}</div>
                                            @endif
                                </div>
                                <div class="col-sm-3">
                                            <label>{{__('lang.email-address')}}:</label>
                                            <input type="email" name="email" class="form-control  mb-0" placeholder="{{__('lang.add-email-address')}}" value="{{ old('email') }}" required>
                                            @if($errors->has('email'))
                                                <div class="errorMsg" id="emailError">{{ $errors->first('email') }}</div>
                                            @endif
                                </div>

                                
                                <div class="col-sm3">
                                <button type="submit" class="btn-theme ml-1 mt-2">{{__('lang.submit')}}</button>
                                </div>
                        </div>        
                    
                    </div>
                    </div>
             
            </form>
        </div>    
    </div>

    <div class="container-fluid">
		<div class="white-wrapper">
        <div class="flex justify-content-between no-flex">
                            <h6><b>{{__('lang.all-categories')}}</b></h6>
        
            <form class="d-lg-flex justify-content-end align-items-center" method="GET" >
				@if(isset($_GET['name']))
					<input type="text"  placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-controller w-300 form-control mb-0"  id="search" name="name" value="{{$_GET['name']}}">
				@else
					<input type="text"  placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-controller w-300 form-control mb-0" id="search" name="name">
				@endif				
				<button type="submit" class="ml-2 btn-theme">{{__('lang.search')}}</button> 						
			</form>
            </div>
            <div class="table mt-4">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th> {{__('lang.id')}} </th>
                            <th>{{__('lang.category-name')}}</th>
                            <th>{{__('lang.email-address')}}</th>
                            <th>{{__('lang.role')}}</th>
                            <th>{{__('lang.created-on')}}</th>
                            <th style="text-align: center;">{{__('lang.action')}}</th>
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

            $ajaxUrl = "{{url('superadmin/contact-categories')}}" + window.location.search;
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                cache : false,
                processData: false,
                ajax: $ajaxUrl,
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false },
                        {data: 'category_name', name: 'category_name'},
                        {data: 'email', name: 'email'},
                        {data: 'role', name: 'role'},
                        {data: 'created_on', name: 'created_on'},
                        {data: 'action', name: 'action', orderable: false, searchable: false},

                    ],
                    'searching': false,
                    'lengthChange': false,
                    'order': [1, 'asc'],
                    "createdRow": function( row ) {
                        $(row).find('td:eq(5)').addClass('flex');
                    }
 
            });
        });
        </script>

    @endsection
@endsection