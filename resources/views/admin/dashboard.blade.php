<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Панель администратора — {{ config('app.name') }}</title>
</head>
<body>
    @include('partials.navigation')
    <h1>Панель администратора</h1>
    <ul>
        <li><a href="{{ route('admin.drinks') }}">Управление напитками</a></li>
        <li><a href="{{ route('admin.drinks.create') }}">Добавить напиток</a></li>
        <li><a href="{{ route('admin.logout') }}">Выйти из админ-раздела</a></li>
    </ul>
</body>
</html>
