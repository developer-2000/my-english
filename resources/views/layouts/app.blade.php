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


    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

</head>
<body>
<div id="app">
    <div class="tabbable tabs-left">
        <!-- МЕНЮ LEFT -->
        <ul class="nav nav-tabs" id="left_menu">
            <!-- link index -->
            <div class="link_index">
                <a href="/" class="left_menu">English</a>
            </div>
            <li>
                <router-link to="/page_list_words" class="left_menu">
                    Список слов
                </router-link>
            </li>
            <li>
                <router-link to="/word_sentences" class="left_menu">
                    Предложения слов
                </router-link>
            </li>
        </ul>

        @yield('content')

    </div>
</div>

<!-- >>> Section JS -->
<!-- jQuery -->
{{--<script src="{{asset('adminlte/plugins/jquery/jquery.min.js')}}"></script>--}}
<script type="text/javascript" src="http://code.jquery.com/jquery-1.10.0.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="{{asset('adminlte/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
<!-- Bootstrap 4 -->
<script src="{{asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<!-- AdminLTE App -->
<script src="{{asset('adminlte/js/adminlte.js')}}"></script>

{{-- Compiller js--}}
<script src="{{ asset('js/app.js') }}" defer></script>

<script  type="text/javascript">

    {{-- клик по ссылке --}}
    setTimeout(function knowClick() {
        var elements = document.querySelectorAll("ul a");

        for (var i = 0; i < elements.length; i++) {
            elements[i].onclick = function(e){
                let arr = e.target.href.split('/');
                // close html main page
                if(arr.length >= 4 && arr[3] !== ''){
                    $('#default_pane').css('display','none');
                }
            };
        }
    }, 200);
    // for reload page
    (function() {
        let url = window.location.href;
        let arr = url.split('/');
        // close html main page
        if(arr.length >= 4 && arr[3] != ''){
            $('#default_pane').css('display','none');
        }
    })();

</script>

@yield('js')
</body>
</html>
