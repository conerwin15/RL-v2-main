@extends('layouts.app')
@section('content')

@php $index = 0; @endphp

</main>
<div class="dashboard">
    <div class="container-fluid">
        <div class="title">
            <span>{{ __('lang.dashboard') }}</span>
        </div>
    </div>
</div>

<div class="container-fluid dashboard-wrapper">
<div class="row" style="width: 106%;">
    <div class="col-sm-3">

        <div class="white-wrapper dash-stat">
            <!-- <img src="{{ asset('assets/images/avatar_default.png') }}" style="width:22%"> -->
            <span class="icon">
                <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="user" class="svg-inline--fa fa-user fa-w-14" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M224 256c70.7 0 128-57.3 128-128S294.7 0 224 0 96 57.3 96 128s57.3 128 128 128zm89.6 32h-16.7c-22.2 10.2-46.9 16-72.9 16s-50.6-5.8-72.9-16h-16.7C60.2 288 0 348.2 0 422.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-41.6c0-74.2-60.2-134.4-134.4-134.4z"></path></svg>
            </span>
            <h5>{{ __('lang.total-learners') }}</h5>
            <h6>{{ $totalLeaners }}</h6>
        </div>
        <div class="white-wrapper  dash-stat">
            <span class="icon">
            <svg aria-hidden="true"  class="svg-inline--fa fa-chart-line fa-w-16" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M496 384H64V80c0-8.84-7.16-16-16-16H16C7.16 64 0 71.16 0 80v336c0 17.67 14.33 32 32 32h464c8.84 0 16-7.16 16-16v-32c0-8.84-7.16-16-16-16zM464 96H345.94c-21.38 0-32.09 25.85-16.97 40.97l32.4 32.4L288 242.75l-73.37-73.37c-12.5-12.5-32.76-12.5-45.25 0l-68.69 68.69c-6.25 6.25-6.25 16.38 0 22.63l22.62 22.62c6.25 6.25 16.38 6.25 22.63 0L192 237.25l73.37 73.37c12.5 12.5 32.76 12.5 45.25 0l96-96 32.4 32.4c15.12 15.12 40.97 4.41 40.97-16.97V112c.01-8.84-7.15-16-15.99-16z"></path></svg>
            </span>
            <h5>{{ __('lang.average-completion-of-learning-path') }} </h5>
            <h6>{{ round($completedPercentage, 2) }} %</h6>
        </div>

    </div>

<!-- == -->
    <div class="col-sm-9">
        <div class="white-wrapper">
            <div class="t-title">
                    <span>
                        {{ __('lang.all-learners') }}
                    </span>
                    <div class="flex">
                    <a class="" href="{{ url('/dealer/staff/') }}"> <i class="fa fa-eye"
                                aria-hidden="true"></i> {{ __('lang.view-all') }}</a>
                        &nbsp;&nbsp;&nbsp;&nbsp;
                        <a class=""
                            href="{{ url('/dealer/leraners/export?type=excel&country='.@$_GET['filter_country']. '&region='. @$_GET['filter_region']) }}">
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
                                        <td>{{ $learner->country_id ? $learner->country->name : 'N/A'}}</td>
                                        <td>{{ $learner->jobRole ?  $learner->jobRole->name : 'N/A' }}
                                        <td>{{ $learner->region_id ?  $learner->region->name : 'N/A' }}</td>
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
        <div class="white-wrapper">
            <div class="t-title">
                <span>
                {{ __('lang.learning-path-completion-rate') }}
                </span>
                <div class="flex align-items-center justify-content-end">
                  
                    <button class="prev-chart chart-ajaxlink" data-id="{{$chartPage-1}}" data-type="prev" data-count="{{$count}}" style="display:none"><i class="fa fa-chevron-left" aria-hidden="true"></i></button>
                   
                    <button class="next-chart chart-ajaxlink" data-id="{{$chartPage+1}}" data-type="next" data-count="{{$count}}"><i class="fa fa-chevron-right" aria-hidden="true"></i></button>
                </div>
            </div>
            <div id="chartdiv"></div>
            <div style="text-align: center;">
                    {{ __('lang.learning-path-code') }}               
            </div>
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

        categoryAxis.adapter.add("getTooltipText", (text, target, key) => {
            let item = chartData.find(data => data.id == text);
            return item.name;
        });


        var valueAxis = percentageChart.yAxes.push(new am4charts.ValueAxis());
        valueAxis.min = 0;
        valueAxis.max = 100;
        valueAxis.numberFormatter.numberFormat = "#"; 
        valueAxis.adapter.add("getTooltipText", (text) => {
            return text + "%";
        });

        var series = percentageChart.series.push(new am4charts.ColumnSeries());
        series.dataFields.valueY = "completedPercentage";
        series.dataFields.categoryX = "id";
        series.columns.template.width = am4core.percent(5);

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

        $.each(charJson, function (i, v) {
            var name = v.name.substring(0, 15);
            chartData.push({
                // id: 'L' + v.id,
                id: name,
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

        var ajaxurl = app_url + "/dealer/prev-next-learning-paths/" + page + '/' + offset;
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
