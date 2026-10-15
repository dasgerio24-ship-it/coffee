<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>{{ $drink['title'] }} — {{ config('app.name') }}</title>
</head>
<body>
    @include('partials.navigation')
    <h1>{{ $drink['title'] }}</h1>
    <dl>
        @foreach ($drink as $field => $value)
            <dt>{{ ucfirst(str_replace('_', ' ', $field)) }}</dt>
            <dd>{{ $value }}</dd>
        @endforeach
    </dl>
    <a href="{{ route('drinks.index') }}">Назад к списку</a>
</body>
</html>
