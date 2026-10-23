@extends('layouts.app')

@section('title', $drink->title)

@section('content')
    <img class="drink-detail-image" src="{{ $drink->image_url }}" alt="{{ $drink->title }}">
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
        @auth
            @if ($drink->attachment)
                <a class="button" href="{{ route('drinks.attachment', $drink) }}">Скачать документ</a>
            @endif
            <section class="booking-form">
                <h2>Заказать с собой</h2>
                @error('drink')
                    <div class="error" role="alert">{{ $message }}</div>
                @enderror
                <form method="POST" action="{{ route('drinks.bookings.store', $drink) }}">
                    @csrf
                    <label for="booking-phone">Телефон</label>
                    <input
                        id="booking-phone"
                        name="phone"
                        type="tel"
                        placeholder="+7XXXXXXXXXX"
                        value="{{ old('phone') }}"
                        @class(['invalid' => $errors->has('phone')])
                    >
                    @error('phone') <div class="error">{{ $message }}</div> @enderror

                    <label for="booking-comment">Комментарий</label>
                    <textarea
                        id="booking-comment"
                        name="comment"
                        maxlength="300"
                        @class(['invalid' => $errors->has('comment')])
                    >{{ old('comment') }}</textarea>
                    @error('comment') <div class="error">{{ $message }}</div> @enderror

                    <button type="submit">Заказать с собой</button>
                </form>
            </section>
        @endauth
        @guest
            <p>Войдите, чтобы оставить заказ: <a href="{{ route('login') }}">Войти</a></p>
        @endguest
        <p class="drink-qr">
            <img src="{{ $qrImage }}" alt="QR-код напитка">
            <span>Откройте страницу напитка по QR-коду</span>
        </p>
    </x-card>

    @if ($relatedDrinks->isNotEmpty())
        <h2>Ещё из этой категории</h2>
        <div class="card-grid">
            @foreach ($relatedDrinks as $relatedDrink)
                <x-card
                    :title="$relatedDrink->title"
                    :url="route('drinks.show', $relatedDrink)"
                    :badge="$relatedDrink->price >= 1500 ? 'Премиум' : null"
                    :image="$relatedDrink->image_url"
                >
                    <p>{{ number_format($relatedDrink->price, 0, ',', ' ') }} ₸ · {{ $relatedDrink->volume }} мл</p>
                </x-card>
            @endforeach
        </div>
    @endif
@endsection
