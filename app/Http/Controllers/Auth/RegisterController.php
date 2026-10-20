<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'required' => 'Поле «:attribute» обязательно для заполнения.',
            'email' => 'Введите корректный адрес электронной почты.',
            'unique' => 'Этот адрес электронной почты уже зарегистрирован.',
            'min' => 'Поле «:attribute» должно содержать не менее :min символов.',
            'confirmed' => 'Пароли не совпадают.',
            'max' => 'Поле «:attribute» не должно превышать :max символов.',
        ], [
            'name' => 'Имя',
            'email' => 'E-mail',
            'password' => 'Пароль',
        ]);

        $user = User::create($data);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('home')
            ->with('success', 'Добро пожаловать, '.$user->name);
    }
}
