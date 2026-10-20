@extends('layouts.admin')

@section('title', 'Изменить напиток')

@section('content')
    <section class="card">
        <h1>Изменить напиток</h1>
        @include('admin.drinks._form', ['action' => route('admin.drinks.update', $drink)])
    </section>
@endsection
