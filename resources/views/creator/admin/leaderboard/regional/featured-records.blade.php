@extends('layouts.app')

@section('content')

    <div class="container-fluid max-width">
    <div class="dash-title container-fluid">
            <div>
                <a href="{{ url( 'admin/regional/leaderboard') }}"><b>{{ __('lang.leaderboard') }} &gt;</b></a>
                <span class="bradcrumb">{{ __('lang.featured-record') }} ({{__('lang.regional') }}) </span>
            </div>
    </div>
    <div class="white-wrapper">

        <div class="container-fluid max-width">
            <table class="table leaderboard-table">

                    <thead style="background-color: #388FB5;color: #fff;">
                        <tr>
                            <th>{{ __('lang.featured-employee') }}</th>
                            <th>{{ __('lang.country') }}</th>
                            <th>{{ __('lang.dealer') }}</th>
                            <th>{{ __('lang.region') }}</th>
                            <th>{{ __('lang.featured-month') }}</th>
                            <th>{{ __('lang.text') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                    @if(count($featuredRecords) > 0)
                        @foreach ($featuredRecords as $featuredRecord)
                            <tr>
                                <td>{{$featuredRecord->user->name}}</td>
                                <td>{{$featuredRecord->user->country->name}}</td>
                                <td>{{$featuredRecord->user->dealer_id ? $featuredRecord->user->getNameById($featuredRecord->user->dealer_id) : 'N/A'}}</td>
                                <td>{{$featuredRecord->user->region->name}}</td>
                                <td>{{date("F Y", strtotime($featuredRecord->created_at))}}</td>
                                <td>{{$featuredRecord->featured_text}}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="9" style="text-align: center;">{{ __('lang.no-record') }} </td>
                        </tr>

                    @endif

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{ $featuredRecords->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}

@endsection
