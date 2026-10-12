<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Напиток №{{ $id }} — {{ config('app.name') }}</title>
</head>
<body>
    <nav>
        <a href="{{ route('home') }}">Главная</a> |
        <a href="{{ route('about') }}">О проекте</a> |
        <a href="{{ route('contacts') }}">Контакты</a>
    </nav>
    <h1>Напиток №{{ $id }}</h1>
</body>
</html>
