<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавить напиток — {{ config('app.name') }}</title>
</head>
<body>
    @include('partials.navigation')
    <h1>Добавить напиток</h1>
    <p>Форма добавления записи будет реализована в следующей лабораторной работе.</p>
    <button type="button">Добавить</button>
    <p><a href="{{ route('admin.drinks') }}">Назад к напиткам</a></p>
</body>
</html>
