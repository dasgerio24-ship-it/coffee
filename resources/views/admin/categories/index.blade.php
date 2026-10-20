@extends('layouts.admin')

@section('title', 'Категории')

@section('content')
    <h1>Категории</h1>
    <p><a class="button" href="{{ route('admin.categories.create') }}">Добавить категорию</a></p>
    <div class="table-scroll">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Название</th>
                    <th>Напитков</th>
                    <th>Описание</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->drinks_count }}</td>
                        <td>{{ $category->description }}</td>
                        <td class="actions">
                            <a href="{{ route('admin.categories.edit', $category) }}">Изменить</a>
                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Удалить категорию?')">
                                @csrf
                                @method('DELETE')
                                <button class="danger" type="submit">Удалить</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Категорий пока нет.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    {{ $categories->links() }}
@endsection
