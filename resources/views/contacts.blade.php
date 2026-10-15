<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Контакты — {{ config('app.name') }}</title>
</head>
<body>
    @include('partials.navigation')
    <h1>Контакты</h1>
    <p>Адрес: {{ $address }}</p>
    <p>Телефон: {{ $phone }}</p>
    <p>E-mail: {{ $email }}</p>
</body>
</html>
