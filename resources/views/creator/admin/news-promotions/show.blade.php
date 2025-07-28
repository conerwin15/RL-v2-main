@extends('layouts.app')

@section('content')
{{-- ▸ 1. Breadcrumb now points to /news‑promotions, not /admin/news‑promotions --}}
<div class="dash-title container">
    <div>
        <a href="{{ url('news-promotions') }}">
            <b>{{ __('lang.news-promotions') }} &gt;</b>
        </a>
        <span class="bradcrumb">{{ __('lang.show-news-promotions') }}</span>
    </div>
</div>

<div class="container">
    <div class="white-wrapper">

        <div class="show-details">
            @if($newsRecord->media)
                @php
                    /* grab the file extension once and lowercase it */
                    $ext = strtolower(pathinfo($newsRecord->media, PATHINFO_EXTENSION));
                @endphp

                {{-- ▸ 2. Same media logic as before --}}
                @if(in_array($ext, $videoFormat))
                    <video class="details-video" controls>
                        <source src="{{ asset('storage' . $viewStoragePath . $newsRecord->media) }}"
                                type="video/mp4">
                    </video>

                @elseif(in_array($ext, $imageFormat))
                    <img src="{{ asset('storage' . $viewStoragePath . $newsRecord->media) }}"
                         class="img-fluid"/>

                @else
                    {{-- ▸ 3. Download link without admin prefix --}}
                    <a href="{{ url('news-promotions/' . $newsRecord->id . '/attachment') }}"
                       target="_blank"
                       class="file-details">
                        <span>
                            <i class="fa fa-paperclip" aria-hidden="true"></i>
                            {{ __('lang.view-attachment') }}
                        </span>
                    </a>
                @endif
            @endif
        </div>

        <h5 class="color">{{ $newsRecord->title }}</h5>

        <div class="ckeditor-text">
            {!! $newsRecord->description !!}
        </div>

        <div>
            {{ __('lang.region') }}:
            {{ $newsRecord->region_id ? $newsRecord->region->name : 'N/A' }}
        </div>
    </div>
</div>
@endsection
