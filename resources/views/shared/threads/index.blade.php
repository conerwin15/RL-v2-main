@extends('layouts.app')

@section('content')
<div class="dash-title container-fluid max-width">
    <b>{{__('lang.threads')}}    </b>
    <a class="btn-theme" href="{{ 'threads/create'}}">+ {{__('lang.create-thread')}}</a>
</div>

@if(Auth::user()->getRoleNames()->first() == 'superadmin')
    <form class="container-fluid max-width" method="GET" id="category-form">
        <div class="white-wrapper pb-4">
            <div class="row align-items-end">

                <div class="col-sm-2">
                    <label>{{__('lang.discussion-forum-type')}}: </label>
                    <select  name="forum_type" class="select form-control"  id="forum_type" required>
                        <option value="0" {{ (@$_GET['forum_type'] == '' || (@$_GET['forum_type']) == 0 )? 'selected' : '' }}>{{__('lang.public')}}</option>
                        <option value="1" {{ @$_GET['forum_type'] == 1 ? 'selected' : '' }}>{{__('lang.private')}}</option>
                    </select>
                        @if($errors->has('type'))
                            <div class="errorMsg" id="type">{{ $errors->first('type') }}</div>
                        @endif
                </div>
                @if(request()->get('forum_type') == 1)
                    <div id="private-div" style="display:contents;">
                @else
                    <div id="private-div" style="display: none;">
                @endif

                    <div class="col-sm-2">
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
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <label>{{ __('lang.region') }}:</label>
                            <button  id="region-checkbox-select" onclick="showHideDropdown(event, 'regions-checkbox')" class="select form-control" type="button" id="region" style=" padding-right: 20px;" required>
                            {{__('lang.select-region')}} <span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>
                            </button>

                            <div class="shadow rounded menu region-checkbox-div" id="regions-checkbox" style="OVERFLOW-Y:scroll; position: absolute; background: white; z-index: 1; WIDTH:84%; HEIGHT: 300px; display:none; padding: 5 0 0 5; margin-top: 2px">
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
                            </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <label>{{__('lang.category')}}: </label>
                    <select  name="category" class="select form-control">
                        <option value="" selected>{{__('lang.select-category')}}</option>
						@foreach ($threadCategories as $threadCategory)
                                <option value="{{$threadCategory->id}}" {{ @$_GET['category'] == $threadCategory->id ? 'selected' : '' }}>{{ $threadCategory->name }}</option>
                        @endforeach
                    </select>
                        @if($errors->has('category'))
                            <div class="errorMsg" id="category">{{ $errors->first('category') }}</div>
                        @endif
                </div>
                <div class="col">
                    <label for=""><br></label><br>
                    <button type="submit" class="btn-theme">{{ __('lang.apply-filter') }}</button>
                </div>
            </div>
        </div>
    </form>
@elseif(Auth::user()->getRoleNames()->first() == 'admin')
    <form class="container-fluid max-width" method="GET" id="category-form">
        <div class="white-wrapper pb-4">
            <div class="row align-items-end">

                <div class="col-sm-2">
                    <label>{{__('lang.discussion-forum-type')}}: </label>
                    <select  name="forum_type" class="select form-control"  id="forum_type" required>
                        <option value="0" {{ (@$_GET['forum_type'] != '' && (@$_GET['forum_type']) == 0 )? 'selected' : '' }}>{{__('lang.public')}}</option>
                        <option value="1" {{ @$_GET['forum_type'] == 1 ? 'selected' : '' }}>{{__('lang.private')}}</option>
                    </select>
                        @if($errors->has('type'))
                            <div class="errorMsg" id="type">{{ $errors->first('type') }}</div>
                        @endif
                </div>
                @if(request()->get('forum_type') == 1)
                    <div id="private-div" style="display:contents;">
                @else
                    <div id="private-div" style="display: none;">
                @endif

                    <div class="col-sm-2">
                        <label>{{ __('lang.region') }}:</label>
                        <button  id="region-checkbox-select" onclick="showHideDropdown(event, 'regions-checkbox')" class="select form-control" type="button" id="region" style=" padding-right: 20px;" required>
                        {{__('lang.select-region')}} <span class="custom-dropdown-symbol" style="pointer-events: none;"> &#9013; </span>
                        </button>

                        <div class="shadow rounded menu region-checkbox-div" id="regions-checkbox" style="OVERFLOW-Y:scroll; position: absolute; background: white; z-index: 1; WIDTH:84%; HEIGHT: 300px; display:none; padding: 5 0 0 5; margin-top: 2px">
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
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <label>{{__('lang.category')}}: </label>
                    <select  name="category" class="select form-control">
                        <option value="" selected>{{__('lang.select-category')}}</option>
						@foreach ($threadCategories as $threadCategory)
                                <option value="{{$threadCategory->id}}" {{ @$_GET['category'] == $threadCategory->id ? 'selected' : '' }}>{{ $threadCategory->name }}</option>
                        @endforeach
                    </select>
                        @if($errors->has('category'))
                            <div class="errorMsg" id="category">{{ $errors->first('category') }}</div>
                        @endif
                </div>
                <div class="col">
                    <label for=""><br></label><br>
                    <button type="submit" class="btn-theme">{{ __('lang.apply-filter') }}</button>
                </div>
            </div>
        </div>
    </form>
@else
<form class="container-fluid max-width" method="GET" id="category-form">
        <div class="white-wrapper pb-4">
            <div class="row align-items-end">

                <div class="col-sm-2">
                    <label>{{__('lang.discussion-forum-type')}}: </label>
                    <select  name="forum_type" class="select form-control"  id="forum_type" required>
                        <option value="0" {{ (@$_GET['forum_type'] == '' || (@$_GET['forum_type']) == 0 )? 'selected' : '' }}>{{__('lang.public')}}</option>
                        <option value="1" {{ @$_GET['forum_type'] == 1 ? 'selected' : '' }}>{{__('lang.private')}}</option>
                    </select>
                        @if($errors->has('type'))
                            <div class="errorMsg" id="type">{{ $errors->first('type') }}</div>
                        @endif
                </div>

                <div class="col-sm-2">
                    <label>{{__('lang.category')}}: </label>
                    <select  name="category" class="select form-control">
                        <option value="" selected>{{__('lang.select-category')}}</option>
						@foreach ($threadCategories as $threadCategory)
                                <option value="{{$threadCategory->id}}" {{ @$_GET['category'] == $threadCategory->id ? 'selected' : '' }}>{{ $threadCategory->name }}</option>
                        @endforeach
                    </select>
                        @if($errors->has('category'))
                            <div class="errorMsg" id="category">{{ $errors->first('category') }}</div>
                        @endif
                </div>
                <div class="col">
                    <label for=""><br></label><br>
                    <button type="submit" class="btn-theme">{{ __('lang.apply-filter') }}</button>
                </div>
            </div>
        </div>
</form>
@endif
<div class="container-fluid max-width mt-3">
    <form class="d-lg-flex justify-content-end align-items-center  padding-responsive" method="GET">
        @if(isset($_GET['search']))
            <input type="text" placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-control" id="search" name="search"
                value="{{ $_GET['search'] }}">
        @else
            <input type="text" placeholder="{{ __('lang.search-placeholder') }}" class="form-control" id="search" name="search">
        @endif
        <button type="submit" class="btn-theme ml-2 mr-3">{{ __('lang.search') }}</button>

    </form>
    @if(count($threads)>0) 
        @foreach ($threads as $thread)
            <div class="discussion" >
                <img src="{{ $thread->creator->image ? asset('storage' . Config::get('constant.PROFILE_PICTURES') . $thread->creator->image) : asset('assets/images/avatar_default.png') }}" class="usericon">
                <div style="width:100%">
                    <div class="flex justify-content-between"style="width:100%  ">
                        <p class="mb-0"><b class="mr-2">{{$thread->creator->name}}</b>
                            {{ $thread->created_at->diffForHumans() }}  

                            <span class="text-gray">{{__('lang.category')}}: {{$thread->category_id ? $thread->category->name : 'N/A'}}</span>
                        </p>   
                        
                            <div class="flex">
                                @if((count($thread->threadSubscriptions) > 0) && $thread->threadSubscriptions[0]->user_id == Auth::user()->id)
                                <form action="{{ url($routeSlug . '/forum/thread/unsubscribe') }}" method="POST" class="mb-0 mr-2">
                                    @csrf
                                    <input type="hidden" name="thread_id" value="{{$thread->id}}">
                                    <button type='submit' class='btn-theme btn-sm'>{{__('lang.unsubscribe')}}</button>
                                </form>
                                @else
                                <form action="{{ url($routeSlug . '/forum/thread/subscribe') }}" method="POST"  class="mb-0 mr-2">
                                    @csrf
                                    <input type="hidden" name="thread_id" value="{{$thread->id}}">
                                    <button type='submit' class='btn-theme btn-sm '>{{__('lang.subscribe')}}</button>
                                </form>
                                @endif
                                @if($thread->status == 0)
                                    <span class="text-danger">
                                    <i class="fa fa-ban" aria-hidden="true"></i>
                                    {{__('lang.closed')}}
                                    </span>
                                @endif

                                @if($thread->status == 1)
                                    <span class="text-success"><i class="fa fa-check" aria-hidden="true"></i> {{__('lang.active')}} 
                                    </span>
                                @endif

                                @if((count($thread->pinThreads) > 0) && $thread->pinThreads[0]->pinned_by == Auth::user()->id)
                                 &nbsp; &nbsp;<i class="fa fa-thumb-tack" aria-hidden="true" style="font-size: 20px;color: #3490dc;"></i>
                                @endif<br>
                            </div>
                    </div>
                        {!! $thread->title !!} <br>
                        @if($thread->embedded_link == null)
                            <span class="text-dim">
                            {!! $thread->body !!}
                            </span>
                        @endif
                    </p>
                    <a href="{{ 'threads/' . $thread->id }}" class="btn-theme-border" style="padding: 2px 16px;"> {{__('lang.view')}}</a>
                    <div class="d-footer">
                        <div>
                            <span class="text-gray mr-2">{{$thread->is_liked_by_count}} {{__('lang.likes')}} </span>
                            <span class="text-gray">{{$thread->replies_count}} {{__('lang.replies')}}</span>
                        </div>
                    </div>
                </div>

            </div>

        @endforeach
    @else
                <span style="margin-left:40%">{{ __('lang.no-record') }}</span>

    @endif    

{{ $threads->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}
</div>
<script>
    $('#forum_type').change(function(){
        if($('#forum_type').val() == 1) {
            $("#private-div").css('display', 'contents');
        } else {
            $("#private-div").css('display', 'none');
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

    $("#country-checkbox").focusout(function(){
    $(this).hide();
    });

    $("#region-checkbox").focusout(function(){
    $(this).hide();
    });

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
</script>
@endsection
