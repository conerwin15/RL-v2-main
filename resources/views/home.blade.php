@extends('layouts.app')
@section('content')

<div class="container-fluid">
    <h6 class="dash-title mb-5">
        <span><b>{{ __('Dashboard') }}</b></span>
        <div>
            <label for="">Country:</label>
            <select name="" class="form-control">
                <option value="">All Countries</option>
                <option value="">Countries</option>
                <option value="">Countries</option>
            </select>
        </div>
        <div>
            <label for="">Region:</label>
            <select name="" class="form-control">
                <option value="">All Region</option>
                <option value="">Region</option>
                <option value="">Region</option>
            </select>
        </div>
        <div>
            <label for="">Job Role:</label>
            <select name="" class="form-control">
                <option value="">All Job Role</option>
                <option value="">Job Role</option>
                <option value="">Job Role</option>
            </select>
        </div>
    </h6>

    <div class="stats">
        <div class="stat-box">
            <h6>TOTAL LEARNERS</h6>
            <h5>1528</h5>
            <div class="text-right">
                <span data-bs-toggle="tooltip"  data-placement="right" data-toggle="tooltip" data-html="true"  title="Tooltip on top" style="position:reletive">
                    <svg width="20" viewBox="0 0 27.417 27.417"><path d="M13.708,0A13.708,13.708,0,1,0,27.417,13.709,13.724,13.724,0,0,0,13.708,0Zm0,24.925A11.216,11.216,0,1,1,24.925,13.709,11.229,11.229,0,0,1,13.708,24.925Z" fill="#fff"/><path d="M146.663,70a1.662,1.662,0,1,0,1.661,1.662A1.664,1.664,0,0,0,146.663,70Z" transform="translate(-132.955 -64.184)" fill="#fff"/><path d="M151.246,140A1.246,1.246,0,0,0,150,141.246v7.477a1.246,1.246,0,1,0,2.492,0v-7.477A1.246,1.246,0,0,0,151.246,140Z" transform="translate(-137.538 -128.369)" fill="#fff"/></svg>
                </span>
            </div>
        </div>
        <div class="stat-box">
            <h6>AVERAGE COMPLETION</h6>
            <h5>80%</h5>
            <div class="progress-bar">
                <span style="width:22%"></span>
            </div>
        </div>
        <div class="stat-box">
            <h6>E-LEARNING DURATION </h6>
            
            <div id="container" style="height:100px;width:100%;margin-left:38px;"></div>
        </div>
        <div class="stat-box">
            <h6>AVERAGE QUIZ RATE</h6>
            <h5>2248</h5>
        </div>
    </div>
    
    

    <div class="row mt-5">
        <div class="col-md-6 ">
            <div class="table">
                <h5><b>All Learners</b>
                <span>
                    <a href="#">View All</a> 
                        <button class="default-btn">
                            <svg width="18.33" height="15.343" viewBox="0 0 18.33 15.343"><g transform="translate(-1.021 -1038.362)"><g transform="translate(1.021 1038.362)"><g transform="translate(0 0)"><path d="M1.021,2V17.343H13.809V12.228h1.279v2.56l1.024-.767,3.239-2.428L15.088,8.386v2.563H13.809V5.834l0,0,0,0L9.98,2V2ZM2.3,3.279H8.7L8.7,7.108H12.53v3.841H7.415v1.279H12.53v3.836H2.3Zm7.678.532L12,5.829H9.975Z" transform="translate(-1.021 -2)"/></g></g></g></svg>
                            Export Data
                        </button>
                    </span>
                </h5>
                <table>
                    <thead>
                        <tr>
                            <th>Sr. No</th>
                            <th>Name</th>
                            <th>Job Role</th>
                            <th>Region</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Anna Smith</td>
                            <td>Dealer</td>
                            <td>Region1</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Anna Smith</td>
                            <td>Dealer</td>
                            <td>Region2</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Anna Smith</td>
                            <td>Dealer</td>
                            <td>Region3</td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Anna Smith</td>
                            <td>Dealer</td>
                            <td>Region4</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-6 ">
            <div class="table">
                <h5><b>All Courses</b>
                <span>
                    <a href="#">View All</a> 
                        <button class="default-btn">
                            <svg width="18.33" height="15.343" viewBox="0 0 18.33 15.343"><g transform="translate(-1.021 -1038.362)"><g transform="translate(1.021 1038.362)"><g transform="translate(0 0)"><path d="M1.021,2V17.343H13.809V12.228h1.279v2.56l1.024-.767,3.239-2.428L15.088,8.386v2.563H13.809V5.834l0,0,0,0L9.98,2V2ZM2.3,3.279H8.7L8.7,7.108H12.53v3.841H7.415v1.279H12.53v3.836H2.3Zm7.678.532L12,5.829H9.975Z" transform="translate(-1.021 -2)"/></g></g></g></svg>
                            Export Data
                        </button>
                    </span>
                </h5>
                <table>
                    <thead>
                        <tr>
                            <th>Sr. No</th>
                            <th>Name</th>
                            <th>Duration</th>
                            <th>Owner</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Course1</td>
                            <td>45 min</td>
                            <td>Admin</td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Course1</td>
                            <td>45 min</td>
                            <td>Admin</td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Course1</td>
                            <td>45 min</td>
                            <td>Admin</td>
                        </tr>
                        <tr>
                            <td>4</td>
                            <td>Course1</td>
                            <td>45 min</td>
                            <td>Admin</td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>

<!-- <script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/material.js"></script>
<script src="https://cdn.amcharts.com/lib/4/lang/de_DE.js"></script>
<script src="https://cdn.amcharts.com/lib/4/geodata/germanyLow.js"></script>
<script src="https://cdn.amcharts.com/lib/4/fonts/notosans-sc.js"></script> -->
<script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
<style>
#container svg{
    transform:scale(1.5);
}
#container svg g[aria-label=Legend] {
    transform:scale(.5)  translateX(-35px);
    transform-origin: right;
}
#container svg g[aria-labelledby=id-66-title] {
    display:none
}
#container svg g[role=tooltip] {
    transform: scale(.5);
    transform-origin: bottom;
}
    </style>
<script>
am4core.ready(function() {

// Themes begin
am4core.useTheme(am4themes_animated);
// Themes end

// Create chart instance
var chart = am4core.create("container", am4charts.PieChart);

// Add data
chart.data = [{
  "country": "Austria",
  "litres": 128.3
}, {
  "country": "UK",
  "litres": 99
}, {
  "country": "Belgium",
  "litres": 60
}];

// Add and configure Series
var pieSeries = chart.series.push(new am4charts.PieSeries());
pieSeries.dataFields.value = "litres";
pieSeries.dataFields.category = "country";
pieSeries.innerRadius = am4core.percent(50);
pieSeries.ticks.template.disabled = true;
pieSeries.labels.template.disabled = true;

var rgm = new am4core.RadialGradientModifier();
rgm.brightnesses.push(-0.8, -0.8, -0.5, 0, - 0.5);
pieSeries.slices.template.fillModifier = rgm;
pieSeries.slices.template.strokeModifier = rgm;
pieSeries.slices.template.strokeOpacity = 0.4;
pieSeries.slices.template.strokeWidth = 0;

chart.legend = new am4charts.Legend();
chart.legend.position = "right";

}); // end am4core.ready()
</script>

@endsection