<!-- Stored in resources/views/layouts/app.blade.php -->

<html>
    <head>
        <meta charset="utf-8">
        <meta charset="-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title')</title>

        <link rel="stylesheet" href="{{asset('css/app.css')}}">
    </head>
    <body>
        <div id="app">
            <div class="container">
                @yield('content')
            </div>
        </div>
        <script src="{{asset('js/app.js')}}"></script>
    </body>
</html>
