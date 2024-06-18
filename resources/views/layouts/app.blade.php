<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <!-- Styles -->
    <link rel="stylesheet" href="{{asset('adminlte/plugins/fontawesome-free/css/all.min.css')}}">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="{{asset('adminlte/css/adminlte.min.css')}}">
    <link rel="stylesheet" href="{{asset('adminlte/plugins/sweetalert2-theme-bootstrap-4/bootstrap-4.min.css')}}">
{{--    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">--}}

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>

<header>
    <a href="/" class="header-element header-main-link">English</a>
</header>

<div id="app">
    <div class="main-page">
        <!-- МЕНЮ LEFT -->
        <ul id="left_menu">
            <li>
                <router-link to="/page-list-words" class="left_menu"  exact>
                    Список слов
                </router-link>
            </li>
            <li>
                <router-link to="/page-word-sentences" class="left_menu"  exact>
                    Предложения слов
                </router-link>
            </li>
        </ul>

        @yield('content')

    </div>
</div>

<!-- >>> Section JS -->
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{asset('adminlte/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<!-- AdminLTE App -->
<script src="{{asset('adminlte/js/adminlte.js')}}"></script>

{{-- Compiller js--}}
<script src="{{ asset('js/app.js') }}" defer></script>

@yield('js')
</body>
</html>
