@extends('layouts.app')

@section('title', $drink['title'])

@section('content')
    <article class="card">
        <h1>{{ $drink['title'] }}</h1>
        @if ((int) $drink['price'] >= 1500)
            <span class="badge">Премиум</span>
        @endif
        <dl>
            @foreach ($drink as $field => $value)
                <dt>{{ ucfirst(str_replace('_', ' ', $field)) }}</dt>
                <dd>{{ $value }}</dd>
            @endforeach
        </dl>
        <a href="{{ route('drinks.index') }}">Назад к списку</a>
    </article>
@endsection
