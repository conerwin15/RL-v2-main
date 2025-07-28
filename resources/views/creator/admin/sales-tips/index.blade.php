@extends('layouts.app')

@section('content')

<div class="dash-title container-fluid max-width">
    <b>{{__('lang.sales-tips')}}</b>
    <a class="btn-theme" href="{{ url('admin/sales-tips/create') }}">+ {{__('lang.create-sales-tip')}} </a>
</div>

    <form class="container-fluid" method="GET">
        <div class="white-wrapper pb-4">
            <div class="row align-items-end">
                <div class="col-sm-12">
                    <h6><b>{{__('lang.filters')}}</b></h6>
                </div>

            <div class="col-sm-2">
                <label>{{ __('lang.select-job-role') }}:</label>
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
                <select name="filter_region" class="select form-control"  required>
                    <option  disabled> {{ __('lang.select') }}  {{ __('lang.region') }} </option>
                    <option value="-1"> {{ __('lang.all') }} </option>
                    @foreach($regionFiters as $regionFiter)
                        <option value="{{ $regionFiter->id }}"
                            {{ @$_GET['filter_region'] == $regionFiter->id ? "selected" : '' }}>
                            {{ $regionFiter->name }}
                        </option>
                    @endforeach
                </select>
            </div>


            <div class="col-sm-2">
                <label>{{ __('lang.group') }}:</label>
                <select name="filter_group"  class="select form-control"  required>
                <option  disabled> {{ __('lang.select') }}  {{ __('lang.group') }} </option>
                    <option value="-1"> {{ __('lang.all') }}</option>
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
@if(count($salesTips) > 0)
    @foreach ($salesTips as $salesTip)

    <div class="white-wrapper white-strip">
        @if($salesTip->media != NULL)

                @php   $ext = explode('.', $salesTip->media); @endphp

                @if(in_array(strtolower($ext[1]),  $imageFormat))
                <div class="img-box">
                  <img src="{{ asset('storage' . $viewStoragePath . $salesTip->media) }}"> &nbsp; &nbsp; &nbsp;
                </div>
                  @elseif (in_array(strtolower($ext[1]),  $videoFormat))
                    <video controls>
                        <source  src="{{ asset('storage' . $viewStoragePath . $salesTip->media) }}" type="video/mp4">
                        {{__('lang.video-support-message')}}
                    </video>

                @else
                <div class="attachment">
                    <a href="{!! url('/admin/sales-tips/'. $salesTip->id . '/attachment') !!}" target="_blank" ><img src="{{ asset('assets/images/pdf.png') }}" title="image"></a> &nbsp;
                </div>
                @endif
        @endif
            <div class="box-col">
                <p class="text-right mb-0 gray">
                    <small>{{__('lang.created-on')}}: {{date('d M Y', strtotime($salesTip->created_at))}}</small> |
                    <small>{{ __('lang.created-by') }}: {{ucfirst($salesTip->createdBy->name)}}</small>
                </p>
                <a href="{{  url('admin/sales-tips', $salesTip->id) }}" >
                    <h6 class="color mt-0"><b>{{$salesTip->title}}</b></h6>
                </a>
                <div class="ckeditor-text">{!! $salesTip->description !!}</div>

                <div class="bottom-text">
                    <small class="text-left">{{__('lang.country')}}: {{ $salesTip->country ? $salesTip->country->name : 'N/A'}} | {{__('lang.job-role')}}: {{ $salesTip->JobRole ? $salesTip->JobRole->name : 'N/A'}} | {{__('lang.region')}}: {{ $salesTip->region ? $salesTip->region->name : 'N/A'}} | {{__('lang.group')}}: {{$salesTip->group ? $salesTip->group->name : 'N/A'}}</small>

                    <div class="text-right actionbuttons">

                        <a href="{{  url('admin/sales-tips', $salesTip->id) }}" target="_blank" class="link"><i class="fa fa-eye" aria-hidden="true"></i> {{__('lang.view')}}</a>

                        @if($salesTip->created_by == Auth::user()->id)
                        <form action="{{  url('admin/sales-tips/' . $salesTip->id . '/edit' ) }}" method="GET" class="text-right">
                            @csrf
                            <button type="submit" class="text-danger" style="border: 0px; background:transparent"><i class="fa fa-pencil" aria-hidden="true"></i> {{__('lang.edit')}} </button>
                        </form>

                        <form action="{{  url('admin/sales-tips', $salesTip->id) }}" method="POST" class="text-right mb-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-danger" style="border: 0px; background:transparent" onclick="return confirm('{{ __('lang.delete-confirmation-text') }}')"><i class="fa fa-trash" aria-hidden="true"></i> {{__('lang.delete')}} </button>
                        </form>
                        @endif

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
{!! $salesTips->links('vendor.pagination.bootstrap-4') !!}

@endsection
