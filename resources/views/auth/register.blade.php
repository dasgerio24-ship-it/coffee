@extends('layouts.app')

@section('title', 'Регистрация')

@section('content')
    <section class="card">
        <h1>Регистрация</h1>
        @if ($errors->any())
            <x-alert type="error">Проверьте введённые данные.</x-alert>
        @endif
        <form method="POST" action="{{ route('register') }}" class="admin-form">
            @csrf

            <label for="name">Имя</label>
            <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" @class(['invalid' => $errors->has('name')])>
            @error('name') <div class="error">{{ $message }}</div> @enderror

            <label for="email">E-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" @class(['invalid' => $errors->has('email')])>
            @error('email') <div class="error">{{ $message }}</div> @enderror

            <label for="password">Пароль</label>
            <input id="password" name="password" type="password" autocomplete="new-password" @class(['invalid' => $errors->has('password')])>
            @error('password') <div class="error">{{ $message }}</div> @enderror

            <label for="password_confirmation">Подтвердите пароль</label>
            <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password">

            <button type="submit">Зарегистрироваться</button>
        </form>
    </section>
@endsection
