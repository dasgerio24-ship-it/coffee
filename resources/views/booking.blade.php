@extends('layouts.app')

@section('title', 'Заказать с собой')

@section('content')
    <section class="card">
        <h1>Заказать с собой</h1>
        <p>Выберите напиток в каталоге и нажмите «Заказать с собой».</p>
        <a class="button" href="{{ route('drinks.index') }}">Открыть каталог</a>
    </section>
@endsection
