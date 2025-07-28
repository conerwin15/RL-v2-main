
@extends('layouts.app')
@section('content')

<div class="dash-title container">
    <div>
        <a href="{{ url($routeSlug .'/news-promotions') }}"><b>{{__('lang.news-promotions')}} &gt;</b></a>
        <span class="bradcrumb">{{__('lang.show-news-promotions')}}</span>
    </div>
</div>

<div class="container">
    <div class="white-wrapper">
    <div class="show-details">
    @if($newsRecord->media != NULL)
        @php   $ext = explode('.', $newsRecord->media); @endphp
         @if(in_array(strtolower($ext[1]), $videoFormat))
                <video class="details-video" controls  ><source src="{{ asset('storage' . $viewStoragePath . $newsRecord->media) }}" type="video/mp4"> </video>
            @elseif(in_array(strtolower($ext[1]),  $imageFormat))
                <img src="{{ asset('storage' . $viewStoragePath . $newsRecord->media) }}"  class="img-fluid"/>
            @else
                <a href="{{url($routeSlug . '/news-promotions/' . $newsRecord->id . '/attachment')}}" target="_blank" class="file-details">
                    <span><i class="fa fa-paperclip" aria-hidden="true"></i> {{__('lang.view-attachment')}}</span>
                </a>
            @endif
    @endif
        </div>
        <h5 class="color">{{ $newsRecord->title }}</h5>
        <div class="ckeditor-text">
        {!!$newsRecord->description !!}
        </div>
    </div>
</div>

@endsection
