@extends('layouts.app')

@section('title', 'Вход')

@section('content')
    <section class="card">
        <h1>Вход</h1>
        @if ($errors->any())
            <x-alert type="error">Проверьте введённые данные.</x-alert>
        @endif
        <form method="POST" action="{{ route('login') }}" class="admin-form">
            @csrf

            <label for="email">E-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" @class(['invalid' => $errors->has('email')])>
            @error('email') <div class="error">{{ $message }}</div> @enderror

            <label for="password">Пароль</label>
            <input id="password" name="password" type="password" autocomplete="current-password" @class(['invalid' => $errors->has('password')])>
            @error('password') <div class="error">{{ $message }}</div> @enderror

            <label class="checkbox-label" for="remember">
                <input id="remember" name="remember" type="checkbox" value="1" @checked(old('remember'))>
                Запомнить меня
            </label>

            <button type="submit">Войти</button>
            <a href="{{ route('register') }}">Зарегистрироваться</a>
        </form>
    </section>
@endsection
