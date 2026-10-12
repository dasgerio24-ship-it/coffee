<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Приветствие — {{ config('app.name') }}</title>
</head>
<body>
    <nav>
        <a href="{{ route('home') }}">Главная</a> |
        <a href="{{ route('about') }}">О проекте</a> |
        <a href="{{ route('contacts') }}">Контакты</a>
    </nav>
    <p>Здравствуйте, {{ $name }}! Добро пожаловать в {{ config('app.name') }}.</p>
</body>
</html>
