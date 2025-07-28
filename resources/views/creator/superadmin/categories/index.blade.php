@extends('layouts.app')

@section('content')

<div class="piaggio-alert">
    <div id="categoryAlert"></div>
</div>
<div class="piaggio-alert">
    <div id="userAlert"></div>
</div>

<div class="dash-title container-fluid">
    <b>{{ __('lang.categories') }}</b>
    <button type="submit" data-toggle="modal" data-backdrop="static" data-keyboard="false" class="btn-theme"
        onclick="showAddCategory();">
        + {{ __('lang.add-category') }}
    </button>
</div>
<br>
<div class="container-fluid">
    <div class="white-wrapper">
        <div class="table mt-4">
            <table class="data-table display">
                <h6 class="col-3"><b> {{ __('lang.categories') }} </b></h6>
                <thead>
                    <tr>
                        <th>{{ __('lang.no') }}</th>
                        <th>{{ __('lang.name') }}</th>
                        <th>{{ __('lang.created-by') }}</th>
                        <th width="250px" class="th-action">{{ __('lang.action') }}</th>

                    </tr>
                </thead>
                <tbody>

                </tbody>

            </table>
        </div>

    </div>
</div>

<!-- Add Modal -->
<div class="modal fade custom-model" id="addCategory" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h5 class="modal-title mb-2">{{ __('lang.add-new-category') }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <!-- form -->
                <form action="{{ url('/superadmin/categories/') }}" method="POST" name="addCategoryForm" id="addCategoryForm">
                    @csrf
                    <div class="form-group">
                        <label>{{ __('lang.category-name') }}:</label>
                        <input type="text" name="name" class="form-control" placeholder="{{ __('lang.category-name') }}"
                            value="{{ old('name') }}" required>
                        <div class="errorMsg" id="addCategoryError"></div>
                    </div>
                    <div class="text-center">
                        <button type="button" class="btn-theme addCategory">{{ __('lang.submit') }}</button>
                        <button type="button" class="btn-theme-border" data-dismiss="modal">{{ __('lang.cancel')
                            }}</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<!------ Edit Category----->
<div class="modal fade custom-model" id="editCategory" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                <h5 class="modal-title mb-2">{{ __('lang.edit-category') }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <!-- form -->
                <form action="" method="POST" id="editCategoryForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="editCategoryId" value="">
                    <div class="form-group">
                        <label>{{ __('lang.category-name') }}:</label>
                        <input type="text" name="name" id="categoryName" class="form-control"
                            placeholder="{{ __('lang.category-name') }}" value="{{ old('name') }}" required>
                        <div class="errorMsg" id="editCategoryError"></div>
                    </div>

                    <div class="text-center">
                        <button type="button" id="editCategoryButton" class="btn-theme editCategoryBtn">{{
                            __('lang.submit') }}</button>
                        <button type="button" class="btn-theme-border" data-dismiss="modal">{{ __('lang.cancel')
                            }}</button>
                    </div>
                </form>

            </div>

        </div>
    </div>
</div>
<!-- Modal -->

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.js"></script>

<script type="text/javascript">

    /*********** datatable ***********/
    $(document).ready(function () {
        $('#addCategoryForm').on('keypress', function (e) {
            if (e.which === 13) {
                e.preventDefault();
            }
        });

        var ajaxUrl = "{{ url('superadmin/categories') }}" + window.location.search;
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            cache: false,
            processData: false,

            ajax: ajaxUrl,

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
                {
                    data: 'created_by',
                    name: 'created_by'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
            "searching": false,
            "bLengthChange": false,
            'order': [1, 'asc'],
            "createdRow": function (row) { // check
                $(row).find('td:eq(3)').addClass('flex');
            }
        });
    });

    function showAddCategory() {
        $('.errorMsg').html("");
        $('#editCategory').attr("style", "display: none !important");
        $('#addCategory').modal('show');
    }

    /********** add category *********/
    $('.addCategory').on('click', function () {
        var formData = $("#addCategoryForm").serialize();
        $('#addCategoryError').html("");
        

        var ajaxurl = app_url + "/superadmin/categories";
        var type = 'add';
        sendPostRequest(ajaxurl, formData, type);

    });

    /********** edit category *********/
    $(".data-table").on("click", ".editCategory", function () {
        $(".editCategory").click(function () {
            $('.errorMsg').html("");
            var categoryId = $(this).data('id');
            var categoryName = $(this).data('category');
            $('#editCategoryId').val(categoryId);
            $('#categoryName').val(categoryName);
            $('#editCategory').attr("style", "display: flex !important");
            $('#editCategory').modal('show');

        });
    });

    $('.editCategoryBtn').on('click', function () {
        event.preventDefault();
        var formData = $("#editCategoryForm").serialize();
        $('#editCategoryError').html(" ");

        let ajaxurl = $('.editCategory').attr('data-href');
        let data = $('#editCategoryForm').serialize();
        let type = "edit";
        sendPostRequest(ajaxurl, formData, type);

    });

    function sendPostRequest(ajaxurl, formData, type) {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData,
            success: function (data) {
                if (data.success == true) {
                    if (type == 'add') {
                        $('#addCategory').modal('hide');
                        $('#categoryAlert').append(`<div  class="alert alert-success"><strong>` +
                            "{{ __('lang.category-added') }}" + `</strong></div> `);
                    } else {
                        $('#editCategory').modal('hide');
                        $('#categoryAlert').append(`<div  class="alert alert-success"><strong>` +
                            "{{ __('lang.category-updated') }}" + `</strong></div> `);
                    }

                    $('body').find('.piaggio-alert .alert').css('animation',
                        'alertIN  ease-in-out .35s forwards , alertOut  ease-in-out  .35s 4s forwards')
                    setTimeout(() => {
                        location.reload()
                    }, 2000);
                } else {
                    $('.errorMsg').html(data.messsage.name);
                }
            },
            error: function (reject) {
                if (reject.status === 422) {
                    var data = $.parseJSON(reject);
                    if (type == 'add') {
                        $("#addCategoryError").html(data.errors.name[0]);
                    } else {
                        $("#editCategoryError").html(data.errors.name[0]);
                    }
                }
            }
        });
    }

</script>
@endsection
@endsection