<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: "DejaVu Sans", sans-serif; font-size: 12px; }
        h1 { font-size: 18px; margin-bottom: 18px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #777; padding: 6px; text-align: left; }
        th { background: #eee4d8; }
        img { height: 40px; width: auto; }
        .total { margin-top: 16px; }
    </style>
</head>
<body>
    <h1>{{ config('app.name') }}: каталог напитков на {{ now()->format('d.m.Y') }}</h1>
    <table>
        <thead>
            <tr>
                <th>№</th>
                <th>Фото</th>
                <th>Название</th>
                <th>Категория</th>
                <th>Цена</th>
                <th>Объём</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($drinks as $drink)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        @if ($drink->image && is_file(public_path('storage/'.$drink->image)))
                            <img src="{{ public_path('storage/'.$drink->image) }}" alt="{{ $drink->title }}">
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $drink->title }}</td>
                    <td>{{ $drink->category->name }}</td>
                    <td>{{ number_format($drink->price, 0, ',', ' ') }} ₸</td>
                    <td>{{ $drink->volume }} мл</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p class="total">Всего записей: {{ $drinks->count() }}</p>
</body>
</html>
