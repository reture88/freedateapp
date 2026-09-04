<!DOCTYPE html>
<html lang="hu">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', config('app.name')) - {{ config('app.name') }}</title>
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        @livewireStyles
    </head>
    <body class="@yield('body_class', 'app-shell')">
        @yield('body')
        @livewireScripts
    </body>
</html>
