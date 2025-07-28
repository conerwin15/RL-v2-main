
@extends('layouts.app')
@section('content')

<div class="dash-title container">
    <div>
        <a href="{{ url('admin/sales-tips') }}"><b>{{__('lang.sales-tips')}} &gt;</b></a>
        <span class="bradcrumb">{{__('lang.show-sales-tip')}}</span>
    </div>
</div>

<div class="container">
    <div class="white-wrapper">
    <div class="show-details">
    @if($salesRecord->media != NULL)
        @php   $ext = explode('.', $salesRecord->media); @endphp
         @if(in_array(strtolower($ext[1]),  $videoFormat))
                <video class="details-video" controls  ><source src="{{ asset('storage' . $viewStoragePath . $salesRecord->media) }}" type="video/mp4"> </video>
            @elseif(in_array(strtolower($ext[1]),  $imageFormat))
                <img src="{{ asset('storage' . $viewStoragePath . $salesRecord->media) }}"  class="img-fluid"/>
            @else
                <a href="{{url('admin/sales-tips/' . $salesRecord->id . '/attachment')}}" target="_blank" class="file-details">
                    <span><i class="fa fa-paperclip" aria-hidden="true"></i> View Attachment</span>
                </a>
            @endif
    @endif
        </div>
        <h5 class="color">{{ $salesRecord->title }}</h5>
        <div class="ckeditor-text">
        {!! $salesRecord->description !!}
        </div>
        <div>
         Region: {{ $salesRecord->region_id ? $salesRecord->region->name : 'N/A'}}
        </div>
    </div>
</div>

@endsection

