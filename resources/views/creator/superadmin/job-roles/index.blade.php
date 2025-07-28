@extends('layouts.app')

@section('content')
    <div class="piaggio-alert"> 
        <div id="jobRoleAlert"></div>
    </div>
    
    <div class="dash-title container-fluid">
		<b>{{__('lang.job-role')}}</b>
        <button  data-toggle="modal" data-backdrop="static" data-keyboard="false" class="btn-theme" onclick = "showAddJobRole()">+ {{ __('lang.add-job-role') }}</button>
    </div>

  
    <div class="container-fluid">
		<div class="white-wrapper">
                    
            <form class="d-lg-flex justify-content-end align-items-center"  method="GET">
               
                @if(isset($_GET['name']))
                    <input type="text" placeholder="{{__('lang.search-by-name-placeholder')}}" class="w-200 form-control" id="name" name="name" value="{{ $_GET['name'] }}">
                @else
                    <input type="text" placeholder="{{__('lang.search-by-name-placeholder')}}" class="w-200 form-control" id="name" name="name">
                @endif
                <button type="submit" class="btn-theme ml-2">{{ __('lang.search') }}</button>
            </form>

            <div class="table">

            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{__('lang.no')}}</th>
                        <th>{{__('lang.name')}}</th>
                        <th>{{__('lang.description')}}</th>
                        <th width="280px" class="th-action">{{__('lang.action')}}</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
    </table>
            </div>

        </div>
    </div>



<!-- Add Modal -->
<div class="modal fade custom-model" id="addJobRole" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                <h5 class="modal-title mb-4 mt-2">  {{ __('lang.add-job-role') }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button> 
                <!-- form -->
                <form action="{{ url('superadmin/job-roles') }}" method="POST" name="addJobRoleForm" id="addJobRoleForm">
                    @csrf
                    <div class="form-group">
                        <label>{{ __('lang.name') }}:</label>
                        <input type="text" name="name" class="form-control" placeholder="{{ __('lang.name') }}" value="{{ old('name') }}" required>
                        <div class="errorMsg nameError" id="nameError"></div>
                    </div>

                    <div class="form-group">
                        <label>{{ __('lang.description') }}:</label>
                        <input type="text" name="description" class="form-control"  placeholder="{{ __('lang.description') }}"  value="{{ old('description') }}" required>
                        <div class="errorMsg descriptionError" id="descriptionError"></div>
                    </div>

                    <div class="text-center">
                        <button type="button"class="btn-theme addJobRole">{{ __('lang.submit') }}</button>
                        <button type="button" class="btn-theme-border" data-dismiss="modal">{{ __('lang.cancel') }}</button>
                    </div>
                   
                </form>

            
            </div>
        </div>

    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade custom-model" id="editJobRole" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
                
            <div class="modal-body">
                <h5 class="modal-title">{{ __('lang.edit-job-role') }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <!-- form -->
                <form action="" method="POST" id="editJobRoleForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="jobRoleId" value="">
                    <div class="form-group">
                        <label>{{ __('lang.name') }}:</label>
                        <input type="text" name="name" id="jobRoleName" class="form-control JobRoleName" placeholder="{{ __('lang.name') }}" value="" required>
                        <div class="errorMsg nameError" id="nameEditError"></div>    
                    </div>

                    <div class="form-group">
                        <label>{{ __('lang.description') }}:</label>
                        <input type="text" name="description" id="jobRoleDescription" class="form-control JobRoleDescription" placeholder="{{ __('lang.description') }}"  value="" required>
                        <div class="errorMsg descriptionError" id="descriptionEditError"></div> 
                    </div>

                    <div class="text-center">
                        <button type="button" id="editJobRoleButton" class="btn-theme editJobRoleButton">{{ __('lang.submit') }}</button>
                        <button type="button" class="btn-theme-border" data-dismiss="modal">{{ __('lang.cancel') }}</button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>

@section ('scripts')
     
<script>

function showAddJobRole() {
        $('#editJobRole').attr("style", "display: none !important");
        $('#addJobRole').modal('show');
    }
    /********** add group *********/ 
    $('.addJobRole').on('click', function() {

        var formData = $("#addJobRoleForm").serialize();
        $( '#nameError' ).html( "" );
        $( '#descriptionError' ).html( "" );
        var ajaxurl = app_url + "/superadmin/job-roles" ;
        var type = "add";
        sendPostRequest(ajaxurl, formData, type);
    }); 
    
    /************ Edit Job Role *************/
    $(".data-table").on("click", ".editJobRole", function() {
        var jobRoleId = $(this).data('id');
        var jobRoleName = $(this).data('jobrole');
        var jobRoleDescription = $(this).data('description');
        $('#jobRoleId').val(jobRoleId);
        $('#jobRoleName').val(jobRoleName);
        $('#jobRoleDescription').val(jobRoleDescription);
        $('#editJobRole').modal('show');
    });

    $("#editJobRoleButton").click(function(){
        event.preventDefault();
        let ajaxurl = $('.editjobRole').attr('data-href');
        let formData = $('#editJobRoleForm').serialize();
        let type = "edit";
        sendPostRequest(ajaxurl, formData, type);

    });

function sendPostRequest(ajaxurl, formData, type) {
    $.ajax({
            url: ajaxurl,
            type:'POST',
            data:formData,
            success:function(data) {
               if(data.success == true){

                if(type == 'add')
                {
                    $('#addJobRole').modal('hide');
                    $('#jobRoleAlert').append( `<div  class="alert alert-success"><strong>`+  "{{ __('lang.jobrole-added') }}" + `</strong></div> `)
                } else {
                    $('#editJobRole').modal('hide');
                    $('#jobRoleAlert').append( `<div  class="alert alert-success"><strong>`+  "{{ __('lang.jobrole-updated') }}" + `</strong></div> `)
                }   
             
                $('body').find('.piaggio-alert .alert').css('animation' , 'alertIN  ease-in-out .35s forwards , alertOut  ease-in-out  .35s 4s forwards')
                setTimeout(() => {
                    location.reload()
                }, 2000);
               } else {
                    if (typeof data.message['name'] !== 'undefined') {
                                $(".nameError").html(data.message['name']);
                    }
                    if (typeof data.message['description'] !== 'undefined') {
                                $(".descriptionError").html(data.message['description']);
                    }
               }
            },
            error: function (reject) {
                if( reject.status === 422 ) {
                    var data = $.parseJSON(reject.responseText);
                    if(type == 'add')
                    {
                        if (typeof data.errors.name !== 'undefined') {
                            $("#nameError").html(data.errors.name[0]);
                        }    
                        if (typeof data.errors.description !== 'undefined') {
                            $("#descriptionError").html(data.errors.description[0]);
                        }
                    } else {
                        if (typeof data.errors.name !== 'undefined') {
                            $("#nameEditError").html(data.errors.name[0]);
                        }    
                        if (typeof data.errors.description !== 'undefined') {
                            $("#descriptionEditError").html(data.errors.description[0]);
                        }
                    }
                    
                }
            }
        });
}


$(document).ready(function (){

    $ajaxUrl = "{{url('superadmin/job-roles')}}" + window.location.search;
    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        cache : false,
        processData: false,
        ajax: $ajaxUrl,
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false },
                {data: 'name', name: 'name'},
                {data: 'description', name: 'description'},
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

@endsection