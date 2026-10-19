<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — Администрирование {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <header class="admin-header">
        <div class="container">
            <a class="brand" href="{{ route('admin.dashboard') }}">Администрирование</a>
            <nav class="menu admin-menu" aria-label="Меню администратора">
                <a href="{{ route('admin.dashboard') }}" @class(['active' => request()->routeIs('admin.dashboard')])>Панель</a>
                <a href="{{ route('admin.drinks') }}" @class(['active' => request()->routeIs('admin.drinks', 'admin.drinks.create')])>Напитки</a>
                <a href="{{ route('admin.logout') }}">Выход</a>
            </nav>
        </div>
    </header>
    <main class="container">
        @yield('content')
    </main>
    @include('partials.footer')
</body>
</html>
