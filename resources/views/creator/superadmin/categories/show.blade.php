@extends('layouts.app')
@section('content')

@php $index = 1; @endphp
<div class="piaggio-alert">
    <div id="categoryAlert"></div>
</div>

<div class="dash-title container-fluid">
<div class="dash-title container-fluid mt-3">
        <div>
            <a href="{{ url('/superadmin/categories') }}"><b>{{ __('lang.category') }} &gt;</b></a>
            <span class="bradcrumb">{{ __('lang.sub-category') }}</span>
        </div>
    </div>
    <button type="submit" data-toggle="modal" data-backdrop="static" data-keyboard="false" class="btn-theme"
        onclick="showAddSubCategory();">
        + {{__('lang.add-sub-category')}}
    </button>
</div>


<div class="container-fluid">
    <div class="white-wrapper">
        <div class="table">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('lang.no') }}</th>
                        <th>{{ __('lang.name') }}</th>
                        <th>{{ __('lang.created-by') }}</th>
                        <th width="280px">{{ __('lang.action') }}</th>
                    </tr>
                </thead>

                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!---  Add region modal ----->
<div class="modal fade custom-model" id="addSubCat" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h5 class="modal-title mb-2">{{ __('lang.add-sub-category') }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <form action="{{ url('/superadmin/regions') }}" method="POST" id="addSubCatForm">
                    @csrf
                    <input type="hidden" name="category" value="{{$id}}">
                    <div class="form-group">
                        <label>{{ __('lang.name') }}:</label>
                        <input type="text" name="name" class="form-control" placeholder="{{ __('lang.name') }}"
                            value="{{ old('name') }}" required>
                        <div class="errorMsg" id="addSubCatError"></div>
                    </div>
                    <div class="text-center">
                        <button type="button" class="btn-theme addSubCat">{{ __('lang.submit') }}</button>
                        <button type="button" class="btn-theme-border" data-dismiss="modal">{{ __('lang.cancel')
                            }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>


<!---- Edit region modal ---->
<div class="modal fade custom-model" id="editSubCat" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h5 class="modal-title mb-2">{{ __('lang.edit-sub-category') }}</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <form action="" method="POST" id="editSubCatForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" id="catId" value="">
                    <div class="form-group">
                        <label>{{ __('lang.name') }}:</label>
                        <input type="text" name="name" id="catName" class="form-control"
                            placeholder="{{ __('lang.sub-category-name') }}" value="{{ old('name') }}" required>
                        <div class="errorMsg" id="editSubCatError"></div>
                    </div>
                    <div class="text-center">
                        <button type="button" class="btn-theme editSubCatButton" id="editSubCatButton">{{
                            __('lang.submit') }}</button>
                        <button type="button" class="btn-theme-border" data-dismiss="modal">{{ __('lang.cancel')
                            }}</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>


@section('scripts')
<script type="text/javascript">

    function showAddSubCategory() {
        $('#editSubCat').attr("style", "display: none !important");
        $('#addSubCat').modal('show');
    }

    /********** add region *********/
    $('.addSubCat').on('click', function () {

        var formData = $("#addSubCatForm").serialize();
        $('#addSubCatError').html("");

        var ajaxurl = app_url + "/superadmin/categories";
        var type = "add";
        sendPostRequest(ajaxurl, formData, type);
    });


    //************* edit sub category modal ************//
    $(".data-table").on("click", ".editSubCat", function () {
        var catId = $(this).data('id');
        var catName = $(this).data('name');
        $('#catId').val(catId);
        $('#catName').val(catName);
        $("#editSubCatError").html(" ");
        $('#editSubCat').modal('show');

    });

    $(".editSubCatButton").click(function () {
        event.preventDefault();
        let ajaxurl = $('.editSubCat').attr('data-href');
        let formData = $('#editSubCatForm').serialize();
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
                        $('#addSubCat').modal('hide');
                        $('#categoryAlert').append(`<div  class="alert alert-success"><strong>` + "{{ __('lang.category-added') }}" + `</strong></div> `);
                    } else {
                        $('#editSubCat').modal('hide');
                        $('#categoryAlert').append(`<div  class="alert alert-success"><strong>` + "{{ __('lang.category-updated') }}" + `</strong></div> `);
                    }
                    $('body').find('.piaggio-alert .alert').css('animation', 'alertIN ease-in-out .35s forwards , alertOut ease-in-out .35s 4s forwards')
                    setTimeout(() => {
                        location.reload()
                    }, 2000);
                } else {
                    $(".errorMsg").html(data['message']);
                }
            },
            error: function (reject) {
                if (reject.status === 422) {
                    var data = $.parseJSON(reject.responseText);
                    if (type == 'add') {
                        $("#addRegionError").html(data.errors.name[0]);
                    } else {
                        $("#editRegionError").html(data.errors.countryName[0]);
                    }
                }
            }
        });
    }


    $(document).ready(function () {
        $('#addSubCat').on('keypress', function (e) {
            if (e.which === 13) {
                e.preventDefault();
            }
        });
        var ajaxUrl = "{{url('superadmin/categories/'.$id)}}" + window.location.search;
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            cache: false,
            processData: false,
            ajax: ajaxUrl,

            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false },
                { data: 'name', name: 'name' },
                { data: 'created_by', name: 'created_by' },
                { data: 'action', name: 'action', orderable: false },
            ],

            'searching': false,
            'lengthChange': false,
            'order': [1, 'asc'],
            "createdRow": function (row) {
                $(row).find('td:eq(4)').addClass('flex');
            }
        });
    });
</script>
@endsection
@endsection
