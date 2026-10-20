<nav class="menu" aria-label="Главное меню">
    <a href="{{ route('home') }}" @class(['active' => request()->routeIs('home')])>Главная</a>
    <a href="{{ route('drinks.index') }}" @class(['active' => request()->routeIs('drinks.*')])>Каталог</a>
    <a href="{{ route('rules') }}" @class(['active' => request()->routeIs('rules')])>Правила</a>
    <a href="{{ route('booking') }}" @class(['active' => request()->routeIs('booking')])>Заказать с собой</a>
    <a href="{{ route('about') }}" @class(['active' => request()->routeIs('about')])>О проекте</a>
    <a href="{{ route('contacts') }}" @class(['active' => request()->routeIs('contacts')])>Контакты</a>
    @guest
        <a href="{{ route('login') }}">Вход</a>
        <a href="{{ route('register') }}">Регистрация</a>
    @endguest
    @auth
        <span>{{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="link" type="submit">Выйти</button>
        </form>
    @endauth
    @can('admin-panel')
        <a href="{{ route('admin.dashboard') }}">Админ-панель</a>
    @endcan
</nav>
