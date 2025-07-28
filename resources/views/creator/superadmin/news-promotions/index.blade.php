@extends('layouts.app')

@section('content')

<div class="dash-title container-fluid max-width">
    <b>{{__('lang.news-promotions')}}</b>
    <a class="btn-theme" href="{{ url('superadmin/news-promotions/create') }}">+ {{__('lang.create-news-promotions')}}</a>
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
                    <label>{{ __('lang.job-role') }}:</label>
                    <select name="filter_jobrole" class="select form-control"  required>
                        <option  disabled> {{ __('lang.select') }}  {{ __('lang.job-role') }} </option>
                        <option value="-1"> {{ __('lang.all') }} </option>
                        @foreach($jobRoles as $jobRole)
                            <option value="{{ $jobRole->id }}" id="jobRoleId"
                                {{ @$_GET['filter_jobrole'] == $jobRole->id ? "selected" : '' }}>
                                {{ $jobRole->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-sm-2">
                <label>{{ __('lang.region') }}:</label>
                <select name="filter_region" class="select form-control" id="region" disabled  required>
                    <option value="-1">{{ __('lang.select') }} {{ __('lang.region') }}</option>
                </select>
            </div>
           
            <div class="col-sm-2">
                <label>{{ __('lang.group') }}:</label>
                <select name="filter_group"  class="select form-control"  required>
                    <option  disabled> {{ __('lang.select') }}  {{ __('lang.group') }} </option>
                    <option value="-1"> {{ __('lang.all') }} </option>
                    @foreach($groups as $group)
                        <option value="{{ $group->id }}" id="groupId"
                            {{ @$_GET['filter_group'] == $group->id ? "selected" : '' }}>
                            {{ $group->name }}
                        </option>
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
@if(count($newsPromotions) > 0)
    @foreach ($newsPromotions as $news)
  
    <div class="white-wrapper white-strip" >
       <div>
       @if($news->media != NULL)    
                @php   $ext = explode('.', $news->media); @endphp

                @if(in_array(strtolower($ext[1]), $imageFormat))
                <div class="img-box">
                    <img src="{{ asset('storage' . $viewStoragePath . $news->media) }}" >
                </div>
                @elseif (in_array(strtolower($ext[1]), $videoFormat))
                    <video controls>
                        <source  src="{{ asset('storage' . $viewStoragePath . $news->media) }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>    

                @else
                <div class="attachment">
                    <a href="{!! url('/superadmin/news-promotions/'. $news->id . '/attachment') !!}" target="_blank" ><img src="{{ asset('assets/images/pdf.png') }}" title="image"></a> &nbsp;
                </div>
                @endif
        @endif  
       </div>  
        
        <div class="box-col">
            <p class="text-right mb-0 gray">
            <small>{{ __('lang.created-by') }}: {{date('d M Y', strtotime($news->created_at))}}</small> 
                | <small>{{ __('lang.created-by') }}: {{$news->createdBy->name}}</small>
                </p>
                <a href="{{url('superadmin/news-promotions/' . $news->id)}}" >
                    <h6 class="color mt-0"><b>{{$news->title}}</b></h6>
                </a>
                <div class="ckeditor-text"> {!! $news->description !!}</div>
           
                <div class="bottom-text">
                    <small class="text-left">{{__('lang.country')}}: {{ $news->country ? $news->country->name : 'N/A'}} | {{__('lang.job-role')}}: {{ $news->JobRole ? $news->JobRole->name : 'N/A'}} | {{__('lang.region')}}: {{ $news->region ? $news->region->name : 'N/A'}} | {{__('lang.group')}}: {{$news->group ? $news->group->name : 'N/A'}}</small>
                    <!-- <small class="text-left ">Region: {{ $news->region ? $news->region->name : 'N/A'}}</small>
                    <small class="text-left  pl-1 ml-1" style="border-left:1px solid #ccc">Country: {{ $news->country ? $news->country->name : 'N/A'}}</small>
                    
                    <small class="text-left pl-1 ml-1" style="border-left:1px solid #ccc">JobRole: {{ $news->JobRole ? $news->JobRole->name : 'N/A'}}</small>
                    <small class="text-left pl-1 ml-1" style="border-left:1px solid #ccc">Group: {{$news->group ? $news->group->name : 'N/A'}}</small>
                    -->
                <div class="text-right actionbuttons">
                    <a href="{{  url('superadmin/news-promotions', $news->id) }}"><i class="fa fa-eye" aria-hidden="true"></i> {{__('lang.view')}}</a>
                    <form action="{{  url('superadmin/news-promotions/' . $news->id . '/edit' ) }}" method="GET" class="text-right">
                        @csrf
                        <button type="submit" class="text-danger" style="border: 0px; background:transparent"><i class="fa fa-pencil" aria-hidden="true"></i> {{__('lang.edit')}} </button>
                    </form>
                
                    <form action="{{  url('superadmin/news-promotions', $news->id) }}" method="POST" class="text-right">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-danger" style="border: 0px; background:transparent" onclick="return confirm('{{ __('lang.delete-confirmation-text') }}')"><i class="fa fa-trash" aria-hidden="true"></i> {{__('lang.delete')}} </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
@else
    <div style="text-align: center;"> {{ __('lang.no-record') }}
    </div>
@endif
</div>
{!! $newsPromotions->appends(request()->query())->links('vendor.pagination.bootstrap-4')!!}
    
@endsection