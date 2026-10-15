<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name') }}</title>
</head>
<body>
    @include('partials.navigation')
    <h1>{{ config('app.name') }}</h1>
    <p>Сегодня: {{ now()->format('d.m.Y') }}</p>
</body>
</html>
