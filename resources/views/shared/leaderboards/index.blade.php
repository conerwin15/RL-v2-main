@extends('layouts.app')

@section('content')

<div class="container-fluid max-width">
    @if($EmpOfTheMonth)
        <div class="f-emp">
            <h5 class="bg-blue" style="margin: -15px -15px  16px -15px">{{ __('lang.featured-trainee-of-the-month') }}</h5>
            <div class="flex justify-content-start">
                <div class="mr-2">
                <img src="{{ $EmpOfTheMonth->user->image ? asset('storage' . Config::get('constant.PROFILE_PICTURES') . $EmpOfTheMonth->user->image) : asset('assets/images/avatar_default.png') }}"
                    class="f-employee">
                </div>

                <div class="emp-details">
                    <div class="basic-details">
                    <p  class="mb-0"><b>{{$EmpOfTheMonth->user->name}}</b><br></p>
                    <p class="flex align-items-center justify-content-start mb-2">
                        <svg width="18" viewBox="0 0 63.79 57" class="mr-1">
                            <g id="Group_1" data-name="Group 1" transform="translate(-564.605 -1439)">
                                <path id="Path_1" data-name="Path 1"
                                    d="M43.437,0A4,4,0,0,1,46.9,2L59.852,24.5a4,4,0,0,1,0,3.99L46.9,51a4,4,0,0,1-3.467,2H17.563A4,4,0,0,1,14.1,51L1.148,28.5a4,4,0,0,1,0-3.99L14.1,2a4,4,0,0,1,3.467-2Z"
                                    transform="translate(566 1441)" fill="#fad82b" />
                                <path id="Path_2" data-name="Path 2"
                                    d="M46.426,0A4,4,0,0,1,49.9,2.019l13.969,24.5a4,4,0,0,1,0,3.963L49.9,54.981A4,4,0,0,1,46.426,57H18.574A4,4,0,0,1,15.1,54.981L1.13,30.481a4,4,0,0,1,0-3.963L15.1,2.019A4,4,0,0,1,18.574,0Z"
                                    transform="translate(564 1439)" fill="#fad82b" opacity="0.41" />
                                <path id="Path_3" data-name="Path 3"
                                    d="M33.825.982l-3.644,7.39L22.026,9.557a1.786,1.786,0,0,0-.988,3.047l5.9,5.748-1.392,8.122a1.785,1.785,0,0,0,2.587,1.88l7.294-3.834,7.294,3.834a1.786,1.786,0,0,0,2.59-1.88l-1.393-8.12,5.9-5.744a1.786,1.786,0,0,0-.988-3.047L40.672,8.374,37.028.984a1.787,1.787,0,0,0-3.2,0Z"
                                    transform="translate(561.57 1453.035)" fill="#fff" />
                            </g>
                        </svg>
                        <small class="text-gray">{{$EmpOfTheMonth->points}} Points | {{$EmpOfTheMonth->user->country->name}}</small>
                    </p>
                    <p>{{$EmpOfTheMonth->featured_text}}</p>
                </div>
            </div>
        </div>
    @endif
</div> 
</div> 


<div class="container-fluid max-width">
        <form class="flex justify-content-end align-items-cener mt-4" method="GET">
            @if(isset($_GET['name']))
                <input type="text" placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-controller form-control w-300 mr-2" id="search" name="name" value="{{ $_GET['name'] }}" style="max-width:100% !important">
            @else
                <input type="text" placeholder="{{__('lang.search-by-name-placeholder')}}" class="form-controller form-control w-300 mr-2" id="search" name="name" style="max-width:100% !important">
            @endif
            <button type="submit" class="btn-theme">{{ __('lang.search') }}</button>
        </form>
    
        <div class="white-wrapper mt-2">
        
        <h5 class="bg-blue" style="margin: -15px -15px  16px -15px">{{ __('lang.global-leaderboard') }}</h5>
        <div class="container-fluid">
            <table class="table data-table">

                    <thead>
                        <tr>
                            <th>{{ __('lang.user-photo') }}</th>
                            <th>{{ __('lang.name') }}</th>
                            <th>{{ __('lang.country') }}</th>
                            <th>{{ __('lang.region') }}</th>
                            <th>{{ __('lang.points') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                    </tbody>
                </table>
            </div>    
        </div>
        </div>
</div>
@section('scripts')
<script>
    /*********** datatable ***********/
        $(document).ready(function() {

            var ajaxUrl = app_url + "/" + logged_user + "/leaderboard" + window.location.search;
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                cache : false,
                processData: false,

                ajax:  ajaxUrl,

                columns: [
                    {
                        data: 'image',
                        "render": function (data)
                         {
                            return '<div  class="flex align-items-center"><img src="'+data+'"class="leaderboard-user"></div>';
                         },
                         orderable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'country',
                        name: 'country'
                    },
                    {
                        data: 'region',
                        name: 'region'
                    },
                    {
                        data: 'points',
                        "render":function (data)
                        {
                            return '<svg  width="20" viewBox="0 0 65 57"><g transform="translate(-564 -1439)"><path d="M43.437,0A4,4,0,0,1,46.9,2L59.852,24.5a4,4,0,0,1,0,3.99L46.9,51a4,4,0,0,1-3.467,2H17.563A4,4,0,0,1,14.1,51L1.148,28.5a4,4,0,0,1,0-3.99L14.1,2a4,4,0,0,1,3.467-2Z" transform="translate(566 1441)" fill="#777d91"/><path d="M46.426,0A4,4,0,0,1,49.9,2.019l13.969,24.5a4,4,0,0,1,0,3.963L49.9,54.981A4,4,0,0,1,46.426,57H18.574A4,4,0,0,1,15.1,54.981L1.13,30.481a4,4,0,0,1,0-3.963L15.1,2.019A4,4,0,0,1,18.574,0Z" transform="translate(564 1439)" fill="#777d91" opacity="0.41"/><path d="M37.089,1.225l-4.537,9.2L22.4,11.9a2.224,2.224,0,0,0-1.23,3.793l7.343,7.156L26.78,32.96A2.222,2.222,0,0,0,30,35.3l9.08-4.773,9.08,4.773a2.224,2.224,0,0,0,3.224-2.341L49.65,22.851,56.993,15.7a2.224,2.224,0,0,0-1.23-3.793l-10.15-1.48-4.537-9.2a2.225,2.225,0,0,0-3.988,0Z" transform="translate(557.914 1449.537)" fill="#fff"/></g></svg> ' +data;
                        }
                    },
                ],
                "searching": false,
                "bLengthChange": false,
                'order': [4, 'desc']
            });
        });
    </script>
@endsection
@endsection
