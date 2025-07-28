@extends('layouts.app')

@section('content')

<div class="piaggio-alert">
    <div id="userAlert"></div>
</div>
<div class="dash-title container-fluid">
    <div>
    <a href="{{ url('/superadmin/scheduled/mails') }}"><b>{{__('lang.campaigns')}}  &gt;</b></a>
        <span class="bradcrumb">{{__('lang.view-campaign')}}</span>
    </div>
</div>

<div class="container-fluid">
    <div class="white-wrapper">
        <div class="table mt-4">
            <table  class="data-table display">
                <h6 class="col-3"><b> {{__('lang.learners')}} </b></h6>
                <thead>
                    <tr>
                        <th>{{__('lang.s-no')}}</th>
                        <th>{{__('lang.learner-name')}}</th>
                        <th>{{__('lang.email')}}</th>
                        <th>{{__('lang.country')}}</th>
                        <th>{{__('lang.region')}}</th>
                        <th>{{__('lang.role')}}</th>
                        <th>{{__('lang.dealer')}}</th>
                        <th>{{__('lang.job-role')}}</th>
                        <th>{{__('lang.group')}}</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="white-wrapper wrapper2">
    <div class="container-fluid max-width user-info">

        <div class="row">
            <div class="col-sm-6">
                <div class="d-flex">
                    <label>{{ __('lang.campaign-name') }}:</label>
                    <div class="user-label">{{ $scheduleJob->name }}</div>
                </div>
                
                <div class="d-flex">
                    <label>{{ __('lang.start-date') }}:</label>
                    <div class="user-label">{{ $startDate }}</div>
                </div>

                @if($scheduleJob->frequency == 'every')
                    <div class="d-flex">
                        <label>{{ __('lang.every') }} X:</label>
                        <div class="user-label">{{ ucfirst($scheduleJob->frequency_unit) }}(s)</div>
                    </div>
                @endif    
            </div>
        
            <div class="col-sm-6">
                 <div class="d-flex">
                    <label>{{ __('lang.recurrence') }}:</label>
                    <div class="user-label">{{ $scheduleJob->frequency == 'every' ? 'Repeat' : 'Once'}}</div>
                </div>

                @if($scheduleJob->frequency == 'every')
                    <div class="d-flex">
                        <label>{{ __('lang.end-date') }}:</label>
                        <div class="user-label">
                        {{ $endRunAt }}
                        </div>
                    </div>

                    <div class="d-flex">
                        <label>X:</label>
                        <div class="user-label">
                        {{ $scheduleJob->frequency_amount }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

            <div class="col-12 col-md-7">
                <label>{{ __('lang.subject') }}:</label>
                <input class="form-control" name="subject" placeholder="{{ __('lang.subject') }}" value="{{$scheduleJob->subject}}" readonly>
            </div>

            <div class="col-12 col-md-7">
                <label>{{ __('lang.mail-content') }}:</label>
                <textarea class="form-control" rows="4" name="description" placeholder="{{ __('lang.description') }}" maxlength = "1200" readonly> {{$scheduleJob->description}}</textarea>
            </div>
    </div>
</div>

@section('scripts')
<script>
    /*********** datatable ***********/
    $(document).ready(function() {

        var Id = '{{$id}}';
        var ajaxUrl = "{{url('superadmin/scheduled/mail/')}}" + '/' + Id + window.location.search;
        var table = $('.data-table').DataTable({
            processing: true,
            serverSide: true,
            cache : false,
            processData: false,
            ajax:  ajaxUrl,

            columns: [

                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false },
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'country', name: 'country'},
                {data: 'region', name: 'region'},
                {data: 'role', name: 'role'},
                {data: 'dealer', name: 'dealer'},
                {data: 'jobRole', name: 'jobRole'},
                {data: 'group', name: 'group'},
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