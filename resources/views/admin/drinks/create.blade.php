@extends('layouts.admin')

@section('title', 'Добавить напиток')

@section('content')
    <section class="card">
        <h1>Добавить напиток</h1>
        <p>Форма добавления записи будет реализована в следующей лабораторной работе.</p>
        <button type="button">Добавить</button>
        <p><a href="{{ route('admin.drinks') }}">Назад к напиткам</a></p>
    </section>
@endsection
