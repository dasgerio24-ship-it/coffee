<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Доступ запрещён — {{ config('app.name') }}</title>
</head>
<body>
    @include('partials.navigation')
    <h1>Доступ запрещён</h1>
    <p>{{ $exception->getMessage() }}</p>
</body>
</html>
