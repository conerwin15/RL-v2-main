@extends('layouts.app')

@section('content')

<div class="piaggio-alert">
    <div id="userAlert"></div>
</div>
<div class="dash-title container-fluid">
    <div>
        <b>{{__('lang.manual-email')}}  &gt;</b>
        <span class="bradcrumb">{{__('lang.campaigns')}}</span>
    </div>
    <a class="btn-theme" href="{{ url('/superadmin/mail/send') }}">{{__('lang.send-mail')}}</a>
</div>

<div class="container-fluid">
    <div class="white-wrapper">

        <div class="table mt-4">
            <table  class="data-table display">
                <h6 class="col-3"><b> {{__('lang.scheduled-mails')}} </b></h6>
                <thead>
                    <tr>
                        <th>{{__('lang.no')}}</th>
                        <th>{{ __('lang.name') }}</th>
                        <th>{{ __('lang.status') }}</th>
                        <th>{{ __('lang.scheduled_at') }}</th>
                        <th>{{ __('lang.recurrence') }}</th>
                        <th class="flex">{{ __('lang.action') }}</th>
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
    $(document).ready(function() {

        var ajaxUrl = "{{url('superadmin/scheduled/mails')}}" + window.location.search;
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            cache : false,
            processData: false,
            ajax:  ajaxUrl,

            columns: [

                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false
                },
                {
                    data: 'campaign_name',
                    name: 'campaign_name'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'scheduled_at',
                    name: 'scheduled_at'
                },
                {
                    data: 'recurrence',
                    name: 'recurrence'
                },
                {   data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
            "searching": false,
            "bLengthChange": false,
            'order': [1, 'asc'],
            "createdRow":  function(row) {
                $(row).find('td:eq(5)').addClass('flex');
            }
        });
    });
</script>
@endsection

@endsection