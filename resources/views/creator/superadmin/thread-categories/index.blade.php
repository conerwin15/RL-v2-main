@extends('layouts.app')

@section('content')

<div class="piaggio-alert"> 
    <div id="categoryAlert"></div>
</div>
<div class="dash-title container-fluid max-width">
    <b>{{__('lang.thread-categories')}}</b>
</div>

<div class="container-fluid max-width">
    <div class="white-wrapper">
        <form action="{{ url('/threads/categories') }}" method="POST" name="addGroupForm" id="addCategoryForm">
            @csrf
            <label>{{ __('lang.category-name') }}:</label>
            <div class="flex form-group justify-content-start">
                <input type="text" name="name" class="form-control w-300 mr-2"  placeholder="{{ __('lang.category-name') }}" value="{{ old('name') }}" required>
                <button type="button" class="btn-theme addCategory">{{ __('lang.add-new') }}</button>
            </div>
            <div id="nameError" class="errorMsg"> </div>
               
        </form>
    </div>

    <div class="container-fluid">
	    <div class="white-wrapper">
            <div class="table mt-4">
                <h6 class="col-3"><b>  {{__('lang.all-categories')}} </b></h6>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th> {{__('lang.no')}} </th>
                            <th>{{__('lang.name')}}</th>
                            <th style="text-align:center;">{{__('lang.action')}}</th>
                        </tr>
                    </thead>

                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!------ Edit thread Category ----->

<!-- Modal -->
<div class="modal fade custom-model" id="editCategory" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h5 class="modal-title mb-3">{{ __('lang.edit-thread-category') }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <form action="" method="POST" id="editCategoryForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="categoryId" value="">
                    <div class="form-group">
                        <label>{{ __('lang.category-name') }}:</label>
                        <input type="text" name="name" id="categoryName" class="form-control" placeholder="{{ __('lang.category-name') }}" value="{{ old('name') }}" required>
                        <div class="errorMsg" id="nameEditError">  </div>    
                    </div>

                    <div class="text-center">
                        <button type="button" id="editCategoryButton"  class="btn-theme editCategoryButton">{{ __('lang.submit') }}</button>
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


/********** add thread category *********/ 
        $('.addCategory').on('click', function() {

        var formData = $("#addCategoryForm").serialize();
        $( '.nameError' ).html( "" );
        var ajaxurl = app_url + "/superadmin/threads/categories" ;
        var type = 'add';
        sendPostRequest(ajaxurl, formData, type);
       
    });

 /***********  edit thread cateogry **************/

 $(".data-table").on("click", ".editCategory", function() {
        var categoryId = $(this).data('id');
        var categoryName = $(this).data('name');
        $('#categoryId').val(categoryId);
        $('#categoryName').val(categoryName);
        $('#editCountry').attr("style", "display: flex !important");
        $('#editCountry').modal('show');
});

$("#editCategoryButton").click(function(){
    event.preventDefault();
    let ajaxurl = $('.editCategory').attr('data-href');
    let formData = $('#editCategoryForm').serialize();
    let type = 'update';
    sendPostRequest(ajaxurl, formData, type);

});     

function sendPostRequest(ajaxurl, formData, type)
{
        $.ajax({
            url: ajaxurl,
            type:'POST',
            data:formData,
            success:function(data) {
                if(data.success == true){
                    if(type == 'add'){
                        $('#categoryAlert').append(`<div  class="alert alert-success"><strong>` + "{{ __('lang.thread-category-added') }}" + `</strong></div> `);
                    } else {
                        $('#editCategory').modal('hide');
                        $('#categoryAlert').append(`<div  class="alert alert-success"><strong>` + "{{ __('lang.thread-category-updated') }}"  + `</strong></div> `)
                    } 
                  
                    $('body').find('.piaggio-alert .alert').css('animation' , 'alertIN  ease-in-out .35s forwards , alertOut  ease-in-out  .35s 4s forwards')
                    setTimeout(() => {
                        location.reload()
                    }, 2000);
                } else if(data.success == false){
                    if(type == 'add'){
                        $('#nameError').html(data.messsage.name[0]);
                    } else {
                        $("#nameEditError").html(data.messsage.name[0]);
                    }
                }
            },
            error: function (reject) {
                if( reject.status === 422 ) {
                    var data = $.parseJSON(reject.responseText);
                    if (typeof data.errors.name !== 'undefined') {
                        $(".nameError").html(data.errors.name[0]);
                    }    
                 
                }
            }
        });
}

     /*********** datatable ***********/
     $(document).ready(function (){
        $ajaxUrl = "{{url('superadmin/threads/categories')}}";
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            cache : false,
            processData: false,
            ajax: $ajaxUrl,
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false },
                    {data: 'name', name: 'name'},
                    {data: 'action', name: 'action', orderable: false, searchable: false},

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
