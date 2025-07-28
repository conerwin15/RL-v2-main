<header>
    <div class="navbar navbar-expand-md navbar-light navbar-laravel" style="height:60px">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ url('/') }}">
                <img src="{{asset('assets/images/logo.png')}}" alt="Piaggio">
            </a>
            <!--<form class="ml-auto" action="{{ url('/package/search') }}" method="GET">
                <div class="input-group custom-search-box">
                    <input type="text" name="search" id="search" class="form-control custom-search-input "
                        placeholder="Search in ReallyLesson">
                    <div class="input-group-append">
                        <button class="btn custom-search-button" type="submit">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
            <div class="icon-cart mr-4">
                <a href="{{url('/public/checkout')}}">
                    <i class="fa-solid fa-cart-shopping fa-2x mr-2"></i>
                    <span class="badge badge-pill badge-danger">
                        <?php
                            $publicViewController = app(\App\Http\Controllers\PublicViewController::class);
                            $cartPackagesCount = $publicViewController->cartPackages()->count();
                            echo $cartPackagesCount;
                        ?>
                    </span>
                </a>
            </div>
            <a href="{{url('/auth/login')}}"> <button class="btn btn-primary btn-lg my-2">Login</button> </a>
        </div>-->
    </div>
</header>

<style>
    .custom-search-box {
        max-width: 600px; 
        min-width: 500px; 
        margin-top: 1rem;
       
    }

    .custom-search-input {
        max-width: 400px; 
    }

    .custom-search-button {
        width: 60px;
        background-color: darkorange;
        color: white;
        margin-right: 10rem;
    }

    .icon-cart {
        margin-right: 2rem;
    }

    .icon-cart a {
        position: relative; 
        
    }

    .icon-cart span {
        right: 0;
        position: absolute;
        bottom: 23;
        font-size: 0.7rem;
    }

    #search {
        max-width: 100%;
    }

    @media (max-width: 767px) {

        .custom-search-box {
            max-width: 100%;
            min-width: 100%;
            margin-top: 1rem;
        }
    }
</style>
