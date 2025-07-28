@extends('layouts.app')

@section('content')

<div class="piaggio-alert">
        <div id="regionAlert"></div>
</div>

<div class="dash-title container-fluid">
    <b>{{ __('lang.regions') }}</b>
        <button type="submit" class="btn-theme" data-backdrop="static" data-keyboard="false" class="btn btn-success" onclick="showAddRegion();">
        + {{ __('lang.create-region') }}
        </button>

</div>


<div class="container-fluid">
    <div class="white-wrapper">
        <div class="row justify-content-between">
            <div class="col-sm-3">
                <form  class="d-lg-flex justify-content-end align-items-center"  method="GET">
                    <select name="country" required class="form-control select">
                        <option value="-1">{{ __('lang.all') }} </option>
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" id="countryId"
                                {{ @$_GET['country'] == $country->id ? "selected" : '' }}>
                                {{ $country->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-theme ml-2">{{ __('lang.search') }}</button>

                </form>
            </div>
            <div class="col-sm-4">
                <form class="d-lg-flex justify-content-end align-items-center" method="GET">
                    @if(isset($_GET['name']))
                        <input type="text" placeholder="{{__('lang.search-by-region-name-placeholder')}}" class="form-control" id="search" name="name"
                            value="{{ $_GET['name'] }}">
                    @else
                        <input type="text" placeholder="{{__('lang.search-by-region-name-placeholder')}}" class="form-control" id="search" name="name">
                    @endif
                    <button type="submit" class="btn-theme ml-2">{{ __('lang.search') }}</button>

                </form>
            </div>
        </div>
        <div class="table">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('lang.no') }}</th>
                        <th>{{ __('lang.country') }}</th>
                        <th>{{ __('lang.region') }}</th>
                        <th width="280px" class="th-action">{{ __('lang.action') }}</th>
                    </tr>
                </thead>

                <tbody>
                </tbody>
            </table>

        </div>
    </div>
</div>

    </div>


    <!-- Modal -->
  <div class="modal fade custom-model" id="addRegion" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">{{__('lang.add-new-region')}}</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <!-- form -->
        <form action="{{ url('/superadmin/regions/') }}" method="POST" id="addRegionForm">
        @csrf
        <div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                <strong>{{__('lang.select-country')}}:</strong>
                        <select  name="country" class="form-control select" required>
                            @foreach ($countries as $country)
                            <option value="{{$country->id}}" id="countryId" >{{ $country->name }}</option>
                            @endforeach
                        </select>
                </div>
            </div>

            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>{{__('lang.region-name')}}:</strong>
                    <input type="text" name="name" class="form-control" placeholder="{{__('lang.region-name')}}" value="{{ old('name') }}" required>

                     <div class="errorMsg" id="addRegionError"></div>

                </div>
            </div>

            <div class="text-center">
				<button type="button" class="btn-theme addRegion">{{ __('lang.submit') }}</button>
				<button type="button" class="btn-theme-border" data-dismiss="modal">{{ __('lang.cancel') }}</button>
			</div>
        </div>
        </form>

        </div>
    </div>

    </div>
  </div>

   <!------ Edit Country----->

    <!-- Modal -->
    <div class="modal fade" id="editRegion" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">{{__('lang.edit-region')}}</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
            <!-- form -->
        <form action="" method="POST"  id="editRegionForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="id" id="editRegionId" value="">
        <div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                <strong>{{__('lang.select-country')}}:</strong>
                        <select  name="country" id="country" class="form-control select" required>
                            @foreach ($countries as $country)
                            <option value="{{$country->id}}" id="countryId" >{{ $country->name }}</option>
                            @endforeach
                        </select>
                </div>
            </div>
            <div class="col-xs-12 col-sm-12 col-md-12">
                <div class="form-group">
                    <strong>{{__('lang.region-name')}}:</strong>
                    <input type="text" name="name" id="regionName" class="form-control" placeholder="{{__('lang.region-name')}}" value="{{ old('name') }}" required>
                      <div class="errorMsg nameError" id="editRegionError"></div>
                </div>
            </div>

            <div class="text-center">
				<button type="button" id="editRegionButton" class="btn-theme editRegionBtn">{{ __('lang.submit') }}</button>
				<button type="button" class="btn-theme-border" data-dismiss="modal">{{ __('lang.cancel') }}</button>
			</div>

        </div>
        </form>

        </div>

      </div>

    </div>
  </div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.js"></script>
<script type="text/javascript">

    /********** add region *********/
        $('.addRegion').on('click', function() {

        var formData = $("#addRegionForm").serialize();
        $( '#addRegionError' ).html( "" );

        var ajaxurl = app_url + "/superadmin/regions" ;
        var type = "add";
        sendPostRequest(ajaxurl, formData, type);

    });



    //************* edit region modal ************//
    $(".data-table").on("click", ".editRegion", function() {
        $('#editRegionId').val($(this).data('id'));
        $('#regionName').val($(this).data('region'));
        $('#editRegion').modal('show');
    });

    function showAddRegion() {
        $(".errorMsg").html("");
        $('#editRegion').attr("style", "display: none !important");
        $('#addRegion').modal('show');
    }
     /********** edit region *********/
     $('.editRegionBtn').on('click', function() {
        $(".errorMsg").html("");
        event.preventDefault();
        var formData = $("#editRegionForm").serialize();
        $( '#editRegionError' ).html(" ");

        let ajaxurl = $('.editRegion').attr('data-href');
        let data = $('#editRegionForm').serialize();
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
                        $('#addRegion').modal('hide');
                        $('#regionAlert').append(`<div  class="alert alert-success"><strong>` + "{{ __('lang.region-added') }}" + `</strong></div> `);
                    } else {
                        $('#editRegion').modal('hide');
                        $('#regionAlert').append(`<div  class="alert alert-success"><strong>` + "{{ __('lang.region-updated') }}" + `</strong></div> `);
                    }
                            $('body').find('.piaggio-alert .alert').css('animation' , 'alertIN ease-in-out .35s forwards , alertOut ease-in-out .35s 4s forwards')
                            setTimeout(() => {
                            location.reload()
                            }, 2000);
               } else {
                    if (typeof data.message['name'] !== 'undefined') {
                         $(".nameError").html(data.message['name']);
                    } else {
                         $('.errorMsg').html(data.message);
                    }
               }
            },
            error: function (reject) {
                if( reject.status === 422 ) {
                    var data = $.parseJSON(reject.responseText);
                    if(type == 'add'){
                        $("#addRegionError").html(data.errors.name[0]);
                    } else {
                        $("#editRegionError").html(data.errors.countryName[0]);
                    }


                }
            }
        });
    }

    $(document).ready(function (){

        $ajaxUrl = "{{url('superadmin/regions')}}" + window.location.search;
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            cache : false,
            processData: false,
            ajax: $ajaxUrl,
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false },
                    {data: 'country', name: 'country'},
                    {data: 'name', name: 'name'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},

                ],
                'searching': false,
                'lengthChange': false,
                'order': [1, 'asc'],
                "createdRow": function( row ) {
                    $(row).find('td:eq(3)').addClass('flex');
                }

        });
    });
</script>
@endsection
