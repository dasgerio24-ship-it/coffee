<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Приветствие — {{ config('app.name') }}</title>
</head>
<body>
    @include('partials.navigation')
    <p>Здравствуйте, {{ $name }}! Добро пожаловать в {{ config('app.name') }}.</p>
</body>
</html>
