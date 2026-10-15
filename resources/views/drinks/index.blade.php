@extends('layouts.app')

@section('title', 'Каталог напитков')

@section('content')
    <h1>Каталог напитков</h1>
    {{-- Порядковый номер записи задаётся циклом Blade. --}}
    @forelse ($items as $id => $item)
        <article class="card">
            <h2>{{ $loop->iteration }}. <a href="{{ route('drinks.show', $id) }}">{{ $item['title'] }}</a>
                @if ((int) $item['price'] >= 1500)
                    <span class="badge">Премиум</span>
                @endif
            </h2>
            <p>{{ $item['volume'] }} · {{ $item['price'] }}</p>
            <p>{{ $item['description'] }}</p>
        </article>
    @empty
        <p>Каталог пока пуст</p>
    @endforelse
@endsection
