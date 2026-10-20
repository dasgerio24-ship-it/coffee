@extends('layouts.app')

@section('title', $drink->title)

@section('content')
    <x-card
        :title="$drink->title"
        :badge="$drink->price >= 1500 ? 'Премиум' : null"
    >
        <dl>
            <dt>Категория</dt>
            <dd><a href="{{ route('categories.show', $drink->category) }}">{{ $drink->category->name }}</a></dd>
            <dt>Цена</dt>
            <dd>{{ number_format($drink->price, 0, ',', ' ') }} ₸</dd>
            <dt>Объём</dt>
            <dd>{{ $drink->volume }} мл</dd>
            @if ($drink->calories !== null)
                <dt>Калорийность</dt>
                <dd>{{ $drink->calories }} ккал</dd>
            @endif
            <dt>Описание</dt>
            <dd>{{ $drink->description }}</dd>
            <dt>Наличие</dt>
            <dd>{{ $drink->in_stock ? 'В наличии' : 'Нет в наличии' }}</dd>
        </dl>
        <a href="{{ route('drinks.index') }}">Назад к списку</a>
    </x-card>

    @if ($relatedDrinks->isNotEmpty())
        <h2>Ещё из этой категории</h2>
        <div class="card-grid">
            @foreach ($relatedDrinks as $relatedDrink)
                <x-card
                    :title="$relatedDrink->title"
                    :url="route('drinks.show', $relatedDrink)"
                    :badge="$relatedDrink->price >= 1500 ? 'Премиум' : null"
                >
                    <p>{{ number_format($relatedDrink->price, 0, ',', ' ') }} ₸ · {{ $relatedDrink->volume }} мл</p>
                </x-card>
            @endforeach
        </div>
    @endif
@endsection
