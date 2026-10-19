<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <header class="site-header">
        <div class="container">
            <a class="brand" href="{{ route('home') }}">{{ config('app.name') }}</a>
            @include('partials.nav')
        </div>
    </header>
    <main class="container">
        @yield('content')
    </main>
    @include('partials.footer')
</body>
</html>
