@extends('layouts.admin')

@section('title', 'Напитки')

@section('content')
    <h1>Напитки</h1>
    <p><a class="button" href="{{ route('admin.drinks.create') }}">Добавить напиток</a></p>
    <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Категория</th>
                    <th>Цена</th>
                    <th>Объём</th>
                    <th>В наличии</th>
                    <th>Создан</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($drinks as $drink)
                    <tr>
                        <td>{{ $drink->id }}</td>
                        <td>
                            {{ $drink->title }}
                            @if ($loop->first && $drinks->onFirstPage())
                                <x-badge>новая</x-badge>
                            @endif
                        </td>
                        <td>{{ $drink->category->name }}</td>
                        <td>{{ number_format($drink->price, 0, ',', ' ') }} ₸</td>
                        <td>{{ $drink->volume }} мл</td>
                        <td>{{ $drink->in_stock ? 'Да' : 'Нет' }}</td>
                        <td>{{ $drink->created_at->format('d.m.Y') }}</td>
                        <td class="actions">
                            <a href="{{ route('admin.drinks.edit', $drink) }}">Изменить</a>
                            <form method="POST" action="{{ route('admin.drinks.destroy', $drink) }}" onsubmit="return confirm('Удалить запись?')">
                                @csrf
                                @method('DELETE')
                                <button class="danger" type="submit">Удалить</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    {{ $drinks->links() }}
    <p><a href="{{ route('admin.dashboard') }}">Назад в панель</a></p>
@endsection
