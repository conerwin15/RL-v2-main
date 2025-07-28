@extends('layouts.app')

@section('content')
<div class="dash-title container-fluid max-width">
    <div>
        <a href="{{ url( 'superadmin/leaderboard') }}"><b>{{ __('lang.leaderboard') }} &gt;</b></a> 
        <span class="bradcrumb">{{ __('lang.manage-points-bulk') }} </span>
    </div>
</div>
<form class="container-fluid max-width" method="GET">
        <div class="white-wrapper pb-4">
            <div class="row align-items-end">
                <div class="col-sm-12">
                    <h6><b>{{__('lang.filter')}}</b></h6>
                </div>
                <div class="col-sm-2">
                    <label>{{__('lang.select-country')}}: </label>
                    <select  name="filter_country" id="country" onchange="getRegion(this.value, true)" class="form-control select" >
                    <option  disabled> {{ __('lang.select') }}  {{ __('lang.country') }} </option>
                        <option value="-1"> {{ __('lang.all') }}</option>
                        @foreach ($countries as $country)
                            <option value="{{$country->id}}"  {{ @$_GET['filter_country'] == $country->id ? 'selected' : '' }}>{{ $country->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-2">
                <label>{{ __('lang.region') }}:</label>
                <select name="filter_region" class="select form-control" id="region" disabled  required>
                    <option value="-1" disabled>{{ __('lang.select') }} {{ __('lang.region') }}</option>
                </select>
            </div>

                <div class="col">
                    <label for=""><br></label><br>
                    <button type="submit" class="btn-theme">{{ __('lang.apply-filter') }}</button>
                </div>
            </div>
        </div>
    </form>
<form action="{{ url('superadmin/leaderboard/user/manage-points') }}"  method="POST" >
    <div class="container-fluid max-width">
        <div class="white-wrapper">
            <h5 class="bg-blue" style="margin: -15px -15px  16px -15px"></h5>
            <div class="flex align-items-center justify-content-start">
                @csrf
                <input type="hidden" name="selectAll" id="selectAll" value="">
                <div class="col-sm-4 row">
                    <label class="mr-2 white-space">{{ __('lang.adjust-point-by') }}:</label> &nbsp;
                    <input type="text" class="form-control w-100 input-small mr-2" rows="4" name="adjust_point" placeholder="-5" required> 
                    @if($errors->has('adjust_point'))
                        <div class="errorMsg" id="adjustPointtError">{{ $errors->first('adjust_point') }}</div>
                    @endif
                </div>
                <div class="col-sm-7 row">
                    <label class="mr-2 white-space">{{ __('lang.why-bonus-point-given') }}:</label>&nbsp;
                    <input type="text" class="form-control input-small mr-2" rows="4" name="bonus_point_reason" placeholder="{{__('lang.max-char-lenth-mark-featured')}}" style="max-width:65%;" maxlength="1200">
                    @if($errors->has('bonus_point_reason'))
                        <div class="errorMsg" id="adjustPointtError">{{ $errors->first('bonus_point_reason') }}</div>
                    @endif
                </div>
                <button type="submit" class="btn-theme"  style="padding: 4px 14px;">{{ __('lang.submit') }}</button>
            </div>
            <p class="text-danger mt-2 mb-0">* {{ __('lang.modification-points') }} </p>
        </div>
    </div>
    <div class="dash-title container-fluid max-width">
            <b style="opacity:.8; color:#3490dc;"><strong>{{ __('lang.leaderboard') }}</strong></b>
            <button type="button" class="btn-theme btn-sm" style="margin-left: 45%;"  id="bulkSelect">{{ __('lang.select-all') }}</button>
    </div>

    <div class="container-fluid max-width">
        <div class="white-wrapper pt-0 pb-0">

        
            <table class="table leaderboard-table">

                    <thead style="background-color: #388FB5;color: #fff;">
                        <tr>
                            <th>{{ __('lang.select') }}</th>
                            <th>{{ __('lang.name') }}</th>
                            <th>{{ __('lang.country') }}</th>
                            <th>{{ __('lang.region') }}</th>
                            <th>{{ __('lang.points') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                    @if(count($managePoints) > 0)    
                        @foreach ($managePoints as $managePoint)
                            <tr>
                                <td><input type="checkbox" name="bulk_point[]" value="{{$managePoint->id}}"></td>
                                <td>{{$managePoint->username}}</td>
                                <td>{{$managePoint->country}}</td>
                                <td>{{$managePoint->region}}</td>
                                <td>
                                <svg width="20" viewBox="0 0 65 57"><g transform="translate(-564 -1439)"><path d="M43.437,0A4,4,0,0,1,46.9,2L59.852,24.5a4,4,0,0,1,0,3.99L46.9,51a4,4,0,0,1-3.467,2H17.563A4,4,0,0,1,14.1,51L1.148,28.5a4,4,0,0,1,0-3.99L14.1,2a4,4,0,0,1,3.467-2Z" transform="translate(566 1441)" fill="#777d91"></path><path d="M46.426,0A4,4,0,0,1,49.9,2.019l13.969,24.5a4,4,0,0,1,0,3.963L49.9,54.981A4,4,0,0,1,46.426,57H18.574A4,4,0,0,1,15.1,54.981L1.13,30.481a4,4,0,0,1,0-3.963L15.1,2.019A4,4,0,0,1,18.574,0Z" transform="translate(564 1439)" fill="#777d91" opacity="0.41"></path><path d="M37.089,1.225l-4.537,9.2L22.4,11.9a2.224,2.224,0,0,0-1.23,3.793l7.343,7.156L26.78,32.96A2.222,2.222,0,0,0,30,35.3l9.08-4.773,9.08,4.773a2.224,2.224,0,0,0,3.224-2.341L49.65,22.851,56.993,15.7a2.224,2.224,0,0,0-1.23-3.793l-10.15-1.48-4.537-9.2a2.225,2.225,0,0,0-3.988,0Z" transform="translate(557.914 1449.537)" fill="#fff"></path></g></svg>
                                    {{$managePoint->totalPoints}}
                                </td>
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
</form>       

    {{ $managePoints->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}

    <script>
        
        var allSelected = false;

        $('#bulkSelect').click(function() {
            allSelected = !allSelected;
            if(allSelected) {
                $('#selectAll').val(-1);
                $('#bulkSelect').text("{{__('lang.unselect-all')}}");
            } else {
                $('#bulkSelect').text("{{__('lang.select-all')}}");
                $('#selectAll').val('');
            }
            $("input[type='checkbox']").attr('checked', allSelected);
            $("input[type='checkbox']").attr("disabled", allSelected);
        })
        
    </script>

    
@endsection