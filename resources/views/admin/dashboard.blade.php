@extends('layouts.admin')

@section('title', 'Панель')

@section('content')
    <x-card title="Панель администратора">
        <p><a href="{{ route('admin.drinks') }}">Управление напитками</a></p>
        <p><a href="{{ route('admin.drinks.create') }}">Добавить напиток</a></p>
    </x-card>
@endsection
