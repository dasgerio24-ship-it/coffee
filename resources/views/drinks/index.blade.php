<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Каталог напитков — {{ config('app.name') }}</title>
</head>
<body>
    @include('partials.navigation')
    <h1>Каталог напитков</h1>
    <ul>
        @foreach ($items as $id => $drink)
            <li><a href="{{ route('drinks.show', $id) }}">{{ $drink['title'] }}</a></li>
        @endforeach
    </ul>
</body>
</html>
