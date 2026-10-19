@extends('layouts.app')

@section('title', 'Главная')

@section('content')
    <h1>{{ config('app.name') }}</h1>
    <p>Добро пожаловать в нашу кофейню. Сегодня: {{ now()->format('d.m.Y') }}</p>
    <h2>Популярные напитки</h2>
    <div class="card-grid">
        @foreach ($drinks as $drink)
            <x-card
                :title="$drink->title"
                :url="route('drinks.show', $drink->id)"
                :badge="$drink->price >= 1500 ? 'Премиум' : null"
                class="card-wide"
            >
                <p>Категория: {{ $drink->category }}</p>
                <p>{{ number_format($drink->price, 0, ',', ' ') }} ₸ · {{ $drink->volume }} мл</p>
                <p>{{ $drink->description }}</p>
            </x-card>
        @endforeach
    </div>
@endsection
