<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Кофейня закрыта — {{ config('app.name') }}</title>
</head>
<body>
    @include('partials.navigation')
    <h1>Сейчас закрыто</h1>
    <p>Сейчас закрыто, работаем с {{ $from }} до {{ $to }}</p>
</body>
</html>
