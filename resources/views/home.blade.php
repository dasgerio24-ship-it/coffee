@extends('layouts.app')

@section('title', 'Главная')

@section('content')
    <section class="card">
        <h1>{{ config('app.name') }}</h1>
        <p>Добро пожаловать в нашу кофейню.</p>
        <p>Сегодня: {{ now()->format('d.m.Y') }}</p>
    </section>
@endsection
