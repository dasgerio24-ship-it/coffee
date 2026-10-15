@extends('layouts.app')

@section('title', 'Контакты')

@section('content')
    <h1>Контакты</h1>
    <p>Адрес: {{ $address }}</p>
    <p>Телефон: {{ $phone }}</p>
    <p>E-mail: {{ $email }}</p>
@endsection
