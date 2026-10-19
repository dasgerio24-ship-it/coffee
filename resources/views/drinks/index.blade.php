@extends('layouts.app')

@section('title', 'Каталог напитков')

@section('content')
    <h1>Каталог напитков</h1>
    {{-- Порядковый номер записи задаётся циклом Blade. --}}
    @forelse ($drinks as $drink)
        <x-card
            :title="$loop->iteration . '. ' . $drink->title"
            :url="route('drinks.show', $drink->id)"
            :badge="$drink->price >= 1500 ? 'Премиум' : null"
        >
            <p>Категория: {{ $drink->category }}</p>
            <p>{{ number_format($drink->price, 0, ',', ' ') }} ₸ · {{ $drink->volume }} мл</p>
            <p>{{ $drink->description }}</p>
        </x-card>
    @empty
        <x-alert type="info">Каталог пока пуст</x-alert>
    @endforelse
@endsection
