@extends('layouts.app')
 
@section('content')
<div class="dash-title container-fluid max-width">
    <b>{{__('lang.contact-piaggio')}}</b>
</div>

<div class="container-fluid max-width">
    <div class="flex align-ites-start justify-content-start support">
        <div class="list-group" id="list-tab" role="tablist">
            <p class="color">{{__('lang.select-query-type')}}</p>
           
            @foreach ($contactCategories as $contactCategory)
            <a class="list-group-item list-group-item-action " id="list-home-list" 
                data-toggle="list" href="#contactCategory{{$contactCategory->id}}" role="tab" aria-controls="home">
                <svg width="36" class="mb-2" viewBox="0 0 46.717 50.671"><g transform="translate(-29 -9.784)"><path d="M71.066,32.547h-.6V28.922c0-.176,0-.353,0-.53A10.944,10.944,0,0,0,55.83,12.171a19.008,19.008,0,0,0-3.158-.285c-9.816,0-17.807,7.642-17.807,17.035v3.626H33.65A4.537,4.537,0,0,0,29,37v7.647a4.554,4.554,0,0,0,4.65,4.47h4.741a.78.78,0,0,0,.8-.8V33.326a.763.763,0,0,0-.8-.779h-1.88V28.922c0-8.486,7.253-15.389,16.156-15.389.553,0,1.108.034,1.657.09a10.942,10.942,0,0,0,14.5,16.145v2.779H66.966a.783.783,0,0,0-.819.779V48.315a.8.8,0,0,0,.819.8h2.355l-.644.841a10.784,10.784,0,0,1-8.889,4.059,5.9,5.9,0,0,0-11.712.783,5.482,5.482,0,0,0,1.763,4,6.123,6.123,0,0,0,8.544-.165,5.239,5.239,0,0,0,1.425-2.966c.13,0,.26.007.39.007a12.425,12.425,0,0,0,9.78-4.7L71.4,49.129C74,49,75.717,47.48,75.717,45.256V37.609A5.347,5.347,0,0,0,74.4,34.2,4.366,4.366,0,0,0,71.066,32.547ZM37.541,47.468H33.65a2.907,2.907,0,0,1-3-2.824V37a2.891,2.891,0,0,1,3-2.8h3.89Zm15.8-26.732a9.305,9.305,0,1,1,9.305,9.305A9.306,9.306,0,0,1,53.343,20.736ZM57.191,57.5a4.375,4.375,0,0,1-3.166,1.31,4.22,4.22,0,0,1-4.3-4.01,4.245,4.245,0,0,1,8.476,0c0,.013,0,.026,0,.039A3.582,3.582,0,0,1,57.191,57.5ZM74.07,45.256c0,1.648-1.544,2.212-3,2.212H67.793V34.194h3.273a3.42,3.42,0,0,1,3,3.415Z" transform="translate(0)" fill="#328cb3"/><path d="M316.592,44.205A4.528,4.528,0,0,0,312,48.728v.806h1.646v-.806a2.879,2.879,0,1,1,3.776,2.735,2.5,2.5,0,0,0-1.718,2.392v2.367h1.646V53.855a.861.861,0,0,1,.584-.827,4.524,4.524,0,0,0-1.343-8.823Z" transform="translate(-253.879 -30.878)" fill="#328cb3"/><rect width="1.646" height="2.264" transform="translate(61.825 25.859)" fill="#328cb3"/></g></svg>
                <br> {{ $contactCategory->category_name }}</a>
            @endforeach
        </div>

        <div class="tab-content col" id="nav-tabContent">
            <div class="tab-pane fade show active" role="tabpanel" aria-labelledby="list-home-list">
                <p class="text-center"><svg  width="100" height="87.5" viewBox="0 0 100 87.5"><g transform="translate(0 -2)"><path d="M7.9,7.9,21.025,54.775,31.65,38.525l20,20,6.875-6.875L37.9,31.65l16.875-10Z" transform="translate(41.475 30.975)" fill="#cdd3e1"/><path d="M50,64.5H6.25V8.25h75V42l6.25,1.25V2H0V70.75H51.25Z" fill="#cdd3e1"/></g></svg>
               <br> {{__('lang.select-query-type-message')}}</p>
            </div>
            @foreach ($contactCategories as $contactCategory)
                <div class="tab-pane fade " id="contactCategory{{$contactCategory->id}}" role="tabpanel" aria-labelledby="list-home-list">
                <form action="{{ url($routeSlug . '/support/mail') }}" method="POST" enctype="multipart/form-data" id="add-sales-tips">
                    @csrf
                    <input type="hidden" name="id" value="{{ $contactCategory->id }}">
                    <input type="hidden" value="{{ $contactCategory->category_name }} " name="categoryName">
                    
                    <label for="" class="gray">{{ $contactCategory->category_name }}</label>
                    <textarea class="form-control" rows="8" name="text" placeholder="{{__('lang.query')}}" value="{{ old('text') }}" required></textarea>
                        @if($errors->has('text'))
                        <div class="errorMsg" id="textError">{{ $errors->first('text') }}</div>
                    @endif

                    <button type="submit" class="mt-3 btn-theme">{{__('lang.submit')}}</button>
                </div>
                </form>
            @endforeach
        </div>
    </div>
</div>

@endsection
