@extends('layouts.app')

@section('title', 'Каталог напитков')

@section('content')
    <h1>Каталог напитков</h1>
    <div class="catalog-layout">
        <aside class="category-menu" aria-label="Категории напитков">
            <h2>Категории</h2>
            <ul>
                <li><a href="{{ route('drinks.index') }}">Все напитки</a></li>
                @foreach ($categories as $category)
                    <li>
                        <a href="{{ route('categories.show', $category) }}">
                            {{ $category->name }} ({{ $category->drinks_count }})
                        </a>
                    </li>
                @endforeach
            </ul>
        </aside>

        <div class="catalog-results">
            <form method="GET" action="{{ route('drinks.index') }}" class="drink-filters">
                <label>
                    Поиск
                    <input type="search" name="q" value="{{ $term }}" placeholder="Название или описание">
                </label>
                <label>
                    Категория
                    <select name="category">
                        <option value="">Все категории</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(request('category') == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </label>
                <label>
                    Сортировка
                    <select name="sort">
                        <option value="title" @selected($sort === 'title')>По названию</option>
                        <option value="price" @selected($sort === 'price')>По цене</option>
                        <option value="volume" @selected($sort === 'volume')>По объёму</option>
                        <option value="newest" @selected($sort === 'newest')>Сначала новые</option>
                    </select>
                </label>
                <button type="submit">Найти</button>
                <a href="{{ route('drinks.index') }}">Сбросить</a>
            </form>

            @forelse ($drinks as $drink)
                <x-card
                    :title="$drinks->firstItem() + $loop->index . '. ' . $drink->title"
                    :url="route('drinks.show', $drink)"
                    :badge="$drink->price >= 1500 ? 'Премиум' : null"
                >
                    <p>Категория: <a href="{{ route('categories.show', $drink->category) }}">{{ $drink->category->name }}</a></p>
                    <p>{{ number_format($drink->price, 0, ',', ' ') }} ₸ · {{ $drink->volume }} мл</p>
                    <p>{{ $drink->description }}</p>
                </x-card>
            @empty
                <x-alert type="warning">По запросу «{{ $term }}» ничего не найдено</x-alert>
            @endforelse

            {{ $drinks->links() }}
        </div>
    </div>
@endsection
