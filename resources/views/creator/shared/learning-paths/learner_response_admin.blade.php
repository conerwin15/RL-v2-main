@extends('layouts.app')


@section('content')

<div class="dash-title container-fluid">
    <div>
        <a href="{{ url($routeSlug .'/learning-paths') }}"><b>{{__('lang.learning-path')}}</b></a>  <b> &gt; </b>
        <span class="bradcrumb">{{__('lang.responses')}}</span>
    </div>
</div>
<a  style="float:right;" class="" href="{{ url('/admin/learner-response/'.$id.'/export?type=excel&region='. $export_regions . '&role=' . $export_roles) }}">
<i class="fa fa-file-excel-o" aria-hidden="true"></i>
{{ __('lang.export-response') }}</a>
<br>
<form class="container-fluid" method="GET">
        <div class="white-wrapper pb-4">
            <div class="row align-items-end">
                <div class="col-sm-12">
                    <h6><b>{{__('lang.filters')}}</b></h6>
                </div>

                    <div class="col-sm-2">
                        <label>{{__('lang.region')}}: </label>
                        <select  name="filter_region" id="country"  class="form-control select"  required>
                            <option value="-1"> {{__('lang.all-regions')}}</option>
                            @foreach ($regions as $region)
                                <option value="{{$region->id}}"  {{ @$_GET['filter_region'] == $region->id ? 'selected' : '' }}>{{ $region->name }}</option>
                            @endforeach
                        </select>
                    </div>

                <div class="col-sm-2">
                    <label>{{ __('lang.role') }}:</label>
                    <select name="filter_role" class="form-control select" required>
                        <option value="-1"> {{__('lang.all')}} </option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" id="roleId"
                                {{ @$_GET['filter_role'] == $role->id ? 'selected' : '' }}>
                                {{ ucfirst(toRoleLabel($role->name)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                @if(isset($_GET['search']))
					<input type="text"  placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-controller form-control mb-0"  id="search" name="search" value="{{$_GET['search']}}">
				@else
					<input type="text"  placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-controller form-control mb-0" id="search" name="search">
				@endif
                <div class="col">
                    <label for=""><br></label><br>
                    <button type="submit" class="btn-theme">{{ __('lang.apply-filter') }}</button>
                </div>
            </div>
        </div>
    </form>
<div class="container-fluid max-width">

    <div class="white-wrapper pt-0 pb-0 table">
        <table class="table " >
            <thead>
                <tr>
                    <th>{{__('lang.no')}}</th>
                    <th>{{__('lang.name')}}</th>
                    <th>{{__('lang.dealer')}}</th>
                    <th>{{__('lang.group')}}</th>
                    <th>{{__('lang.responses')}}</th>
                </tr>
            </thead>

            <tbody>
                @php
                $index = 0
                @endphp
                @if(count($responses)>0)
                @foreach ($responses as $response)
                    <tr>
                        <td>{{++ $index}}</td>
                        <td>{{ucfirst($response->learner->name)}}</td>
                        <td>{{$response->learner->dealer ? ucfirst($response->learner->dealer->name) : 'N/A'}}</td>
                        <td>{{$response->learner->group ? $response->learner->group->name : 'N/A'}}</td>
                        @php
                            if(isset($response->cmi_data["interactions"])) {
                                $interactions = $response->cmi_data["interactions"];
                            } else {
                                $interactions = [];
                            }
                        @endphp
                        <td>
                            @if($interactions)
                                <table style="border:0px">
                                    @foreach ($interactions["childArray"] as $interaction)
                                        <tr>
                                            <td>{{$interaction["id"]}},</td>
                                            <td>{{$interaction["student_response"]}}</td>
                                            <td>{{$interaction["result"]}}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            @endif
                        </td>
                    </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="5" style="text-align: center;">{{__('lang.no-record')}} </td>
                </tr>
                @endif

                </tbody>
        </table>
    </div>
</div>


 {!! $responses->links() !!}
@endsection
