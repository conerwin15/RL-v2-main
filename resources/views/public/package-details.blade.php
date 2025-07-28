@extends('layouts.app')

@section('content')

<div class="piaggio-alert">
    <div id="cartAlert"></div>
</div>

<div class="dash-title container-fluid mt-2">
    <div>
        <a href="{{ url('/') }}"><b>{{ __('lang.packages') }} &gt;</b></a>
        <span class="bradcrumb">Package Detail</span>
    </div>
</div>
<script>
    function showCard(element) {
        element.querySelector('.card').style.display = 'block';
    }

    function hideCard(element) {
        element.querySelector('.card').style.display = 'none';
    }
</script>

<div class="container">
    <div class="row mt-3">
        <div class="col-sm-4">
            <div class="card" style="width: 90%; height: 100%;">
                <img src="{{($packageDetail->image != null) ? asset('storage' . Config::get('constant.LEARNING_PACKAGE') . $packageDetail->image) : asset('assets/images/avatar_default.png')}}"
                    alt="Sample photo" width="100%;">
                <div class="text-center mt-2">
                    <h3 class="text-center">{{ucfirst($packageDetail->name)}}</h3>
                    <p class="text-center"> ({{__('lang.category')}}:{{$packageDetail->category->name}})</p>
                    <span>• {{$reallybotCount}}&nbsp;{{__('lang.pre-selected-reallybots')}}</span><br>
                    <span>• {{$courseCount}}&nbsp;{{__('lang.courses')}}</span><br>
                    <span>• {{$mediaCount}}&nbsp;{{__('lang.media-pdfs')}}</span><br><br>
                    <h5 class="text-center"><span style="text-decoration: line-through;">$ {{$packageDetail->price}}</span><br><span>$ {{$packageDetail->discount_price}}</span></h5>
                </div>

                <div class="text-center">
                    @if($cartPackages)
                    <?php $package = false; ?>
                    @foreach($cartPackages as $cartPackage)
                    @if($cartPackage['id'] == $packageDetail->id)
                    <?php $package = true; ?>
                    @break
                    @endif
                    @endforeach
                    @if($package)
                    <button type="button" class="btn btn-primary mb-1 mt-4 ml-3 btn-lg" disabled>Already Added</button>
                    @else
                    <a href="{{url('/add-to-cart/'. $packageDetail->id)}}"><button type="button"
                            class="btn btn-primary mb-1 mt-4 ml-3 btn-lg">Add to Cart</button></a>
                    @endif
                    @else
                    <a href="{{url('/add-to-cart/'. $packageDetail->id)}}"><button type="button"
                            class="btn btn-primary mb-1 mt-4 ml-3 btn-lg">Add to Cart</button></a>
                    @endif

                    <a href="{{url('/')}}"><button type="button"
                            class="btn btn-primary mb-1 mt-2 ml-3 mr-2 btn-lg">Continue Shopping</button></a>
                    <a href="{{url('/public/checkout')}}"><button type="button"
                            class="btn btn-info mb-2 ml-2 btn-lg">Checkout</button></a><br>
                </div>
            </div>
        </div>


        <div class="col-sm-8">
            <div class="card" style="width:43%;  height:70vh; border:none; position:fixed;">
                <div class="row ml-1 mt-5 reallybots-info">
                    <div style="margin-left:1.5rem; margin-right:2rem;">
                        <h5>ReallyBots</h5>
                    </div>
                    @if($packageDetail->learningPath)
                    <div id="reallybotsCarousel" class="carousel slide " data-ride="carousel" >
                        <div class="carousel-inner">
                            @php $index = 0 @endphp
                            @foreach($packageDetail->learningPath as $learningPath)
                            @if(strpos($learningPath->iframe_link, 'reallybot'))
                            @if($index % 3 == 0)
                            <div class="carousel-item @if($index == 0) active @endif">
                                <div class="row">
                                    @endif
                                    <div class="col">
                                        <div class="card package-info-card" >
                                            <img src="{{asset('storage' . Config::get('constant.LEARNING_PATH_STORAGE') .'/'. $learningPath->featured_image)}}"
                                                class="img-fluid" />
                                            <h6>{{ $learningPath->name }}</h6>
                                        </div>
                                    </div>
                                    @php $index++ @endphp
                                    @if($index % 3 == 0)
                                </div>
                            </div>
                            @endif
                            @elseif($learningPath->resources)
                            @foreach($learningPath->resources as $resource)
                            @if(strpos($resource->link, 'reallybot'))
                            @if($index % 3 == 0)
                            <div class="carousel-item @if($index == 0) active @endif">
                                <div class="row">
                                    @endif
                                    <div class="col">
                                        <div class="card package-info-card">

                                            <img src="{{asset('storage' . Config::get('constant.LEARNING_PATH_STORAGE') .'/'. $learningPath->featured_image)}}"
                                                class="img-fluid">

                                            <h6>{{$resource->title}}</h6>
                                        </div>
                                    </div>
                                    @php $index++ @endphp
                                    @if($index % 3 == 0)
                                </div>
                            </div>
                            @endif
                            @endif
                            @endforeach
                            @endif
                            @endforeach
                        </div>

                        <!-- Carousel controls within the third item -->
                        <a class="carousel-control-prev  mb-4 " href="#reallybotsCarousel" role="button"
                            data-slide="prev" style="color:red;">
                            <span class="carousel-control-prev-icon mb-4 " aria-hidden="true" style="color:red;"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="carousel-control-next mb-4" href="#reallybotsCarousel" role="button"
                            data-slide="next">
                            <span class="carousel-control-next-icon mb-4" aria-hidden="true" style="color:red;"></span>
                            <span class="sr-only">Next</span>
                        </a>
                    </div>
                    @endif
                    <a class="carousel-control-prev  mb-4 " href="#reallybotsCarousel" role="button" data-slide="prev"
                        style="color:red;">
                        <span class="carousel-control-prev-icon mb-4 mr-5" aria-hidden="true" style="color:red;"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next mb-4" href="#reallybotsCarousel" role="button" data-slide="next">
                        <span class="carousel-control-next-icon mb-4 ml-5" aria-hidden="true" style="color:red;"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
                

                <div class="row ml-3 mt-5 courses-info">
                    @if($courseCount > 0 || $reallybotCount > 0)
                    <div style="margin-left:-6rem;">
                        <h5>Courses</h5>
                    </div>
                    @else
                    <div style="margin-left:1rem;">
                        <h5>Courses</h5>
                    </div>
                    @endif
                    
                    @php $courseCount = 0 @endphp
                    @if($packageDetail->learningPath)
                    <div id="courseCarousel" class="carousel slide course" data-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($packageDetail->learningPath as $learningPath)
                            @if($learningPath->resources)
                            @foreach($learningPath->resources as $resource)
                            @if($resource->type == "course_link")
                            @if($courseCount % 3 == 0)
                            <div class="carousel-item @if($courseCount == 0) active @endif">
                                <div class="row">
                                    @endif
                                    <div class="col">
                                        <div class="card package-info-card">

                                            <img src="{{asset('storage' . Config::get('constant.LEARNING_PATH_STORAGE') .'/'. $learningPath->featured_image)}}"
                                                class="img-fluid" />
                                            <h6> {{$resource->title}}</h6>
                                        </div>
                                    </div>
                                    @php $courseCount++ @endphp
                                    @if($courseCount % 3 == 0)
                                </div>
                            </div>
                            @endif
                            @endif
                            @endforeach
                            @endif
                            @endforeach
                            <!-- Carousel controls within the third item -->
                            <a class="carousel-control-prev  mb-4 " href="#courseCarousel" role="button"
                                data-slide="prev" style="color:red;">
                                <span class="carousel-control-prev-icon mb-4 " aria-hidden="true"
                                    style="color:red;"></span>
                                <span class="sr-only">Previous</span>
                            </a>
                            <a class="carousel-control-next mb-4" href="#courseCarousel" role="button"
                                data-slide="next">
                                <span class="carousel-control-next-icon mb-4" aria-hidden="true"
                                    style="color:red;"></span>
                                <span class="sr-only">Next</span>
                            </a>
                        </div>
                    </div>
                    @endif
                    <a class="carousel-control-prev mb-4" href="#courseCarousel" role="button" data-slide="prev"
                        style="color:red;">
                        <span class="carousel-control-prev-icon mb-4 mr-5" aria-hidden="true" style="color:red;"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next mb-4" href="#courseCarousel" role="button" data-slide="next">
                        <span class="carousel-control-next-icon mb-4 ml-5" aria-hidden="true" style="color:red;"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .package-info-card {
        min-width: 150px;
        max-width: 150px;
        max-height: 170px;
        min-height: 130px;
        border:none;
        /* background: #f8fafc; */
    }

    .package-info-card img {
        width: 100%;
    }

    .reallybots-info h5 {
        margin-top: 3rem;
    }

    .courses-info h5 {
        margin-top: 3rem;
    }
    .img {
        position: relative;
        cursor: pointer;
    }
    
    #courseCarousel {
        position:fixed;
        margin-right:20rem;
        margin-left:-1rem;
    }

    #reallybotsCarousel {
        position:fixed;
        margin-left:7rem;
        margin-right:20rem;
    }
    
    .img-fluid {
        max-width: 100%;
        height: 110px;
    }
    @media screen and (max-width: 1300px) {
    .package-info-card{
        min-width: 100px;
        max-width: 100px;
        max-height: 110px;
        min-height: 110px;
        border:none;

    }
    .reallybots-info h5,
    .courses-info h5 {
        font-size: 14px;
        margin-top: 1.5rem; 
    }

    #courseCarousel{
        margin-right:18rem;
        margin-left:-1rem;
        

    } 
    #reallybotsCarousel {
        margin-left:7rem;
        margin-right:18rem;
    }

    .img-fluid {
        height: 90px; 
    }
}
   
</style>
<script type="text/javascript" src="http://localhost/reallylesson/public/js/jquery.js"></script>
@endsection