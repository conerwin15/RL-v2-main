@include('js-localization::head')
<html lang="{{ app()->getLocale() }}">
<head>
    @yield('js-localization.head')
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png')}}" type="image/x-icon">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Piaggio') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.3/css/fontawesome.min.css" integrity="sha384-wESLQ85D6gbsF459vf1CiZ2+rr+CsxRY0RpiF1tLlQpDnAgg6rwdsUF1+Ics2bni" crossorigin="anonymous">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}?v2" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}?v2" rel="stylesheet">
    <link  href="{{url('/')}}/assets/css/app.css?v2" rel="stylesheet">

    <script type="text/javascript" src="{{ asset('js/jquery.js') }}"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!---- Datatable --->
    <link href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" rel="stylesheet">

    <!-- font-awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    

    @yield('styles')

</head>

<body>

    @include('layouts.partials.header')
    @include('partials.flash-message')

    <div id="app" class="{{Auth::user() ? Auth::user()->roles[0]->name : null }}">

        <main>
            <div id="alert" class="piaggio-alert"></div>
            @yield('content')
        </main>
    </div>
 
    <!-- Scripts -->
    <script> var app_url ="{{url('')}}"; var angle = 0; </script>
    <script> var storage_url = "{{asset('storage')}}"; </script>
    <script> 
            var logged_user = "{{Auth::user() ? Auth::user()->roles[0]->name : null }}"; 
            var logged_user_id = "{{Auth::user() ? Auth::user()->id : null }}";
    </script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>  
    <script type="text/javascript" src="{{ asset('/assets/js/app.js') }}"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js" defer></script>
    <script type="text/javascript">

        $(document).ready(function(){
            $('body').find('.piaggio-alert .alert').css('animation' , 'alertIN  ease-in-out .35s forwards , alertOut  ease-in-out  .35s 4s forwards')
            setTimeout(() => {
                $('body').find('.piaggio-alert .alert').remove() 
            }, 6000);

            $('.piaggio-alert .alert').on('click' , function(){
                $(this).css('animation' , 'alertOut2 ease-in-out  .35s forwards')
            })
        })
    

    </script>
    @if (App::environment() == "piaggioprod")
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-46XEHMGWFH"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-46XEHMGWFH');
        </script>
    @endif


    @yield('scripts')
    
<script>
    $('.exp-icon a').click(function(){
        $(this).toggleClass('active')
        $('.sidebar').toggleClass('active')
        $('body').toggleClass('expend')
    })
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
        })

        $('.mobilemenu').click(function(){
           $('.topnav').toggle();
           $('.mobilemenu .fa-close , .mobilemenu .fa-bars').toggle()
        })
</script>
</body>
</html>