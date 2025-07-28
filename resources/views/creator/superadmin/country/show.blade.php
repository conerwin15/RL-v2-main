@extends('layouts.app')
@section('content')

@php$index = 1; @endphp
    <div class="piaggio-alert">
        <div id="regionAlert"></div>
    </div>

    <div class="dash-title container-fluid">
        <div>
            <a href="{{ url('/superadmin/countries') }}"><b>{{ __('lang.countries') }} &gt;</b></a> <a
                href="{{ url('/superadmin/countries') }}"><b>{{ $country->name }} &gt;</b></a>
            <span class="bradcrumb">{{ __('lang.regions') }}</span>
        </div>
        <button type="submit" data-toggle="modal" data-backdrop="static" data-keyboard="false" class="btn-theme"
              onclick="showAddRegion();">
            + {{ __('lang.add-new-region') }}
        </button>
    </div>


    <div class="container-fluid">
        <div class="white-wrapper">
            <div class="table">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{ __('lang.no') }}</th>
                            <th>{{ __('lang.region-name') }}</th>
                            <th>{{ __('lang.created-by') }}</th>
                            <th>{{ __('lang.created-on') }}</th>
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
    <div class="modal fade custom-model" id="addRegion" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <h5 class="modal-title mb-2">{{ __('lang.add-new-region') }}</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <form action="{{ url('/superadmin/regions') }}" method="POST" id="addRegionForm">
                        @csrf
                        <input type="hidden" name="country" value="{{ $country->id }}">
                        <div class="form-group">
                            <label>{{ __('lang.region') }}:</label>
                            <input type="text" name="name" class="form-control"
                                placeholder="{{ __('lang.name') }}"
                                value="{{ old('name') }}" required>
                            <div class="errorMsg" id="addRegionError"></div>
                        </div>
                        <div class="text-center">
                            <button type="button"
                                class="btn-theme addRegion">{{ __('lang.submit') }}</button>
                            <button type="button" class="btn-theme-border" data-dismiss="modal">{{ __('lang.cancel') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>


    <!---- Edit region modal ---->
    <div class="modal fade custom-model" id="editRegion" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <h5 class="modal-title mb-2">{{ __('lang.edit-region') }}</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <form action="" method="POST" id="editRegionForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="id" id="regionId" value="">
                        <input type="hidden" name="country" value="{{ $country->id }}">
                        <div class="form-group">
                            <label>{{ __('lang.region-name') }}:</label>
                            <input type="text" name="name" id="regionName" class="form-control"
                                placeholder="{{ __('lang.region-name') }}"
                                value="{{ old('name') }}" required>
                            <div class="errorMsg" id="editRegionError"></div>
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn-theme editCountryButton"
                                id="editCountryButton">{{ __('lang.submit') }}</button>
                            <button type="button" class="btn-theme-border" data-dismiss="modal">{{ __('lang.cancel') }}</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>


   @section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.2.2/jquery.form.js"></script>
<script type="text/javascript">

    function showAddRegion() {
        $('#editRegion').attr("style", "display: none !important");
        $('#addRegion').modal('show');
    }

    // ********** Add Region **********
    $('.addRegion').on('click', function () {
        var name = $('input[name="name"]').val().trim();
        $('#addRegionError').html("");

        if (name.length < 4) {
            $('#addRegionError').html("Region name must be at least 4 characters.");
            return;
        }

        var formData = $("#addRegionForm").serialize();
        var ajaxurl = app_url + "/superadmin/regions";
        var type = "add";
        sendPostRequest(ajaxurl, formData, type);
    });

    // ********** Edit Region Modal Open **********
    $(".data-table").on("click", ".editRegion", function() {
        var regionId = $(this).data('id');
        var regionName = $(this).data('region');
        $('#regionId').val(regionId);
        $('#regionName').val(regionName);
        $("#editRegionError").html("");
        $('#editRegion').modal('show');
    });

    // ********** Edit Region Submit **********
    $(".editCountryButton").click(function (event) {
        event.preventDefault();
        var name = $('#regionName').val().trim();
        $("#editRegionError").html("");

        if (name.length < 4) {
            $("#editRegionError").html("Region name must be at least 4 characters.");
            return;
        }

        let ajaxurl = $('.editRegion').attr('data-href');
        let formData = $('#editRegionForm').serialize();
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
                        $('#addRegion').modal('hide');
                        $('#regionAlert').append(`<div class="alert alert-success"><strong>{{ __('lang.region-added') }}</strong></div>`);
                    } else {
                        $('#editRegion').modal('hide');
                        $('#regionAlert').append(`<div class="alert alert-success"><strong>{{ __('lang.region-updated') }}</strong></div>`);
                    }

                    $('body').find('.piaggio-alert .alert').css('animation', 'alertIN ease-in-out .35s forwards, alertOut ease-in-out .35s 4s forwards');

                    setTimeout(() => {
                        location.reload();
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
                        $("#editRegionError").html(data.errors.countryName[0] || data.errors.name[0]);
                    }
                }
            }
        });
    }

    $(document).ready(function () {
        var ajaxUrl = "{{ url('superadmin/countries/' . $countryId) }}" + window.location.search;
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
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false },
            ],

            searching: false,
            lengthChange: false,
            order: [1, 'asc'],
            createdRow: function (row) {
                $(row).find('td:eq(5)').addClass('flex');
            }
        });
    });
</script>
@endsection
    @endsection
