@extends('layouts.app')

@section('title', '404 — Страница не найдена')

@section('content')
    <h1 class="error-code">404</h1>
    <p>Такая страница не найдена</p>
    <p>Такой страницы нет</p>
    <a href="{{ route('home') }}">На главную</a>
@endsection
