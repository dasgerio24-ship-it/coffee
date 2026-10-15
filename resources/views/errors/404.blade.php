<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>404 — {{ config('app.name') }}</title>
</head>
<body>
    @include('partials.navigation')
    <h1 style="font-size: 6rem;">404</h1>
    <p>Такая страница не найдена</p>
    <p>Такой страницы нет</p>
    <a href="{{ route('home') }}">На главную</a>
</body>
</html>
