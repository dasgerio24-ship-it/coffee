@extends('layouts.admin')

@section('title', 'Добавить категорию')

@section('content')
    <section class="card">
        <h1>Добавить категорию</h1>
        @include('admin.categories._form', ['action' => route('admin.categories.store')])
    </section>
@endsection
