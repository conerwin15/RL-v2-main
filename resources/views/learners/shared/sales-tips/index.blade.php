
@extends('layouts.app')

@section('content')

<div class="dash-title container-fluid max-width">
    <b>{{__('lang.sales-tips')}}</b>
</div>

<div class="container-fluid max-width">
    @if(count($salesTips)>0)
    @foreach($salesTips as $salesTip)
    <div class="white-wrapper white-strip">
    @if($salesTip->media != NULL) 
         
         @php   $ext = explode('.', $salesTip->media); @endphp
        
         @if(in_array(strtolower($ext[1]), $imageFormat))
         <div class="img-box">
             <img src="{{ asset('storage' . $viewStoragePath . $salesTip->media) }}">
         </div>
         @elseif (in_array(strtolower($ext[1]), $videoFormat))
             <video controls>
                 <source  src="{{ asset('storage' . $viewStoragePath . $salesTip->media) }}" type="video/mp4">
                 {{ __('lang.video-support-message') }}
             </video>    

         @else
         <div class="attachment">
             <a href="{!! url($routeSlug . '/sales-tips/'. $salesTip->id . '/attachment') !!}" target="_blank" ><img src="{{ asset('assets/images/pdf.png') }}" title="image" ></a> &nbsp;
         </div>
         @endif
 @endif 
 <div class="box-col">
        <p class="text-right mt-0 mb-0 gray"><small> {{ __('lang.created-on') }}: {{date('d M Y', strtotime($salesTip->created_at))}}</small></p>
           
           <a href="{{url($routeSlug . '/sales-tips/' . $salesTip->id)}}"  >
               <h6 class="color"><b>{{$salesTip->title}}</b></h6> 
           </a>
        <div class="ckeditor-text">{!!$salesTip->description!!}</div>
        <div class="bottom-text ">
            <div></div>
            <div class="text-right">
                <a href="{{url($routeSlug . '/sales-tips/' . $salesTip->id)}}" class="link" ><i class="fa fa-eye" aria-hidden="true"></i> {{ __('lang.view') }}</a> &nbsp;
            </div>
                </div>
        </div>


    </div>
    @endforeach
    @else
        <h4 style="text-align: center;">{{__('lang.no-record')}} </h4>
    @endif  
</div>


{!! $salesTips->links('vendor.pagination.bootstrap-4') !!}
    
@endsection