@extends('layouts.app')

@section('content')

<style>
    .cards {
        display: flex;
        flex-wrap: wrap;
        align-items: stretch;
    }

    .card {
        flex: 0 0 210px;
        margin: 10px;
        border: 1px solid #ccc;
        box-shadow: 2px 2px 6px 0px rgba(0, 0, 0, 0.3);
    }

    .card img {
        width: 100%;
        height: 15vw;
        object-fit: cover;
    }

    .card .text {
        padding: 0 20px 20px;
    }

    .card .text>button {
        background: gray;
        border: 0;
        color: white;
        padding: 10px;
        width: 100%;
    }
/* 
    .original-price {
        text-decoration: line-through;
    } */

    .carousel-item {
        height: 300px;
        max-height: 300px;
        width: 100%;
        max-width: 1000px;
        margin-left: 3rem;

    }

    .carousel-item img {
        height: 300px;
        width: 100%;
    }
    
</style>
<div class="dash-title container-fluid no-flex">
    <b>{{ __('lang.banners') }}</b>
    <div class="d-lg-flex align-items-center justify-content-end no-flex">
        <a class="btn-theme" id="backBtn" href="{{  url('/') }}" style="display:none;"> {{__('lang.back')}}</a>
    </div>
</div>

<br />

<div class="container carousel-div">
    <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            @php $c=1; @endphp
            @foreach($banners as $banner)
            <div class="carousel-item" id="slide_{{$c}}">
                <img class="d-block"
                    src="{{($banner->image!= null) ? asset('storage' . Config::get('constant.BANNER_STORAGE') . $banner->image) : asset('assets/images/avatar_default.png')}}"
                    alt="image">
            </div>
            @php $c++; @endphp
            @endforeach
        </div>
        <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
</div>

<br>

<div id="package-div">
    <div class="dash-title container-fluid no-flex">
        <b>{{ __('lang.packages') }}</b>
        <div class="d-lg-flex align-items-center justify-content-end no-flex">
        </div>
    </div><br>

    <div class="cards" style="display:flex; margin-left:18rem;">
    @if(count($packages)>0)
        @foreach($packages as $package)
        <article class="card">
            <img src="{{($package->image!= null) ? asset('storage' . Config::get('constant.LEARNING_PACKAGE') . $package->image) : asset('assets/images/avatar_default.png')}}"
                alt="Sample photo" style="width:209px; max-height:190px;"><br>
            <div class="text">
                <h5 style="text-align:center">{{ucfirst($package->name)}}</h5>
                <p style="text-align:center">({{__('lang.category')}}:{{$package->category->name}})</p>
                <label> </label><span>• {{$package->learning_path_count}}
                    &nbsp;{{__('lang.pre-selected-reallybots')}}</span><br>
                <label> </label><span>• {{$package->courseCount}}
                    &nbsp;{{__('lang.courses')}}</span><br>
                <label> </label><span>• {{$package->mediaCount}}
                    &nbsp;{{__('lang.media-pdfs')}}</span><br>
                <label> <strong>{{__('lang.price')}}:</strong></label> &nbsp;<span
                    class="original-price">${{$package->price}}</span>
                <label> <strong>{{__('lang.discount-price')}}:</strong></label>
                <span>${{$package->discount_price}}</span>

                <!-- <div style="margin-left:-10px;"><a href="{{url('/customer/login/'. $package->id)}}"><button class="ml-2 btn-theme buyNow" style="width:100px;" data-id="{{$package->id}}" >Buy Now</button></a></div> -->
                <div style="margin-left: -10px;">
                    <a
                        href="{{ url('/package/' . $package->id . '?reallybotCount=' . $package->learning_path_count.'&courseCount='.$package->courseCount . '&mediaCount=' .$package->mediaCount ) }}">
                        <button class="ml-2 btn-theme " style="width: 170px;" data-id="{{ $package->id }}">
                            Click for more details
                        </button>
                    </a>
                </div>
        </article>
        @endforeach
        @else
       <h3>Sorry, we couldn't find any packages realated to your search..</h3>
        @endif
    </div>
</div>
{{ $packages->appends(request()->query())->links('vendor.pagination.bootstrap-4') }}
<script type="text/javascript" src="http://localhost/reallylesson/public/js/jquery.js"></script>
<script>
    $(document).ready(function () {
        $('#slide_1').addClass('active');
        $('.pagination-div').show();
    });
</script>
@endsection