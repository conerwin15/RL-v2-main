@extends('layouts.app')

@section('content')

    <div class="container-fluid max-width">
    <div class="dash-title container-fluid">
            <div>
                <a href="{{ url( 'superadmin/leaderboard') }}"><b>{{ __('lang.leaderboard') }} &gt;</b></a> 
                <span class="bradcrumb">{{ __('lang.featured-record') }} </span>
            </div>  
    </div>
    <div class="white-wrapper">
            
    <form class="container-fluid max-width" method="GET">
        <div class="white-wrapper pb-4">
            <div class="row align-items-end">
                <div class="col-sm-12">
                    <h6><b>{{__('lang.filter')}}</b></h6>
                </div>
                <div class="col-6 col-md-2">
                    <label>{{ __('lang.country') }}:</label>
                    <button id="country-checkbox-select" onclick="showHideDropdown(event, 'country-checkbox')" class="select form-control" type="button" id="" style=" padding-right: 20px;" required>
                        {{__('lang.select-country')}} <span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>
                    </button>

                    <div class="shadow rounded menu" id="country-checkbox" style="OVERFLOW-Y:scroll; position: absolute; background: white; z-index: 1; WIDTH:84%; HEIGHT: 300px; display:none; padding: 5 0 0 5; margin-top: 2px">
                            <span class="d-block menu-option dashboard-checkbox">
                                <label>
                                    <input  type="checkbox" class="country-checkbox-all" id="country-checkbox-all"  name="country[]" value="-1" data-name="{{__('lang.all')}}" >&nbsp; {{__('lang.all')}}
                                </label>
                            </span>

                           @foreach($countries as $country)
                            <span class="d-block menu-option dashboard-checkbox">
                                <label>
                                    <input onclick="doThis('{{__('lang.select-country')}}', 'country-checkbox-select', this.dataset.name, 'country-checkbox')" type="checkbox" class="country-checkbox"  name="country[]" value="{{ $country->id }}" data-name="{{ $country->name }}" >&nbsp;
                                    {{ $country->name }}
                                </label>
                            </span>

                        @endforeach
                    </div>

                    @if($errors->has('country'))
                            <div class="errorMsg" id="quizTextError">{{ $errors->first('country') }}</div>
                    @endif
                </div>

                <div class="col-sm-2">
                <label>{{ __('lang.training-admin') }}:</label>
                <select name="filter_admin" class="select form-control" id="admin" required>
                    <option value="-1">{{ __('lang.select') }} {{ __('lang.region') }}</option>
                    @foreach ($admins as $admin)
                        <option value="{{$admin->id}}"  {{ @$_GET['filter_admin'] == $admin->id ? 'selected' : '' }}>{{ $admin->name }}</option>
                    @endforeach
                </select>
            </div>

                <div class="col">
                    <label for=""><br></label><br>
                    <button type="submit" class="btn-theme">{{ __('lang.apply-filter') }}</button>
                </div>
            </div>
        </div>
    </form>
        <div class="container-fluid max-width">
            <table class="table leaderboard-table">

                    <thead style="background-color: #388FB5;color: #fff;">
                        <tr>
                            <th>{{ __('lang.featured-employee') }}</th>
                            <th>{{ __('lang.country') }}</th>
                            <th>{{ __('lang.training-admin') }}</th>
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
                                <td>{{ $featuredRecord->user->createdBy->model->role_id == $adminRole ? ucfirst($featuredRecord->user->getNameById($featuredRecord->user->created_by)) : 'N/A'}}</td>
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

    <script>
        $('#country-checkbox-all').change(function() {
            if($(this).is(":checked")) {
                $('.country-checkbox') .prop('checked', true);
                $('.country-checkbox') .prop('disabled', true);
                $('#region-checkbox-select') .prop('disabled', true);
            } else {
                $('.country-checkbox') .prop('checked', false);
                $('.country-checkbox') .prop('disabled', false);
            }

        });
    </script>
@endsection