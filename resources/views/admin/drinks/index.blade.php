@extends('layouts.admin')

@section('title', 'Напитки')

@section('content')
    <h1>Напитки</h1>
    <p><a href="{{ route('admin.drinks.create') }}">Добавить напиток</a></p>
    <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Категория</th>
                    <th>Цена</th>
                    <th>Объём</th>
                    <th>Калории</th>
                    <th>Описание</th>
                    <th>В наличии</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($drinks as $drink)
                    <tr>
                        <td>{{ $drink->id }}</td>
                        <td>
                            {{ $drink->title }}
                            @if ($loop->first)
                                <x-badge>новая</x-badge>
                            @endif
                        </td>
                        <td>{{ $drink->category }}</td>
                        <td>{{ number_format($drink->price, 0, ',', ' ') }} ₸</td>
                        <td>{{ $drink->volume }} мл</td>
                        <td>{{ $drink->calories ?? '—' }}</td>
                        <td>{{ $drink->description }}</td>
                        <td>{{ $drink->in_stock ? 'Да' : 'Нет' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <p><a href="{{ route('admin.dashboard') }}">Назад в панель</a></p>
@endsection
