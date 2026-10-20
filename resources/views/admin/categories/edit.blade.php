@extends('layouts.admin')

@section('title', 'Изменить категорию')

@section('content')
    <section class="card">
        <h1>Изменить категорию</h1>
        @include('admin.categories._form', ['action' => route('admin.categories.update', $category)])
    </section>
@endsection
