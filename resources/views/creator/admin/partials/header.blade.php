<header>
    <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
        <div class="container-fluid navflex">
            <a class="navbar-brand" href="{{ url('/admin/dashboard') }}">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Piaggio">
            </a>

            <div style="display:flex">
            <div class="topnav">
                <a href="{{ url('/admin/dashboard') }}"  class="{{ request()->is('dashboard') ? 'active' : '' }}">{{ __('lang.dashboard') }}</a>
                <a  class="dropdown-item"  href="{{ url('/admin/users') }}"> {{ __('lang.users') }}</a>
                <a  class="dropdown-item" href="{{ url('/admin/learning-paths') }}" >{{ __('lang.learning-paths') }}</a>

                <div class="dropdown custom-drop drop3">
                    <button type="button" class="btn btn-secondary dropdown-toggle  {{ request()->is('users') ? 'active' : '' }}" id="dropdownMenuOffset"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-offset="10,20">
                        {{ __('lang.leaderboard') }}
                        <svg viewBox="0 0 22.243 12.621">
                            <path d="M26.5,11.5l-9,9-9-9" transform="translate(-6.379 -9.379)" fill="none"
                                stroke="#43b4e5" stroke-linecap="round" stroke-linejoin="round"
                                stroke-miterlimit="10" stroke-width="3" />
                        </svg>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMenuOffset">
                        <li><a class="dropdown-item" href="{{ url('/admin/global/leaderboard') }}">{{ __('lang.global-leaderboard') }}</a></li>
                        <li><a class="dropdown-item" href="{{ url('/admin/regional/leaderboard') }}">{{ __('lang.organisation-leaderboard') }}</a></li>
                    </div>
                </div>

                <!-- <a class="dropdown-item" href="{{ url('/admin/leaderboard') }}">{{ __('lang.leaderboard') }}</a> -->
                <a class="dropdown-item" href="{{ url('/admin/sales-tips') }}">{{ __('lang.sales-tips') }}</a>
                <a class="dropdown-item"  href="{{ url('/admin/news-promotions') }}">{{ __('lang.news-promotions') }}</a>
                <a class="dropdown-item" href="{{url('/admin/forum/threads')}}"> {{ __('lang.discussion-forum') }}</a>
            </div>
            <div class="d-flex justify-content-end align-items-center">
                <!-- Left Side Of Navbar -->

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
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuOffset">
                                <li>
                                    <a class="dropdown-item" href="{{ url('/admin/my-profile') }}"><i class="fa fa-user-circle-o" aria-hidden="true"></i>{{ __('lang.my-profile') }}</a>
                                </li>
                                
                                <li>
                                    <a class="dropdown-item" href="{{url('/admin/faqs')}}"><i class="fa fa-question-circle-o" aria-hidden="true"></i>{{ __('lang.faqs') }} </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{url('/admin/quiz')}}"><i class="fa fa-question-circle-o" aria-hidden="true"></i>{{ __('lang.quizzes') }} </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{ url('/admin/email-templates') }}"><i class="fa fa-envelope-o" aria-hidden="true"></i> {{ __('lang.manage-email-templates') }}</a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{ url('/admin/scheduled/mails') }}"><i class="fa fa-envelope-o" aria-hidden="true"></i> {{ __('lang.manual-email') }}</a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{url('/admin/support/mail/create')}}"> <i class="fa fa-envelope-o" aria-hidden="true"></i>{{ __('lang.help-support') }}</a>
                                </li>
                                    
                                <li>
                                    <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                        document.getElementById('logout-form').submit();">
                                         <i class="fa fa-sign-in" aria-hidden="true"></i>{{ __('lang.logout') }}
                                    </a>
                                </li>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
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
            </div>
        </div>
    </nav>
</header>
