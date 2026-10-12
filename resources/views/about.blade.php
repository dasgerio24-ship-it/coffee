<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>О проекте — {{ config('app.name') }}</title>
</head>
<body>
    <nav>
        <a href="{{ route('home') }}">Главная</a> |
        <a href="{{ route('about') }}">О проекте</a> |
        <a href="{{ route('contacts') }}">Контакты</a>
    </nav>
    <h1>О проекте</h1>
    <p>ФИО: {{ $name }}</p>
    <p>Группа: {{ $group }}</p>
    <p>«Кофейня» — учебный проект сайта уютной кофейни. Здесь посетители могут узнать о заведении и посмотреть информацию о напитках.</p>
</body>
</html>
