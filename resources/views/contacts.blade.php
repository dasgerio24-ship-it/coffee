<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Контакты — {{ config('app.name') }}</title>
</head>
<body>
    <nav>
        <a href="{{ route('home') }}">Главная</a> |
        <a href="{{ route('about') }}">О проекте</a> |
        <a href="{{ route('contacts') }}">Контакты</a>
    </nav>
    <h1>Контакты</h1>
    <p>Адрес: {{ $address }}</p>
    <p>Телефон: {{ $phone }}</p>
    <p>E-mail: {{ $email }}</p>
</body>
</html>
