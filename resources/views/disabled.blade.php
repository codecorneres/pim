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
    .warning-page{
        margin-top: 100px;
    }
    .warning-page p {
        font-size: 24px;
        text-align: center;
        margin-top: 20px;
        margin-bottom: 20px;
    }
    .navbar-dark .navbar-brand {
        color: #fff;
        font-size: 20px;
        letter-spacing: 0.5px;
    }
    nav.navbar.navbar-expand-md.navbar-dark.bg-dark.shadow-sm {
        padding-top: 15px;
        padding-bottom: 15px;
    }
    .navbar-dark .navbar-nav .nav-link {
        font-size: 16px;
        color: #FFF;
    }
    ul.actions {
        padding-left: 0px;
    }
    ul.actions li {
        list-style-type: none;
        display: inline-block;
    }
    </style>
</head>
<body>
    <div id="app" class="user-panel">
        <nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm users-nav">
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
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            <div class="container image-manager warning-page">
                <div class="row justify-content-center">
                    <div class="col-md-12">
                        <div class="alert alert-danger" role="alert">
                        <p>You don't have permissions to view store data</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    @yield('scripts')
</body>
</html>


