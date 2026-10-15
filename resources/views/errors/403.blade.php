@extends('layouts.app')

@section('title', 'Доступ запрещён')

@section('content')
    <h1>Доступ запрещён</h1>
    <p>{{ $exception->getMessage() }}</p>
@endsection
