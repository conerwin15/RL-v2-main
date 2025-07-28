@extends('layouts.app')

@section('content')
<div class="piaggio-alert">
	<div id="countryAlert"></div>
</div>

<div class="dash-title container-fluid">
	<b>{{__('lang.country')}}</b>
	<button type="submit"  data-toggle="modal"  data-backdrop="static" data-keyboard="false" class="btn-theme" onclick="showAddCountry();">
	+ {{__('lang.add-new-country')}}
	</button>
</div>

	<div class="container-fluid">
		<div class="white-wrapper">

            <form class="d-lg-flex justify-content-end align-items-center"  method="GET" >
				@if(isset($_GET['name']))
				<input type="text"  placeholder="{{__('lang.search-by-name-placeholder')}}" class="w-200 form-control" id="search" name="name" value="{{$_GET['name']}}">
				@else
                <input type="text"  placeholder="{{__('lang.search-by-name-placeholder')}}" class="w-200 form-control" id="search" name="name">
        		@endif
				<button type="submit" class="btn-theme ml-2">{{__('lang.search')}}</button>
		    </form>
			<div class="table">
				<table class="data-table">
					<thead>
							<tr>
								<th>{{__('lang.no')}}</th>
								<th>{{__('lang.country-name')}}</th>
								<th>{{__('lang.no-of-regions')}}</th>
								<th>{{__('lang.created-by')}}</th>
								<th>{{__('lang.created-on')}}</th>
								<th style="text-align:center;">{{__('lang.action')}}</th>
							</tr>
					</thead>

					<tbody>

                    </tbody>
				</table>
			</div>
		</div>
	</div>
	<!-- Modal -->
  	<div class="modal fade custom-model" id="addCountry" role="dialog">
      	<div class="modal-dialog">
          	<div class="modal-content">
              	<div class="modal-body">
					<h5 class="modal-title mb-2">{{ __('lang.add-new-country') }}</h5>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
                  	<!-- form -->
                  	<form action="{{ url('/superadmin/countries/') }}" method="POST"
                      id="addCountryForm">
                      @csrf
                      	<div class="form-group">
							<label>{{ __('lang.country-name') }}:</label>
							<input type="text" name="name" class="form-control"
                                      placeholder="{{ __('lang.country-name') }}"
                                      value="{{ old('name') }}" required>
							<div class="errorMsg" id="addCountryError"></div>
						</div>
						<div class="text-center">
							<button type="button" class="btn-theme addCountry">{{ __('lang.submit') }}</button>
							<button type="button" class="btn-theme-border" data-dismiss="modal">{{ __('lang.cancel') }}</button>
						</div>
					</form>
          		</div>

      		</div>
  		</div>
	</div>


  <!------ Edit Country----->

    <!-- Modal -->
<div class="modal fade custom-model" id="editCountry" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                <!-- form -->
                <h5 class="modal-title mb-2">{{ __('lang.edit-country') }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <form action="" method="POST" id="editCountryForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="editCountryId" value="">
                    <div class="form-group">
                        <label>{{ __('lang.country-name') }}:</label>
                        <input type="text" name="name" id="countryName" class="form-control"
                            placeholder="{{ __('lang.country-name') }}"
                            value="{{ old('name') }}" required>
                        <div class="errorMsg" id="editCountryError"></div>
                    </div>

                    <div class="text-center">
                        <button type="button" id="editCountryButton"
                            class="btn-theme editCountryBtn">{{ __('lang.submit') }}</button>
                        <button type="button" class="btn-theme-border" data-dismiss="modal">{{ __('lang.cancel') }}</button>
                    </div>
                </form>

            </div>

        </div>
  	</div>
</div>

@section('scripts')

<script type="text/javascript">

    /********** add country *********/
        $('.addCountry').on('click', function() {

        var formData = $("#addCountryForm").serialize();
        $( '#addCountryError' ).html( "" );

        var ajaxurl = app_url + "/superadmin/countries" ;
        var type = 'add';
        sendPostRequest(ajaxurl, formData, type);

    });

    //************* edit country modal ************//

    function showAddCountry() {
        $('.errorMsg').html("");
        $('#editCountry').attr("style", "display: none !important");
        $('#addCountry').modal('show');
    }

    /********** edit country *********/
    $(".data-table").on("click", ".editCountry", function() {
            $(".editCountry").click(function () {
                var countryId = $(this).data('id');
                var countryName = $(this).data('country');
                $('#editCountryId').val(countryId);
                $('#countryName').val(countryName);
                $('#editCountry').attr("style", "display: flex !important");
                $('#editCountry').modal('show');

            });
    });

    $('.editCountryBtn').on('click', function() {
         event.preventDefault();
        var formData = $("#editCountryForm").serialize();
        $( '#editCountryError' ).html(" ");

        let ajaxurl = $('.editCountry').attr('data-href');
        let data = $('#editCountryForm').serialize();
        let type = "edit";
        sendPostRequest(ajaxurl, formData, type);

    });

    function sendPostRequest (ajaxurl, formData, type)
    {
        $.ajax({
            url: ajaxurl,
            type:'POST',
            data:formData,
            success:function(data) {
               if(data.success == true){
                    if(type == 'add'){
                        $('#addCountry').modal('hide');
                        $('#countryAlert').append(`<div  class="alert alert-success"><strong>` + "{{ __('lang.country-added') }}" + `</strong></div> `);
                    } else {
                        $('#editCountry').modal('hide');
                        $('#countryAlert').append(`<div  class="alert alert-success"><strong>` + "{{ __('lang.country-updated') }}" + `</strong></div> `);
                    }

                            $('body').find('.piaggio-alert .alert').css('animation' , 'alertIN  ease-in-out .35s forwards , alertOut  ease-in-out  .35s 4s forwards')
                            setTimeout(() => {
                                location.reload()
                            }, 2000);
                } else {
                   $('.errorMsg').html(data.messsage);
                }
            },
            error: function (reject) {
                if( reject.status === 422 ) {
                    var data = $.parseJSON(reject);
                    if(type == 'add'){
                        $("#addCountryError").html(data.errors.name[0]);
                    } else {
                        $("#editCountryError").html(data.errors.countryName[0]);
                    }

                }
            }

        });
    }

    $(document).ready(function (){
        var ajaxUrl = "{{url('superadmin/countries')}}" + window.location.search;
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            cache : false,
            processData: false,
            ajax: ajaxUrl,

                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false },
                    {data: 'name', name: 'name'},
                    {data: 'regions_count', name: 'regions_count'},
                    {data: 'created_by.name', name: 'created_by.name'},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'action', name: 'action', orderable: false},
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
