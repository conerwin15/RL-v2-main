@extends('layouts.app')

@section('content')

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
    <div class="dash-title container-fluid">
        <a href="{{ url('/superadmin/quizzes') }}"><b>{{ __('lang.quizzes') }} &gt;</b>
            <span class="bradcrumb">{{ $quiz->name }}</span>
        </a>

        <div class="d-lg-flex align-items-center justify-content-end ">
            <form class="d-lg-flex mb-0 justify-content-end align-items-center" method="GET" >
                    
                    @if(isset($_GET['search']))
                        <input type="text"  placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-controller form-control mb-0"  id="search" name="search" value="{{$_GET['search']}}">
                    @else
                        <input type="text"  placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-controller form-control mb-0" id="search" name="search">
                    @endif				
                    <button type="submit" class="ml-2 btn-theme">{{__('lang.search')}}</button> 						
            </form>
        </div>
    </div>    
     
            <form method="GET" class="container-fluid" >
                <div class="white-wrapper pb-4">
                    <div class="row align-items-end">
                        <div class="col-sm-8">
                            <h6><b>{{__('lang.quiz-filters')}}</b></h6>
                        </div>

                        <div class="col-sm-4 row">
                            <a class="" href="{{ url('/superadmin/answer-scores/'.$quiz->id.'/export?type=excel&country='.$export_countries. '&region='. $export_regions . '&group=' . $export_groups . '&jobRole=' . $export_jobRoles) }}">
                                
                                <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                {{ __('lang.export-score-with-answer') }}
                            </a>
                            &nbsp; &nbsp; &nbsp;
                            <a class="" href="{{ url('/superadmin/scores/'.$quiz->id.'/export?type=excel&country='.$export_countries. '&region='. $export_regions . '&group=' . $export_groups . '&jobRole=' . $export_jobRoles) }}">
                                
                                <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                                {{ __('lang.export-score') }}
                            </a>
                        </div>

                        <!-- country -->
                        <div class="col-sm-2">
                            <button id="country-checkbox-select" onclick="showHideDropdown(event, 'country-checkbox')" class="select form-control" type="button" id="" style=" padding-right: 20px;" required>
                                {{__('lang.select-country')}} <span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>
                            </button>

                            <div class="shadow rounded menu" id="country-checkbox" style="OVERFLOW-Y:scroll; position: absolute; background: white; z-index: 1; WIDTH:84%; HEIGHT: 300px; display:none; padding: 5 0 0 5; margin-top: 2px">
                                
                                <span class="d-block menu-option dashboard-checkbox">
                                        <label>
                                            <input  type="checkbox" class="country-checkbox-all" id="country-checkbox-all"  name="country[]" value="-1" data-name="{{__('lang.all')}}" >&nbsp; {{__('lang.all')}}
                                        </label>
                                </span>
                                
                                @if($quiz->country_id == -1)
                                    @foreach($countries as $country)   
                                        <span class="d-block menu-option dashboard-checkbox">
                                            <label>
                                                <input onclick="doThis('{{__('lang.select-country')}}', 'country-checkbox-select', this.dataset.name, 'country-checkbox')" type="checkbox" class="country-checkbox"  name="filter_country[]" value="{{ $country->id }}" data-name="{{ $country->name }}" {{ ($export_countries == 0) ? '' : (in_array($country->id, explode(',', $export_countries)) ?  'checked="checked"' : '') }}>&nbsp;
                                                {{ $country->name }}
                                            </label>
                                        </span>
                                        @if(in_array($country->id, explode(',', $export_countries))) 
                                            <script>
                                                doThis('{{__('lang.select-country')}}', 'country-checkbox-select', "{{ $country->name }}" , 'country-checkbox');
                                            </script>
                                        @endif
                                    @endforeach 
                                @else
                                    @foreach($quiz->quizCountries() as $country)   
                                        <span class="d-block menu-option dashboard-checkbox">
                                            <label>
                                                <input onclick="doThis('{{__('lang.select-country')}}', 'country-checkbox-select', this.dataset.name, 'country-checkbox')" type="checkbox" class="country-checkbox"  name="filter_country[]" value="{{ $country->id }}" data-name="{{ $country->name }}" {{ ($export_countries == 0) ? '' : (in_array($country->id, explode(',', $export_countries)) ?  'checked="checked"' : '') }}>&nbsp;
                                                {{ $country->name }}
                                            </label>
                                        </span>
                                        @if(in_array($country->id, explode(',', $export_countries))) 
                                            <script>
                                                doThis('{{__('lang.select-country')}}', 'country-checkbox-select', "{{ $country->name }}" , 'country-checkbox');
                                            </script>
                                        @endif
                                    @endforeach 
                                @endif       
                            </div>

                        </div> &nbsp; &nbsp;

                        <!-- region -->
                        <div class="col-sm-2">
                            <button  id="region-checkbox-select" onclick="showHideDropdown(event, 'regions-checkbox')" class="select form-control" type="button" id="region" style=" padding-right: 20px;">
                            {{__('lang.select-region')}} <span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>
                            </button>

                            <div class="shadow rounded menu region-checkbox-div" id="regions-checkbox" style="OVERFLOW-Y:scroll; position: absolute; background: white; z-index: 1; WIDTH:84%; HEIGHT: 300px; display:none; padding: 5 0 0 5; margin-top: 2px">
                                 @if($quiz->country_id == -1)
                                    @foreach($regions as $region)   
                                        <span class="d-block menu-option dashboard-checkbox">
                                            <label>
                                                <input onclick="doThis('{{__('lang.select-region')}}', 'region-checkbox-select', this.dataset.name, 'region-checkbox')" type="checkbox" class="region-checkbox"  name="filter_region[]" value="{{ $region->id }}" data-name="{{ $region->name }}" {{ ($export_regions == 0) ? '' : (in_array($region->id, explode(',', $export_regions)) ?  'checked="checked"' : '') }}>&nbsp;
                                                {{ $region->name }}
                                            </label>
                                        </span>
                                        @if(in_array($region->id, explode(',', $export_regions))) 
                                            <script>
                                                doThis('{{__('lang.select-region')}}', 'region-checkbox-select', "{{ $region->name }}" , 'region-checkbox');
                                            </script>
                                        @endif
                                    @endforeach 
                                @else
                                    @foreach($quiz->quizRegions() as $region)   
                                            <span class="d-block menu-option dashboard-checkbox">
                                                <label>
                                                    <input onclick="doThis('{{__('lang.select-region')}}', 'region-checkbox-select', this.dataset.name, 'region-checkbox')" type="checkbox" class="region-checkbox"  name="filter_region[]" value="{{ $region->id }}" data-name="{{ $region->name }}" {{ ($export_regions == 0) ? '' : (in_array($region->id, explode(',', $export_regions)) ?  'checked="checked"' : '') }}>&nbsp;
                                                    {{ $region->name }}
                                                </label>
                                            </span>
                                            @if(in_array($region->id, explode(',', $export_regions))) 
                                            <script>
                                                doThis('{{__('lang.select-region')}}', 'region-checkbox-select', "{{ $region->name }}" , 'region-checkbox');
                                            </script>
                                            @endif
                                    @endforeach  
                                @endif          
                            </div>

                        </div> &nbsp; &nbsp;

                        <!-- jobrole-->
                        <div class="col-sm-2">
                            <button id="jobrole-checkbox-select" onclick="showHideDropdown(event, 'jobrole-checkbox')" class="select form-control" type="button" id="" style="padding-right: 20px;" required>
                            {{__('lang.select-job-role')}} <span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>
                            </button>

                            <div class="shadow rounded menu" id="jobrole-checkbox" style="OVERFLOW-Y:scroll; position: absolute; background: white; z-index: 1; width: 84%; HEIGHT: 300px; display:none; padding: 5 0 0 5; margin-top: 2px; ">
                                @foreach($jobRoleWithNa as $jobRole)   
                                    <span class="d-block menu-option" style="color:#328CB3;">
                                        <label>
                                            <input type="checkbox" class="jobrole-checkbox" onclick="doThis('{{__('lang.select-job-role')}}', 'jobrole-checkbox-select', this.dataset.name, 'jobrole-checkbox')" data-name="{{ $jobRole->name }}" 
                                            name="filter_jobRole[]" value="{{ $jobRole->id }}" {{ ($export_jobRoles == 0) ? '' : (in_array($jobRole->id, explode(',', $export_jobRoles)) ?  'checked="checked"' : '')}}>&nbsp;
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

                        <!-- group -->

                        <div class="col-sm-2">
                            <button id="group-checkbox-select" onclick="showHideDropdown(event, 'group-checkbox')" class="select form-control" type="button" id="" style=" padding-right: 20px;" required>
                            {{__('lang.select-group')}} <span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>
                            </button>

                            <div class="shadow rounded menu" id="group-checkbox" style="OVERFLOW-Y:scroll; position: absolute; background: white; z-index: 1; width: 84%; HEIGHT: 300px; display:none; padding: 5 0 0 5; margin-top: 2px;">
                            @foreach($groupWithNa as $group)   
                                    <span class="d-block menu-option" style="color:#328CB3;">
                                        <label>
                                            <input type="checkbox" class="group-checkbox" onclick="doThis('{{__('lang.select-group')}}', 'group-checkbox-select', this.dataset.name, 'group-checkbox')" data-name="{{ $group->name }}" 
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

                        </div>  

                        <div class="col">
                            <label for=""><br></label><br>
                            <button type="submit" class="btn-theme">{{ __('lang.apply-filter') }}</button>
                        </div>
                    </div>
                </div>    
            </form>

    <br/>
        <div class="table">

            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{__('lang.no')}}</th>
                        <th>{{__('lang.user')}}</th>
                        <th>{{__('lang.email')}}</th>
                        <th>{{__('lang.country')}}</th>
                        <th>{{__('lang.region')}}</th>
                        <th>{{__('lang.job-role')}}</th>
                        <th>{{__('lang.group')}}</th>
                        <th>{{__('lang.score')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @if(count($userQuizzes)>0)
                        @foreach($userQuizzes as $userQuiz)

                            @if($userQuiz->user != null)
                                <tr>
                                    <td>{{ ++ $index }}</td>
                                    <td>{{ $userQuiz->user->name }}</td>
                                    <td>{{ $userQuiz->user->email }}</td>
                                    <td>{{$userQuiz->user->country->name}}</td>
                                    <td>{{$userQuiz->user->region->name}}</td>
                                    <td>{{$userQuiz->user->jobRole ? $userQuiz->user->jobRole->name : 'N/A'}}</td>
                                    <td>{{$userQuiz->user->group ? $userQuiz->user->group->name : 'N/A'}}</td>
                                    <td>{{ $userQuiz->score }}</td>
                                
                                </tr> 
                            @else
                              @php $userCount ++;  @endphp
                            @endif    
                        @endforeach
                    @else
                        
                        <tr>
                            <td colspan="8" style="text-align: center;">{{ __('lang.no-record') }} </td>

                        </tr>

                    @endif
                    
                    @if(count($userQuizzes)>0 && count($userQuizzes) == $userCount)   
                    
                        <tr>
                            <td colspan="8" style="text-align: center;">{{ __('lang.no-record') }} </td>
                        </tr>
                    @endif    
                </tbody>
            </table>
           
        </div>

        {{ $userQuizzes->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}


    <script>
        $('#country-checkbox-all').change(function() {
            if($(this).is(":checked")) {
                $('.country-checkbox') .prop('checked', true);
                $('.country-checkbox') .prop('disabled', true);
                $('#region-checkbox-select') .prop('disabled', true);
            } else {
                $('.country-checkbox') .prop('checked', false);
                $('.country-checkbox') .prop('disabled', false);
                $('#region-checkbox-select') .prop('disabled', false);
            }
                
        });

        var countries;    
        $(".country-checkbox").change(function() {

            countries = [];

            $('#region').prop('disabled', false);

            $(".country-checkbox").each(function() {
                if ($(this).is(":checked")) {
                    countries.push($(this).val());
                } 
            });

            var ajaxurl = app_url + "/superadmin/country/" + countries + "/region" ;

            $.ajax({
            type: 'get',
            url: ajaxurl,
                success: function (data) {
                    $("#regions-checkbox").empty();
                    if(data){
                        $.each(data,function(key, value) {
                            $('#regions-checkbox').append(
                            `<span class="d-block menu-option" style="color:#328CB3;"><label><input type="checkbox" onclick="doThis('{{__('lang.select-region')}}', 'region-checkbox-select', this.dataset.name, 'regions-checkbox')" class="regions-checkbox" name="filter_region[]" value="${value.id}" data-name="${value.name}">&nbsp;
                            ${value.name}</label></span>`
                            );
                        });
                    }
                    
                },
                error: function (data) {
                
                }

            });

        });
    </script>    
@endsection