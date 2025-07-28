@extends('layouts.app')
@section('content')

    <div class="piaggio-alert">
        <div id="jobRoleAlert"></div>
    </div>
    <div class="dash-title container-fluid">
		<b>{{__('lang.learning-path-resources')}}</b>
    </div>

    <div class="container-fluid">
		<div class="white-wrapper">
            <div class="table">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>{{__('lang.no')}}</th>
                            <th>{{__('lang.name')}}</th>
                            <th>{{__('lang.completion-rate-of-scorm-file')}}</th>
                            <th>{{__('lang.start-date-scorm-file')}}</th>
                            <th>{{__('lang.end-date-scorm-file')}}</th>
                            <th>{{__('lang.engagement-time')}}</th>
                            <th>{{__('lang.quiz-score')}}</th>
                            <th style="text-align:center;">{{__('lang.progress')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

@section ('scripts')
<script>

$(document).ready(function (){
    $ajaxUrl = "{{url($routeSlug .'/learning-paths/resource/'.$userId.'/'.$learningPathId.'/progress')}}" + window.location.search;
    var table = $('.data-table').DataTable({
        processing: true,
        serverSide: true,
        cache : false,
        processData: false,
        ajax: $ajaxUrl,
            columns: [
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false },
                {data: 'title', name: 'title'},
                {data: 'rate', name: 'rate'},
                {data: 'start_date', name: 'start_date'},
                {data: 'end_date', name: 'end_date'},
                {data: 'duration', name: 'duration'},
                {data: 'score', name: 'score'},
                {data: 'action', name: 'progress'},

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