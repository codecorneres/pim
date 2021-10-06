<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style type="text/css">
    .card-header strong {
        font-size: 18px;
        line-height: 35px;
    }
    a.notify-me {
        float: right;
/*        padding: 5px 10px;*/
        color: #FFF;
        font-size: 16px;
    }
    a.notify-me:hover {
        text-decoration: none;
    }
    .pagination nav{
        margin: 15px auto 0px;
    }
    .check-image label, .check-image input {
        cursor: pointer;
        margin-bottom: 0px;
    }
    .user-panel .navbar-dark .navbar-brand {
        color: #fff;
        font-size: 20px;
        letter-spacing: 0.5px;
    }
    .user-panel nav.navbar.navbar-expand-md.navbar-dark.bg-dark.shadow-sm.user-navs {
        padding-top: 15px;
        padding-bottom: 15px;
    }
    .user-panel .navbar-dark .navbar-nav .nav-link {
        font-size: 16px;
        color: #FFF;
    }
    .user-panel ul.actions {
        padding-left: 0px;
    }
    .user-panel ul.actions li {
        list-style-type: none;
        display: inline-block;
    }
    .form-control{
        padding:   3px!important;
    }
    .bbtn {
        margin-top: 31px;
    }
    main {
        margin-top: 50px;
    }
    </style>
</head>
<body>
    <div id="app" class="user-panel">
        <nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm user-navs">
            <div class="container">
                @guest
                <a class="navbar-brand" href="{{ route('home') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                @else
                    @if(Auth::user()->isAdmin())
                    <a class="navbar-brand" href="{{ route('admin.home') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                    @else
                    <a class="navbar-brand" href="{{ route('home') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                    @endif
                @endif    
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            @if(Auth::user()->isAdmin())
                                
                            @else
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('sync') }}">Sync Store Products</a>
                                </li>
                                                    
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        {{ Auth::user()->name }}
                                    </a>

                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault();
                                                         document.getElementById('logout-form').submit();">
                                            {{ __('Logout') }}
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </div>
                                </li>
                            @endif
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    @yield('scripts')
</body>
</html>
