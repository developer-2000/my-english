<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Error Page</title>
    @if(app()->environment('local'))
        <link rel="stylesheet" href="http://localhost:5173/resources/sass/app.scss">
    @else
        <link href="{{ asset('build/app.css') }}" rel="stylesheet">
    @endif
    <style>
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .error-box {
            width: 35%;
            text-align: center;
        }
        .box-button {
            display: flex;
            justify-content: center;
        }
        .btn-back {
            width: 200px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="error-box">
        <div class="alert alert-secondary">
            <h3>Error: {{ $code }}</h3>
            <p>{{ $message }}</p>
        </div>
        <div class="box-button">
            <a href="javascript:history.back()" class="btn btn-secondary btn-back mt-3">Back to page</a>
        </div>
    </div>
</div>
</body>
</html>
