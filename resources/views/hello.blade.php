@extends('layouts.app')

@section('title', 'Приветствие')

@section('content')
    <h1>Здравствуйте, {{ $name }}!</h1>
    <p>Добро пожаловать в {{ config('app.name') }}.</p>
@endsection
