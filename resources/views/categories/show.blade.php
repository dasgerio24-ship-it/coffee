@extends('layouts.app')

@section('title', $category->name)

@section('content')
    <h1>{{ $category->name }}</h1>
    @if ($category->description)
        <p>{{ $category->description }}</p>
    @endif

    <h2>Напитки категории</h2>
    <div class="card-grid">
        @forelse ($drinks as $drink)
            <x-card
                :title="$drink->title"
                :url="route('drinks.show', $drink)"
                :badge="$drink->price >= 1500 ? 'Премиум' : null"
                :image="$drink->image_url"
            >
                <p>{{ number_format($drink->price, 0, ',', ' ') }} ₸ · {{ $drink->volume }} мл</p>
                <p>{{ $drink->description }}</p>
            </x-card>
        @empty
            <x-alert type="warning">В этой категории пока нет напитков.</x-alert>
        @endforelse
    </div>

    <a href="{{ route('drinks.index') }}">Все напитки</a>
@endsection
