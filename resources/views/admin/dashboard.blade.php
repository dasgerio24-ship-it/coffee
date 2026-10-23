@extends('layouts.admin')

@section('title', 'Панель')

@section('content')
    <x-card title="Панель администратора">
        <p><a href="{{ route('admin.drinks.index') }}">Управление напитками</a></p>
        <p><a href="{{ route('admin.drinks.create') }}">Добавить напиток</a></p>
        <p><a href="{{ route('admin.categories.index') }}">Управление категориями</a></p>
        <form method="POST" action="{{ route('admin.exports.catalog') }}">
            @csrf
            <button type="submit">Сформировать PDF в фоне</button>
        </form>
    </x-card>
@endsection
