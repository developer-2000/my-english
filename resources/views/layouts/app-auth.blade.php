<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    @if(app()->environment('local'))
        <link rel="stylesheet" href="http://localhost:5173/resources/sass/app.scss">
    @else
        <link href="{{ asset('build/app.css') }}" rel="stylesheet">
    @endif
    @yield('css')
</head>
<body>
<div id="app">
    @yield('content')
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
@if(app()->environment('local'))
    <script type="module" src="http://localhost:5173/@vite/client"></script>
    <script type="module" src="http://localhost:5173/resources/js/app.js"></script>
@else
    <script src="{{ asset('build/app.js') }}"></script>
@endif

@yield('js')
</body>
</html>
