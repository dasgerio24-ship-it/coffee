@extends('layouts.admin')

@section('title', 'Добавить напиток')

@section('content')
    <section class="card">
        <h1>Добавить напиток</h1>
        @include('admin.drinks._form', ['action' => route('admin.drinks.store')])
    </section>
@endsection
