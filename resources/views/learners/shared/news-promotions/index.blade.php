@extends('layouts.app')
@section('content')

<div class="dash-title container-fluid">
    <b>{{__('lang.news-promotions')}}</b>
</div> 

<br />

<div class="container-fluid">
    @if(count($newsPromotions)>0)
    @foreach($newsPromotions as $news)
    <div class="white-wrapper white-strip">
    @if($news->media != NULL)    
                @php   $ext = explode('.', $news->media); @endphp

                @if(in_array(strtolower($ext[1]), $imageFormat))
                <div class="img-box">
                    <img src="{{ asset('storage' . $viewStoragePath . $news->media) }}" >
                </div>
                @elseif (in_array(strtolower($ext[1]), $videoFormat))
                    <video controls>
                        <source  src="{{ asset('storage' . $viewStoragePath . $news->media) }}" type="video/mp4">
                        {{ __('lang.video-support-message') }}
                    </video>    

                @else
                <div class="attachment">
                    <a href="{!! url($routeSlug . '/news-promotions/'. $news->id . '/attachment') !!}" target="_blank" ><img src="{{ asset('assets/images/pdf.png') }}" title="image"></a> &nbsp;
                </div>
                @endif
        @endif 

        <div class="box-col">
        <p class="text-right mt-0 mb-0 gray"><small>{{ __('lang.created-on') }}: {{date('d M Y', strtotime($news->created_at))}}</small></p>
           
           <a href="{{url($routeSlug . '/news-promotions/' . $news->id)}}"  >
               <h6 class="color"><b>{{$news->title}}</b></h6> 
           </a>
        <div class="ckeditor-text">{!!$news->description !!}</div>
        <div class="bottom-text ">
            <div></div>
            <div class="text-right">
                <a href="{{url($routeSlug . '/news-promotions/' . $news->id)}}" class="link" ><i class="fa fa-eye" aria-hidden="true"></i> {{__('lang.view')}}</a> &nbsp;
            </div>
                </div>
        </div>

                
    </div>
    @endforeach

    @else
        <h4 style="text-align: center;">{{__('lang.no-record')}} </h4>
    @endif    
</div>




{!! $newsPromotions->links('vendor.pagination.bootstrap-4') !!}

@endsection
