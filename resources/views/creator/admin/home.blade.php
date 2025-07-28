@extends('layouts.app')
@section('content')

@php $index = 0; @endphp
</main>
<div class="dashboard">
    <div class="container-fluid max-width">
        <div class="title">
            <span>{{ __('lang.dashboard') }}</span>
        </div>
    </div>
</div>

<script>

    function doThis(title, buttonId, value, checkboxId) {
        let allSelected = $('.' + checkboxId + ':checked');
        let selectedLength = allSelected.length;
        if(selectedLength > 0) {
            if(selectedLength == 1) {
                $('#' + buttonId).html(allSelected[0].dataset.name + '<span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>');
            } else {
                $('#' + buttonId).html(selectedLength + ' Selected' + '<span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>');
            }
        } else {
            $('#' + buttonId).html(title + '<span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>');
        }
    }
</script>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-3">
            <p style="margin-right: 20px; font-weight: bold; padding-top: 5px; color: gray;">{{__('lang.filter')}} {{__('lang.learners')}}</p>
        </div>
        <div class="col-sm-9">
            <form method="GET" class="form-flex">

                        <!-- dealer -->
                        <div>
                            <button  id="dealer-checkbox-select" onclick="showHideDropdown(event, 'dealer-checkbox')" class="select form-control" type="button" id="dealer" style="text-align: inherit;  padding-right: 20px;" required>
                            {{__('lang.select-dealer')}} <span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>
                            </button>

                            <div class="shadow rounded menu" id="dealer-checkbox" style="OVERFLOW-Y:scroll; position: absolute; background: white; z-index: 1;  HEIGHT: 300px; display:none; padding: 5 0 0 5; margin-top: 2px;">
                                @foreach($dealers as $dealer)
                                    <span class="d-block menu-option dashboard-checkbox">
                                        <label>
                                            <input type="checkbox" class="dealer-checkbox" onclick="doThis('{{__('lang.select-dealer')}}', 'dealer-checkbox-select', this.dataset.name, 'dealer-checkbox')" data-name="{{ $dealer->name }}"
                                            name="filter_dealer[]" value="{{$dealer->id}}" {{ ($export_dealers == 0) ? '' : (in_array($dealer->id, explode(',', $export_dealers)) ?  'checked="checked"' : '') }} >&nbsp;
                                            {{ $dealer->name }}
                                        </label>
                                    </span>
                                    @if(in_array($dealer->id, explode(',', $export_dealers)))
                                        <script>
                                            doThis('{{__('lang.select-dealer')}}', 'dealer-checkbox-select', '{{ $dealer->name }}', 'dealer-checkbox');
                                        </script>
                                    @endif
                                @endforeach
                            </div>

                        </div> &nbsp; &nbsp;

                          <!-- jobrole-->
                          <div>
                            <button id="jobrole-checkbox-select" onclick="showHideDropdown(event, 'jobrole-checkbox')" class="select form-control" type="button" id="" style="text-align: inherit;  padding-right: 20px;" required>
                            {{__('lang.select-job-role')}} <span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>
                            </button>

                            <div class="shadow rounded menu" id="jobrole-checkbox" style="OVERFLOW-Y:scroll; position: absolute; background: white; z-index: 1; HEIGHT: 300px; display:none; padding: 5 0 0 5; margin-top: 2px; ">
                                @foreach($jobRoleWithNa as $jobRole)
                                    <span class="d-block menu-option" style="color:#328CB3;">
                                        <label>
                                            <input type="checkbox" class="jobrole-checkbox" onclick="doThis('{{__('lang.select-job-role')}}', 'jobrole-checkbox-select', this.dataset.name, 'jobrole-checkbox')"
                                            data-name="{{ $jobRole->name }}"
                                            name="filter_jobRole[]" value="{{ $jobRole->id }}" {{ ($export_jobRoles == 0) ? '' : (in_array($jobRole->id, explode(',', $export_jobRoles)) ?  'checked="checked"' : '') }}>&nbsp;
                                            {{ $jobRole->name }}
                                        </label>
                                    </span>
                                    @if(in_array($jobRole->id, explode(',', $export_jobRoles)))
                                        <script>
                                            doThis('{{__('lang.select-job-role')}}', 'jobrole-checkbox-select', "{{ $jobRole->name }}" , 'jobrole-checkbox');
                                        </script>
                                    @endif
                                @endforeach
                            </div>

                        </div> &nbsp; &nbsp;

                        <!-- region -->
                        <div>
                            <button  id="region-checkbox-select" onclick="showHideDropdown(event, 'regions-checkbox')" class="select form-control" type="button" id="region" style="text-align: inherit;  padding-right: 20px;" required>
                            {{__('lang.select-region')}} <span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>
                            </button>

                            <div class="shadow rounded menu"  id="regions-checkbox" style="OVERFLOW-Y:scroll; position: absolute; background: white; z-index: 1;  HEIGHT: 300px; display:none; padding: 5 0 0 5; margin-top: 2px;">
                                @foreach($adminRegions as $region)
                                    <span class="d-block menu-option dashboard-checkbox">
                                        <label>
                                            <input type="checkbox"
                                                class="region-checkbox"
                                                onclick="doThis('{{__('lang.select-region')}}', 'region-checkbox-select', this.dataset.name, 'region-checkbox')" data-name="{{ $region->name }}"
                                                name="filter_region[]"
                                                value="{{$region->id}}" {{ ($export_regions == 0) ? '' : (in_array($region->id, explode(',', $export_regions)) ?  'checked="checked"' : '') }} >&nbsp;
                                            {{ $region->name }}
                                        </label>
                                    </span>
                                    @if(in_array($region->id, explode(',', $export_regions)))
                                            <script>
                                                doThis('{{__('lang.select-region')}}', 'region-checkbox-select', '{{$region->name}}', 'region-checkbox');
                                            </script>
                                    @endif
                                @endforeach
                            </div>
                        </div> &nbsp; &nbsp;

                        <!-- group -->

                        <div>
                            <button id="group-checkbox-select" onclick="showHideDropdown(event, 'group-checkbox')" class="select form-control" type="button" id="" style="text-align: inherit;  padding-right: 20px;" required>
                            {{__('lang.select-group')}} <span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>
                            </button>

                            <div class="shadow rounded menu" id="group-checkbox" style="OVERFLOW-Y:scroll; position: absolute; background: white; z-index: 1; HEIGHT: 300px; display:none; padding: 5 0 0 5; margin-top: 2px;">
                            @foreach($groupWithNa as $group)
                                    <span class="d-block menu-option" style="color:#328CB3;">
                                        <label>
                                            <input type="checkbox" class="group-checkbox" onclick="doThis('{{__('lang.select-group')}}', 'group-checkbox-select', this.dataset.name, 'group-checkbox')"
                                             data-name="{{ $group->name }}"
                                            name="filter_group[]" value="{{$group->id}}" {{ ($export_groups == 0) ? '' : (in_array($group->id, explode(',', $export_groups)) ?  'checked="checked"' : '') }}  >&nbsp;
                                        {{ $group->name }}
                                        </label>
                                    </span>
                                    @if(in_array($group->id, explode(',', $export_groups)))
                                        <script>
                                            doThis('{{__('lang.select-group')}}', 'group-checkbox-select', "{{ $group->name }}", 'group-checkbox');
                                        </script>
                                    @endif
                                @endforeach
                            </div>

                        </div> &nbsp; &nbsp;

                        <!-- learning path-->
                        <div>
                            <button id="learningPath-checkbox-select" onclick="showHideDropdown(event, 'learningPath-checkbox')" class="select form-control" type="button" id="" style="text-align: inherit;  padding-right: 20px;" required>
                                {{__('lang.select-learning-path')}} <span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>
                            </button>

                            <div class="shadow rounded menu" id="learningPath-checkbox" style="OVERFLOW-Y:scroll; position: absolute; background: white; z-index: 1; WIDTH:12%; HEIGHT: 300px; display:none; padding: 5 0 0 5; margin-top: 2px;">
                                @foreach($learningPaths as $learningPath)
                                    <span class="d-block menu-option dashboard-checkbox">
                                        <label>
                                            <input type="checkbox" class="learningPath-checkbox" onclick="doThis('{{__('lang.select-learning-path')}}', 'learningPath-checkbox-select', this.dataset.name, 'learningPath-checkbox')"
                                             data-name="{{ $learningPath->name }}"
                                            name="filter_learningPath[]" value="{{ $learningPath->id }}" {{ ($export_learningPaths == 0) ? '' : (in_array($learningPath->id, explode(',', $export_learningPaths)) ?  'checked="checked"' : '') }}>&nbsp;
                                            {{ $learningPath->name }}
                                        </label>
                                    </span>
                                    @if(in_array($learningPath->id, explode(',', $export_learningPaths)))
                                        <script>
                                            doThis('{{__('lang.select-learning-path')}}', 'learningPath-checkbox-select', "{{ $learningPath->name }}" , 'learningPath-checkbox');
                                        </script>
                                    @endif
                                @endforeach
                            </div>

                        </div> &nbsp; &nbsp;

                        <button type="submit" class="btn-theme">{{ __('lang.apply-filter') }}</button>
                </form>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-3">

            <div class="white-wrapper dash-stat">
                <!-- <img src="{{ asset('assets/images/avatar_default.png') }}" style="width:22%"> -->
                <span class="icon">
                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="user" class="svg-inline--fa fa-user fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M224 256c70.7 0 128-57.3 128-128S294.7 0 224 0 96 57.3 96 128s57.3 128 128 128zm89.6 32h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.6-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-41.6c0-74.2-60.2-134.4-134.4-134.4z"></path></svg>
                </span>
                <h5>{{ __('lang.total-learners') }}</h5>
                <h6>{{ $totalLeaners }}</h6>
            </div>

        </div>

        <div class="col-sm-9">
            <div class="white-wrapper">
                <div class="t-title">
                    <span>
                        {{ __('lang.learners') }}
                    </span>
                    <div class="flex">
<?php echo e(url('/admin/users/') . (isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '')); ?> <i class="fa fa-eye"
                                    aria-hidden="true"></i> {{ __('lang.view-filtered-data') }}</a>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <a class="" href="{{ url('/admin/leraners/export?type=excel&country='.$export_countries. '&region='. $export_regions . '&group=' . $export_groups . '&jobRole=' . $export_jobRoles. '&dealer=' . $export_dealers. '&learningPath=' . $export_learningPaths) }}" >
                                <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                {{ __('lang.export-data') }}</a>
                    </div>
                </div>
                <div class="table">
                        <table class="data-table table2">
                            <thead>
                                <tr>
                                    <th>{{ __('lang.s-no') }}</th>
                                    <th>{{ __('lang.name') }}</th>
                                    <th>{{ __('lang.email-address') }}</th>
                                    <th>{{ __('lang.country') }}</th>
                                    <th>{{ __('lang.job-role') }}</th>
                                    <th>{{ __('lang.region') }}</th>
                                    <th>{{ __('lang.group') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($learners) > 0)
                                    @foreach($learners as $learner)
                                        <tr>
                                            <td>{{ ++$index }}</td>
                                            <td>{{ $learner->name }}</td>
                                            <td>{{ $learner->email }}</td>
                                            <td>{{ $learner->country_id ? $learner->country->name : 'N/A' }}</td>
                                            <td>{{ $learner->jobRole ?  $learner->jobRole->name : 'N/A' }}
                                            <td>{{ $learner->region_id ? $learner->region->name : 'N/A' }}</td>
                                            <td>{{ $learner->group  ?  $learner->group->name : 'N/A' }}
                                            </td>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="9" style="text-align: center;">
                                            {{ __('lang.no-record') }} </td>
                                    </tr>

                                @endif
                            </tbody>
                        </table>
                    </div>
            </div>
        </div>
    </div>
</div>

<hr style="border-top: 0.5px solid gray;">


<div class="container-fluid">
    <div class="row" style="color: gray; font-weight: bold; margin-left: 2px;">
        <span>{{ __('lang.all-learners') }}</span>
    </div>
</div>

<div class="container-fluid dashboard-wrapper mt-0">


    <div class="row" style="width:100%">
    <div class="col-sm-3">
        <div class="white-wrapper  dash-stat">
            <span class="icon">
            <svg aria-hidden="true"  class="svg-inline--fa fa-chart-line fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M496 384H64V80c0-8.84-7.16-16-16-16H16C7.16 64 0 71.16 0 80v336c0 17.67 14.33 32 32 32h464c8.84 0 16-7.16 16-16v-32c0-8.84-7.16-16-16-16zM464 96H345.94c-21.38 0-32.09 25.85-16.97 40.97l32.4 32.4L288 242.75l-73.37-73.37c-12.5-12.5-32.76-12.5-45.25 0l-68.69 68.69c-6.25 6.25-6.25 16.38 0 22.63l22.62 22.62c6.25 6.25 16.38 6.25 22.63 0L192 237.25l73.37 73.37c12.5 12.5 32.76 12.5 45.25 0l96-96 32.4 32.4c15.12 15.12 40.97 4.41 40.97-16.97V112c.01-8.84-7.15-16-15.99-16z"></path></svg>
            </span>
            <h5>{{ __('lang.average-completion-of-learning-path') }} </h5>
            <h6>{{ round($completedPercentage, 2) }} %</h6>
        </div>

    </div>
    <div class="col-sm-9">


        <div class="white-wrapper">
            <div class="t-title">
                <span>
                    {{ __('lang.learning-path-completion-rate') }} (%)
                </span>
                @if($count > $learningPathLimit)
                    <div class="flex align-items-center justify-content-end">

                        <button class="prev-chart chart-ajaxlink" data-id="{{$chartPage-1}}" data-type="prev" data-count="{{$count}}" style="display:none"><i class="fa fa-chevron-left" aria-hidden="true"></i></button>

                        <button class="next-chart chart-ajaxlink" data-id="{{$chartPage+1}}" data-type="next" data-count="{{$count}}"><i class="fa fa-chevron-right" aria-hidden="true"></i></button>
                    </div>
                @endif
            </div>
            <div id="chartdiv"></div>
            <div style="text-align: center;">
                    {{ __('lang.learning-path-code') }}
            </div>
        </div>

    </div>
    </div>
</div>

<div class="container-fluid dashboard-wrapper mt-0" >
    <div class="col">
        <div class="white-wrapper">
            <div class="t-title">
                <span>
                    {{ __('lang.completion-of-courses-for-dealer') }} (%)
                </span>
                <span>
                @if(count($learningPaths) > 0)
                <select name="learningpath" id="learningpath" class="form-control" onchange="getCountryChartData(this.value)" required>
                    @foreach($learningPaths as $learningPath)
                        <option value="{{ $learningPath->id }}" >
                            {{ $learningPath->name }}</option>
                    @endforeach
                </select>
                @endif
                </span>
            </div>
            <div id="chartdiv1"></div>
        </div>

    </div>

</div>

<div class="container-fluid dashboard-wrapper mt-0" >
    <div class="col">
        <div class="white-wrapper">
            <div class="chartloader"></div>
            <div class="t-title">
                <span>
                    {{ __('lang.completion-of-course-for-staff') }} (%)
                </span>
                <span style="display: flex;">

                    @if(count($dealers) > 0)
                    <select name="dealerid" id="dealerid" class="form-control" onchange="getStaffChartData()" required>
                        @foreach($dealers as $dealer)
                            <option value="{{ $dealer->id }}" >
                                {{ $dealer->name }}</option>
                        @endforeach
                    </select>
                    @endif

                    @if(count($learningPaths) > 0)
                    <select name="stafflearningpath" id="stafflearningpath" class="form-control" onchange="getStaffChartData()" required>
                        @foreach($learningPaths as $learningPath)
                            <option value="{{ $learningPath->id }}" >
                                {{ $learningPath->name }}</option>
                        @endforeach
                    </select>
                    @endif
                </span>
            </div>
            <div id="chartdiv2"></div>
        </div>

    </div>

</div>


<div class="container-fluid dashboard-wrapper" style="margin-top: 120px !important">
<div class="row">
    <div class="col-sm-6">
        <div class="white-wrapper">
            <div class="t-title">
                <span>{{__('lang.news-promotions')}}</span>
                <a href="{{ url('/admin/news-promotions/')}}">{{ __('lang.view-all') }}</a>
            </div>
            @foreach ($newsPromotions as $news)

                <div style="border-bottom:1px solid #eee;" class="pb-3 mb-3 dash-flex">
                    @if($news->media != NULL)
                            @php   $ext = explode('.', $news->media); @endphp

                            @if(in_array(strtolower($ext[1]), $imageFormat))
                            <div class="img-box">
                                <img src="{{ asset('storage' . $newsViewStoragePath . $news->media) }}" >
                            </div>
                            @elseif (in_array(strtolower($ext[1]), $videoFormat))
                            <video controls>
                                <source  src="{{ asset('storage' . $newsViewStoragePath . $news->media) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>

                            @else
                            <div class="attachment">
                                <a href="{!! url('/admin/news-promotions/'. $news->id . '/attachment') !!}" target="_blank" ><img src="{{ asset('assets/images/pdf.png') }}" title="image"></a> &nbsp;
                            </div>
                            @endif
                    @endif
                    <div style="width:100%">
                        <div>
                        <!-- <p class="text-right mb-0 gray"><small>{{date('d M Y', strtotime($news->created_at))}} | {{$news->createdBy->name}}</small></p> -->
                        <a href="{{url('admin/news-promotions/' . $news->id)}}" >
                            <h6>
                                <b class="color">{{$news->title}}</b>
                            </h6>
                        </a>
                            <div class="ckeditor-text">{!!$news->description!!}</div>
                        </div>
                            <div class="flex justify-content-between">
                            <small class="text-left">{{ $news->country ? $news->country->name : 'N/A'}} | {{ $news->region ? $news->region->name : 'N/A'}} | {{ $news->JobRole ? $news->JobRole->name : 'N/A'}} | {{$news->group ? $news->group->name : 'N/A'}}</small>
                            <div class="flex">
                            <a href="{{  url('admin/news-promotions', $news->id) }}"><i class="fa fa-eye" aria-hidden="true"></i> {{ __('lang.view') }}</a>
                                &nbsp;&nbsp;<form action="{{  url('admin/news-promotions', $news->id) }}" method="POST" class="text-right mb-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('{{ __('lang.delete-confirmation-text') }}')" class="text-danger" style="border: 0px; background:transparent"><i class="fa fa-trash" aria-hidden="true"></i> {{__('lang.delete')}} </button>
                            </form></div>
                            </div>

                            <div class="text-left">

                            </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>


    <div class="col-sm-6">
        <div class="white-wrapper">
            <div class="t-title">
                <span> {{__('lang.sales-tips')}} </span>
                <a href="{{ url('/admin/sales-tips/')}}">{{ __('lang.view-all') }}</a>
            </div>

            @foreach ($salesTips as $salesTip)

            <div style="border-bottom:1px solid #eee;" class="pb-3 mb-3  dash-flex">
                    @if($salesTip->media != NULL)

                            @php   $ext = explode('.', $salesTip->media); @endphp

                            @if(in_array(strtolower($ext[1]), $imageFormat))
                            <div class="img-box">
                                <img src="{{ asset('storage' . $salesViewStoragePath . $salesTip->media) }}">
                            </div>
                            @elseif (in_array(strtolower($ext[1]), $videoFormat))
                                <video controls>
                                    <source  src="{{ asset('storage' . $salesViewStoragePath . $salesTip->media) }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>

                            @else
                            <div class="attachment">
                                <a href="{!! url('/admin/sales-tips/'. $salesTip->id . '/attachment') !!}" target="_blank" ><img src="{{ asset('assets/images/pdf.png') }}" title="image" ></a> &nbsp;
                            </div>
                            @endif
                    @endif
                    <div style="width:100%">
                        <div>
                            <a href="{{url('admin/sales-tips/' . $salesTip->id)}}" >
                            <h6>
                                <b class="color">{{$salesTip->title}}</b>
                            </h6>
                                </a>
                                <div class="ckeditor-text">{!!$salesTip->description!!}</div>
                            </div>


                            <div class="flex justify-content-between">
                                <small class="text-left">{{ $salesTip->country ? $salesTip->country->name : 'N/A'}} | {{ $salesTip->region ? $salesTip->region->name : 'N/A'}} | {{ $salesTip->JobRole ? $salesTip->JobRole->name : 'N/A'}} | {{ $salesTip->group ? $salesTip->group->name : 'N/A'}}</small>

                                <div class="flex">
                                <a href="{{  url('admin/sales-tips', $salesTip->id) }}" ><i class="fa fa-eye" aria-hidden="true"></i> {{ __('lang.view') }}</a>
                                @if($salesTip->created_by == Auth::user()->id)
                                    <form action="{{  url('admin/sales-tips/' . $salesTip->id . '/edit') }}" method="GET" class="text-right">
                                        @csrf
                                        <button type="submit" class="text-danger" style="border: 0px; background:transparent"><i class="fa fa-pencil" aria-hidden="true"></i> {{__('lang.edit')}} </button>
                                    </form>
                                    <form action="{{  url('admin/sales-tips', $salesTip->id) }}" method="POST" class="text-right">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-danger" onclick="return confirm('{{ __('lang.delete-confirmation-text') }}')" style="border: 0px; background:transparent"><i class="fa fa-trash" aria-hidden="true"></i> {{__('lang.delete')}} </button>
                                    </form>
                                @endif
                                </div>
                        </div>



                    </div>
            </div>
            @endforeach

        </div>
    </div>

</div>
</div>


<script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>

<script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
<script src="https://www.amcharts.com/lib/3/serial.js"></script>
<script src="https://www.amcharts.com/lib/3/plugins/dataloader/dataloader.min.js"></script>

<style>
    body {
        padding: 0;
        margin: 0;
        font-family: Verdana;
        font-size: 15px;
    }

    select {
        font-size: 15px;
        padding: 5px;
    }

    chartloader {
        border: 5px solid #f3f3f3;
        -webkit-animation: spin 1s linear infinite;
        animation: spin 1s linear infinite;
        border-top: 5px solid #555;
        border-radius: 50%;
        width: 50px;
        height: 50px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .selector {
        background: #388FB5;
        border-bottom: 1px solid #ddd;
        padding: 16px;
        margin: 0;
    }

    #chartdiv {
        width: 100%;
        height: 300px;
        font-size: 11px;
    }

    #chartdiv1 {
        width: 100%;
        height: 250px;
    }

</style>

<script>

    function renderLPPercentage(percentageChart, chartData) {
        percentageChart.data = chartData;

        var categoryAxis = percentageChart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.dataFields.category = "id";
        categoryAxis.renderer.grid.template.location = 0;
        categoryAxis.renderer.minGridDistance = 20;
        categoryAxis.renderer.grid.template.disabled = true;
        let label = categoryAxis.renderer.labels.template;
        label.truncate = true;
        label.maxWidth = 120;

        categoryAxis.adapter.add("getTooltipText", (text, target, key) => {
            let item = chartData.find(data => data.id == text);
            return item.name;
        });


        var valueAxis = percentageChart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.min = 0;
        valueAxis.max = 100;
        valueAxis.numberFormatter = new am4core.NumberFormatter();
        valueAxis.numberFormatter.numberFormat = "#";
        valueAxis.adapter.add("getTooltipText", (text) => {
            return text + "%";
        });

        var series = percentageChart.series.push(new am4charts.ColumnSeries());
        series.dataFields.valueY = "completedPercentage";
        series.dataFields.categoryX = "id";
        // series.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/]";

        percentageChart.cursor = new am4charts.XYCursor();
        percentageChart.cursor.xAxis = categoryAxis;

        var gradient = new am4core.LinearGradient();


        var columnTemplate = series.columns.template;
        columnTemplate.strokeWidth = 2;
        columnTemplate.strokeOpacity = 1;

        gradient.addColor(am4core.color("#109BF8"));
        gradient.addColor(am4core.color("#0D5F7B"));
        gradient.rotation = 90;

        series.columns.template.fill = gradient;
        series.columns.template.strokeWidth = 0 ;
    }

    function generateChartData(charJson) {

        var chartData = [];
        // var charJson = {!!json_encode($learningPathCompletion)!!};
        var t = this;

        $.each(charJson, function (i, v) {
            var name = v.name.substring(0, 15);
            chartData.push({
                // id: t.getInitials(v.name),
                id: v.name,
                name: v.name,
                completedPercentage: v.completed_percentage
            });
        });

        return chartData;
    }

    var learningPathCompletion = {!!json_encode($learningPathCompletion)!!};
    var chartData = generateChartData(learningPathCompletion);
    var completionChart = am4core.create("chartdiv", am4charts.XYChart);
    renderLPPercentage(completionChart, chartData);


    /************ Course By Country chart ***********************/

    am4core.useTheme(am4themes_animated);
    var dealerDataChart = am4core.create("chartdiv1", am4charts.XYChart);
    dealerDataChart.paddingRight = 20;

    var categoryAxis = dealerDataChart.xAxes.push(new am4charts.CategoryAxis());
    categoryAxis.dataFields.category = "name";

    // Create value axis
    var valueAxis = dealerDataChart.yAxes.push(new am4charts.ValueAxis());
    valueAxis.min = 0;
    valueAxis.max = 100;
    valueAxis.baseValue = 0;
    valueAxis.numberFormatter.numberFormat = "#";

    dealerDataChart.cursor = new am4charts.XYCursor();
    dealerDataChart.cursor.xAxis = categoryAxis;

    const staffChartData = am4core.create("chartdiv2", am4charts.XYChart);
    staffChartData.paddingRight = 20;

    const categoryAxisStaff = staffChartData.xAxes.push(new am4charts.CategoryAxis());
    categoryAxisStaff.dataFields.category = "name";

    // Create value axis
    const valueAxisStaff = staffChartData.yAxes.push(new am4charts.ValueAxis());
    valueAxisStaff.min = 0;
    valueAxisStaff.max = 100;
    valueAxisStaff.baseValue = 0;
    valueAxisStaff.numberFormatter.numberFormat = "#";
    valueAxis.adapter.add("getTooltipText", (text) => {
        return text + "%";
    });

    staffChartData.cursor = new am4charts.XYCursor();
    staffChartData.cursor.xAxis = categoryAxisStaff;

    function formatAndRenderCourseChartForStaff(data) {

       const staffsData = [];

       if(staffChartData.series.length > 0) {
        staffChartData.series.removeIndex(0);
       }

       const series = staffChartData.series.push(new am4charts.LineSeries());
       series.dataFields.categoryX = "name";
       series.dataFields.valueY = "completed_percentage";
       series.stroke = am4core.color("#6cb2eb"); //blue
       series.strokeWidth = 2;
       series.xAxis = categoryAxisStaff;

       const bullet = series.bullets.push(new am4charts.CircleBullet());
       bullet.circle.stroke = am4core.color("#fff");
       bullet.circle.fill = am4core.color("#6cb2eb");
       bullet.circle.strokeWidth = 2;
       bullet.tooltipText = "[bold]{valueY} [/]";

       $.each(data, function (i, v) {
           let staffData = {
               name: v.name,
               completed_percentage: v.completed_percentage
           };

           staffsData.push(staffData);
       });

       staffChartData.data = staffsData;
       staffChartData.invalidate();


       // Tooltip
    //    series.tooltip.label.textAlign = "middle";
    //    series.tooltip.pointerOrientation = "down";
    //    series.tooltip.getFillFromObject = false;
    //    series.tooltip.background.fill = am4core.color("#6cb2eb");
    //    series.tooltip.dy = -5;

    //    bullet.propertyFields.showTooltipOn = "showTooltip";
    //    series.tooltip.propertyFields.pointerOrientation = "orientation";
    //    series.tooltip.propertyFields.dy = "offset";

   }

    function formatAndRenderCourseChart(data) {

        var dealersData = [];
        var axisIds = [];

        $.each(data, function (i, v) {
            let dealerData = {
                name: v.name,
                completed_percentage: v.completed_percentage
            };

            dealersData.push(dealerData);

        });
        dealerDataChart.data = dealersData;

        if(dealerDataChart.series.length > 0) {
            dealerDataChart.series.removeIndex(0);
        }

        var series1 = dealerDataChart.series.push(new am4charts.LineSeries());
        series1.dataFields.categoryX = "name";
        series1.dataFields.valueY = "completed_percentage";
        series1.stroke = am4core.color("#6cb2eb"); //blue
        series1.strokeWidth = 2;
        series1.xAxis = categoryAxis;


        var bullet = series1.bullets.push(new am4charts.CircleBullet());
        bullet.circle.stroke = am4core.color("#fff");
        bullet.circle.fill = am4core.color("#6cb2eb");
        bullet.circle.strokeWidth = 2;
        bullet.tooltipText = "[bold]{valueY} [/]";

        // Tooltip
        series1.tooltip.label.textAlign = "middle";
        series1.tooltip.pointerOrientation = "down";
        series1.tooltip.getFillFromObject = false;
        series1.tooltip.background.fill = am4core.color("#6cb2eb");
        series1.tooltip.dy = -5;

        bullet.propertyFields.showTooltipOn = "showTooltip";
        series1.tooltip.propertyFields.pointerOrientation = "orientation";
        series1.tooltip.propertyFields.dy = "offset";

    }

    var dealerDataResponse = {!!json_encode($dealerData)!!};
    formatAndRenderCourseChart(dealerDataResponse);

    var staffDataResponse = {!!json_encode($staffData)!!};
    formatAndRenderCourseChart(staffDataResponse);



    function getCountryChartData(learningPathId) {
        $.ajax({
              type: 'GET',
              url :  `chart/learning-path/${learningPathId}/data`,
              cache: false,
              success: function (data) {
                formatAndRenderCourseChart(data.data)
              },
              error: function (error) {
                console.log(error);
              }
        });
    }

    function getStaffChartData(learningPathId, dealerId) {
        let learningPath = $('#stafflearningpath').val();
        let dealer = $('#dealerid').val();

        $.ajax({
              type: 'GET',
              url :  `chart/learning-path/${learningPath}/${dealer}/data`,
              cache: false,
              success: function (data) {
                formatAndRenderCourseChartForStaff(data.data)
              },
              error: function (error) {
                console.log(error);
              }
        });
    }

    function getInitials(name) {
        return name.match(/(\b\S)?/g).join("").toUpperCase();
    }


    //ajax call to render learning path graph

  $(".chart-ajaxlink").click(function (e) {
        e.preventDefault();
        var offset = 7;
        var page = $(this).attr("data-id");
        var linkType = $(this).attr("data-type");
        totalCount = $(this).attr("data-count");
        var nextPage;
        var prevPage;

        // show-hide pre-button
        if(page != 1 && page > 0){
            $('.prev-chart').show();
        } else {
            $('.prev-chart').hide();
        }

        // show-hide next button
        if(totalCount > (page * offset) )
        {
            $('.next-chart').show();
        } else {
            $('.next-chart').hide();
        }

        var ajaxurl = app_url + "/admin/prev-next-learning-paths/" + page + '/' + offset;
        $.ajax({
                    url: ajaxurl,
                    type: 'GET',
                    success: function(data) {
                        generateChartData(data.data);
                        var chartData = generateChartData(data.data);
                        var completionChart = am4core.create("chartdiv", am4charts.XYChart);
                        renderLPPercentage(completionChart, chartData);
                        prevPage = parseInt(page)-1;
                        $('.prev-chart').attr('data-id', prevPage);

                        nextPage = parseInt(page)+1;
                        $('.next-chart').attr('data-id', nextPage);
                        if(nextPage == 2)
                        {
                            $('.next-chart').show();
                        }

                    }
        })
  });


</script>

@endsection
