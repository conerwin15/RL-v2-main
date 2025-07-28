@extends('layouts.app')

@section('content')

<div class="piaggio-alert"> 
    <div id="groupAlert"></div>
</div>
<div class="dash-title container-fluid">
    <b>{{__('lang.groups')}}</b>
    <button data-toggle="modal" data-backdrop="static" data-keyboard="false"  class="btn-theme" onclick="showAddGroup();" >+ {{ __('lang.add-new-group') }}</button>
</div>

<div class="container-fluid">
    <div class="white-wrapper">
        <form class="d-lg-flex justify-content-end align-items-center"  method="GET" style="margin-left: 70%">
            @if(isset($_GET['name']))
            <input type="text" placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-control" id="search" name="name" value="{{ $_GET['name'] }}">
            @else
            <input type="text" placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-control" id="search" name="name">
            @endif
            <button type="submit" class="btn-theme ml-2">{{ __('lang.search') }}</button>
        </form>
        <div class="table">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('lang.no') }}</th>
                        <th>{{ __('lang.name') }}</th>
                        <th>{{ __('lang.description') }}</th>
                        <th width="280px" class="th-action">{{ __('lang.action') }}</th>
                    </tr>
                </thead>
                    <tbody>
                    </tbody>
            </table>
        </div>
    </div>
</div>



<!-- Modal -->
<div class="modal fade custom-model" id="addGroup" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h5 class="modal-title mb-3">{{ __('lang.add-new-group') }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <!-- form -->
                <form action="{{ url('superadmin/groups') }}" method="POST" name="addGroupForm" id="addGroupForm">
                    @csrf
                    <div class="form-group">
                        <label>{{ __('lang.group-name') }}:</label>
                        <input type="text" name="name" class="form-control" placeholder="{{ __('lang.group-name') }}" value="{{ old('name') }}" required>
                        <div class="errorMsg nameError" id="nameError"> </div>
                    </div>
                    <div class="form-group">
                        <label>{{ __('lang.description') }}:</label>
                        <input type="text" name="description" class="form-control" placeholder="{{ __('lang.description') }}"  value="{{ old('description') }}" required>
                        <div class="errorMsg descriptionError" id="descriptionError"> </div>
                    </div>

                    <div class="text-center">
                        <button type="button" class="btn-theme addGroup">{{ __('lang.submit') }}</button>
                        <button type="button" class="btn-theme-border" data-dismiss="modal">{{ __('lang.cancel') }}</button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>


<!------ Edit Country----->

<!-- Modal -->
<div class="modal fade custom-model" id="editGroup" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h5 class="modal-title mb-3">{{ __('lang.edit-group') }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <!-- form -->
                <form action="" method="POST" id="editGroupForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="groupId" value="">
                    <div class="form-group">
                        <label>{{ __('lang.group-name') }}:</label>
                        <input type="text" name="name" id="groupName" class="form-control" placeholder="{{ __('lang.group-name') }}" value="{{ old('name') }}" required>
                        <div class="errorMsg nameError" id="nameEditError">  </div>    
                    </div>

                    <div class="form-group">
                        <label>{{ __('lang.description') }}:</label>
                        <input type="text" name="description" id="groupDescription" class="form-control" placeholder="{{ __('lang.description') }}"  value="{{ old('description') }}" required>
                        <div class="errorMsg descriptionError" id="descriptionEditError"> </div> 
                    </div>

                    <div class="text-center">
                        <button type="button" id="editGroupButton"  class="btn-theme editGroupButton">{{ __('lang.submit') }}</button>
                        <button type="button" class="btn-theme-border" data-dismiss="modal">{{ __('lang.cancel') }}</button>
                    </div>
                </form>

            </div>
        </div>

    </div>
</div>

</div>

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.js"></script>
<script type="text/javascript">

function showAddGroup() {
        $('#editGroup').attr("style", "display: none !important");
        $('#addGroup').modal('show');
    }
/********** add group *********/ 
    $('.addGroup').on('click', function() {
        var formData = $("#addGroupForm").serialize();
        $( '.nameError' ).html( "" );
        $( '.descriptionError' ).html( "" );
       
        var ajaxurl = app_url + "/superadmin/groups" ;
        var type = "add";
       sendPostRequest(ajaxurl, formData, type);
    });

/***********  edit group **************/
$(".data-table").on("click", ".editGroup", function() {
    var groupId = $(this).data('id');
    var groupName = $(this).data('group');
    var groupDescription = $(this).data('description');
    $('#groupId').val(groupId);
    $('#groupName').val(groupName);
    $('#groupDescription').val(groupDescription);
    $(".nameError").html("");
    $(".descriptionError").html("");
    $('#editGroup').modal('show');
});

$("#editGroupButton").click(function(){
    event.preventDefault();
    let ajaxurl = $('.editGroup').attr('data-href');
    let formData = $('#editGroupForm').serialize();
    $( '.nameError' ).html( "" );
    $( '.descriptionError' ).html( "" );
    let type = "edit";

    sendPostRequest(ajaxurl, formData, type);
   
});     

function sendPostRequest(ajaxurl, formData, type){

    $.ajax({
            url: ajaxurl,
            type:'POST',
            data:formData,
            success:function(data) {
                if(data.success == true){

                    if(type == 'add'){
                        $('#addGroup').modal('hide');
                        $('#groupAlert').append(`<div  class="alert alert-success"><strong>` + "{{ __('lang.group-added') }}"+ `</strong></div> `)
                    } else {
                        $('#editGroup').modal('hide');
                        $('#groupAlert').append(`<div  class="alert alert-success"><strong>` + "{{ __('lang.group-updated') }}"+ `</strong></div> `)
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
                    if(type == 'add') {
                        if (typeof data.errors.name !== 'undefined') {
                            $("#nameError").html(data.errors.name[0]);
                        }    
                        if (typeof data.errors.description !== 'undefined') {
                            $("#descriptionError").html(data.errors.description[0]);
                        } 
                    } else 
                    {
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

    $ajaxUrl = "{{url('superadmin/groups')}}" + window.location.search;
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
