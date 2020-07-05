<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{asset('/images/fav-logo.png')}}"/>

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    @if(Request::is('login') || Request::is('register'))
        <link rel="stylesheet" type="text/css" href="{{ asset('loginStyle/css/util.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('loginStyle/css/main.css') }}">
        <style type="text/css">
        .pb-4, .py-4 {
            padding: 0px !important;
        }
        </style>
    @endif

</head>
<body>
    <div id="app">
        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
