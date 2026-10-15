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
                    @foreach (array_keys(reset($items)) as $field)
                        <th>{{ ucfirst(str_replace('_', ' ', $field)) }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $id => $item)
                    <tr>
                        <td>{{ $id }}</td>
                        <td>{{ $item['title'] }}
                            @if ($loop->first)
                                <span class="badge">новая</span>
                            @endif
                        </td>
                        @foreach (array_slice($item, 1, null, true) as $value)
                            <td>{{ $value }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <p><a href="{{ route('admin.dashboard') }}">Назад в панель</a></p>
@endsection
