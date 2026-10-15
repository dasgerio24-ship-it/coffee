@extends('layouts.admin')

@section('title', 'Панель')

@section('content')
    <section class="card">
        <h1>Панель администратора</h1>
        <p><a href="{{ route('admin.drinks') }}">Управление напитками</a></p>
        <p><a href="{{ route('admin.drinks.create') }}">Добавить напиток</a></p>
    </section>
@endsection
