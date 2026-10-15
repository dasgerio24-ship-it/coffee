<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Напитки — администрирование</title>
</head>
<body>
    @include('partials.navigation')
    <h1>Напитки</h1>
    <p><a href="{{ route('admin.drinks.create') }}">Добавить напиток</a></p>
    <table border="1" cellpadding="6">
        <thead>
            <tr>
                <th>ID</th>
                @foreach (array_keys(reset($items)) as $field)
                    <th>{{ ucfirst(str_replace('_', ' ', $field)) }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $id => $drink)
                <tr>
                    <td>{{ $id }}</td>
                    @foreach ($drink as $value)
                        <td>{{ $value }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
    <p><a href="{{ route('admin.dashboard') }}">Назад в панель</a></p>
</body>
</html>
