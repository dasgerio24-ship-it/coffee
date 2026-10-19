@extends('layouts.app')

@section('title', $drink->title)

@section('content')
    <x-card
        :title="$drink->title"
        :badge="$drink->price >= 1500 ? 'Премиум' : null"
    >
        <dl>
            <dt>Категория</dt>
            <dd>{{ $drink->category }}</dd>
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
@endsection
