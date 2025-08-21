<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#ffffff">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    
    <!-- CSS loaded normally -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <style>
        #languagesList{
            display: flex;
            justify-content: space-between;
        }
        #languagesList .language-option{
            background: #f1f1f1;
            width: 50%;
            padding: 10px 10px;
            text-align: center;
            cursor: pointer;
            outline: 1px solid #dbdbdb;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        #languagesList .language-option:hover {
            background-color: #2000ff;
            color: white;
        }
    </style>
</head>
<body>
<div id="app">
    <hallway :user="{{ json_encode(Auth::user()) }}"></hallway>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script src="{{ mix('js/app.js') }}"></script>
@yield('js')
</body>
</html>
