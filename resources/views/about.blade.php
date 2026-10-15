<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>О проекте — {{ config('app.name') }}</title>
</head>
<body>
    @include('partials.navigation')
    <h1>О проекте</h1>
    <p>ФИО: {{ $name }}</p>
    <p>Группа: {{ $group }}</p>
    <p>«Кофейня» — учебный проект сайта уютной кофейни. Здесь посетители могут узнать о заведении и посмотреть информацию о напитках.</p>
</body>
</html>
