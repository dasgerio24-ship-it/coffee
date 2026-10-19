@extends('layouts.app')

@section('title', 'Доступ запрещён')

@section('content')
    <h1>Доступ запрещён</h1>
    <x-alert type="warning">{{ $exception->getMessage() }}</x-alert>
@endsection
