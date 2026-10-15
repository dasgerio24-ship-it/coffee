@extends('layouts.app')

@section('title', 'Кофейня закрыта')

@section('content')
    <h1>Сейчас закрыто</h1>
    <p>Сейчас закрыто, работаем с {{ $from }} до {{ $to }}</p>
@endsection
