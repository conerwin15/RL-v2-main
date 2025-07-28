<header>
    <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
        <div class="container-fluid navflex">
            <a class="navbar-brand" href="{{ url('/') }}">
             <img src="{{asset('assets/images/logo.png')}}" alt="Piaggio">
            </a>

            <div class="d-flex justify-content-end align-items-center">
                <!-- Left Side Of Navbar -->
                <div class="topnav">
                    <a href="{{ url('/superadmin/dashboard') }}"  class="{{ request()->is('dashboard') ? 'active' : '' }}">{{ __('lang.dashboard') }}</a>
   
                    <div class="dropdown custom-drop drop3">
                        <button type="button" class="btn btn-secondary dropdown-toggle  {{ request()->is('users') ? 'active' : '' }}" id="dropdownMenuOffset"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="10,20">
                            {{ __('lang.user-management') }}
                            <svg viewBox="0 0 22.243 12.621">
                                <path d="M26.5,11.5l-9,9-9-9" transform="translate(-6.379 -9.379)" fill="none"
                                    stroke="#43b4e5" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-miterlimit="10" stroke-width="3" />
                            </svg>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuOffset">
                            <li><a class="dropdown-item" href="{{ url('/superadmin/users') }}">{{ __('lang.view-users') }}</a></li>
                            <!-- <li><a class="dropdown-item" href="{{ url('/superadmin/trainingadmins/create') }}">{{ __('lang.add-training-admin') }}</a></li>
                            <li><a class="dropdown-item" href="{{ url('/superadmin/dealers/create') }}">{{ __('lang.add-dealer') }}</a></li>
                            <li><a class="dropdown-item" href="{{ url('/superadmin/customers/create') }}">{{ __('lang.add-staff') }}</a></li> -->
                        </div>
                    </div>

                        <a class="dropdown-item" href="{{ url('/superadmin/packages') }}">{{ __('lang.packages') }}</a>
                        <a class="dropdown-item" href="{{ url('/superadmin/categories') }}">{{ __('lang.categories') }}</a>
                        <a class="dropdown-item" href="{{ url('/superadmin/banners') }}">{{ __('lang.banners') }}</a>
                    <!-- <div class="dropdown custom-drop drop3">
                        <button type="button" class="btn btn-secondary dropdown-toggle  {{ request()->is('users') ? 'active' : '' }}" id="dropdownMenuOffsetThread"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="10,20">
                            {{ __('lang.discussion-forum') }}
                            <svg viewBox="0 0 22.243 12.621">
                                <path d="M26.5,11.5l-9,9-9-9" transform="translate(-6.379 -9.379)" fill="none"
                                    stroke="#43b4e5" stroke-linecap="round" stroke-linejoin="round"
                                    stroke-miterlimit="10" stroke-width="3" />
                            </svg>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuOffsetThread">
                            <li><a class="dropdown-item" href="{{url('/superadmin/forum/threads')}}">{{ __('lang.view-forum') }}</a></li>
                            <li><a class="dropdown-item" href="{{ url('/superadmin/threads/categories') }}">{{ __('lang.manage-categories') }}</a></li>
                            <li><a class="dropdown-item" href="{{url('/superadmin/threads/reported')}}">{{ __('lang.reported-threads') }} </a></li>
                            <li><a class="dropdown-item" href="{{url('/superadmin/comment/reported')}}">{{ __('lang.reported-comment') }} </a></li>
                        </div>
                    </div> -->

                    <a class="dropdown-item" href="{{ url('/superadmin/learning-paths') }}" >{{ __('lang.learning-paths') }}</a>
                    <a class="dropdown-item" href="{{ url('/superadmin/job-roles') }}">{{ __('lang.job-roles') }}</a>
                    <a class="dropdown-item" href="{{ url('/superadmin/groups') }}">{{ __('lang.groups') }}</a>
                    <a class="dropdown-item" href="{{ url('/superadmin/leaderboard') }}">{{ __('lang.leaderboard') }}</a>
                   
                   
                </div>
                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto">
                    <!-- Authentication Links -->
                    @guest
                        <li><a class="nav-link"
                                href="{{ route('login') }}">{{ __('lang.login') }}</a>
                        </li>
                    @else
                        <div class="dropdown mr-1 custom-drop drop2">
                            <button type="button" class="btn btn-secondary dropdown-toggle" id="dropdownMenuOffset"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="10,20">
                                @if(Auth::user()->image)
                                    <img src="{{ asset('storage' . Config::get('constant.PROFILE_PICTURES') . Auth::user()->image) }}">
                                @else
                                    <div class="f-letter">
                                        <?php echo Auth::user()->name[0]; ?>
                                    </div>
                                @endif
                                <span>{{ Auth::user()->name }}</span>
                                <svg viewBox="0 0 22.243 12.621">
                                    <path d="M26.5,11.5l-9,9-9-9" transform="translate(-6.379 -9.379)" fill="none"
                                        stroke="#43b4e5" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-miterlimit="10" stroke-width="3" />
                                </svg>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuOffset" style="overflow:auto;">
                                <li>
                                    <a class="dropdown-item" href="{{ url('/superadmin/my-profile') }}"><i class="fa fa-user-circle-o" aria-hidden="true"></i> {{ __('lang.my-profile') }}</a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{ url('/superadmin/countries') }}"><i class="fa fa-globe" aria-hidden="true"></i> {{ __('lang.countries') }}</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ url('/superadmin/regions') }}"><i class="fa fa-map-pin" aria-hidden="true"></i> {{ __('lang.regions') }}</a>
                                </li>
                                <li>
                                    <a class="dropdown-item"  href="{{ url('/superadmin/news-promotions') }}"><i class="fa fa-newspaper-o" aria-hidden="true"></i> {{ __('lang.news-promotions') }}</a>
                                </li>
                                <li>
                                    <a class="dropdown-item"  href="{{ url('/superadmin/quizzes') }}"><i class="fa fa-question-circle" aria-hidden="true"></i> {{ __('lang.quizzes') }}</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ url('/superadmin/email-templates') }}"><i class="fa fa-envelope-o" aria-hidden="true"></i> {{ __('lang.manage-email-templates') }}</a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{ url('/superadmin/scheduled/mails') }}"><i class="fa fa-envelope-o" aria-hidden="true"></i> {{ __('lang.manual-email') }}</a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{ url('/superadmin/sales-tips') }}"><i class="fa fa-lightbulb-o" aria-hidden="true"></i>{{ __('lang.sales-tips') }}</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{url('/superadmin/certificates')}}"><i class="fa fa-file-text-o" aria-hidden="true"></i> {{ __('lang.certificates') }} </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{url('/superadmin/contact-categories')}}"><i class="fa fa-address-card-o" aria-hidden="true"></i> {{ __('lang.contact-category') }} </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{ url('/superadmin/faqs') }}"><i class="fa fa-question-circle-o" aria-hidden="true"></i>{{ __('lang.manage-FAQs') }} </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{ url('/superadmin/faq/categories') }}"><i class="fa fa-question-circle-o" aria-hidden="true"></i>{{ __('lang.FAQ-categories') }} </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{ url('/superadmin/settings') }}"><i class="fa fa-cog" aria-hidden="true"></i> {{ __('lang.point-settings') }}</a>
                                </li>

                                <li>
                                    <!-- <a class="dropdown-item" href="{{ url('/superadmin/import-users/create') }}"><i class="fa fa-cog" aria-hidden="true"></i> Import Users</a> -->
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="fa fa-sign-in" aria-hidden="true"></i>   {{ __('lang.logout') }}
                                    </a>
                                </li>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    @endguest
                </ul>
                
                <div class="mobilemenu">
                    <i class="fa fa-bars"></i>
                    <i class="fa fa-close"></i>
                </div>
            </div>
        </div>
    </nav>
</header>
