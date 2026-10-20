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
                <span>{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
                <a href="{{ route('admin.dashboard') }}" @class(['active' => request()->routeIs('admin.dashboard')])>Панель</a>
                <a href="{{ route('admin.drinks.index') }}" @class(['active' => request()->routeIs('admin.drinks.*')])>Напитки</a>
                <a href="{{ route('admin.categories.index') }}" @class(['active' => request()->routeIs('admin.categories.*')])>Категории</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="link" type="submit">Выйти</button>
                </form>
            </nav>
        </div>
    </header>
    <main class="container">
        @if (session('success'))
            <x-alert type="success">{{ session('success') }}</x-alert>
        @endif
        @if (session('error'))
            <x-alert type="error">{{ session('error') }}</x-alert>
        @endif
        @yield('content')
    </main>
    @include('partials.footer')
</body>
</html>
