<?php

namespace App\Policies;

use App\Models\Drink;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DrinkPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return null;
    }

    public function create(User $user): Response
    {
        if ($user->role === 'editor') {
            return Response::allow();
        }

        return Response::deny('Недостаточно прав для добавления напитка.');
    }

    public function update(User $user, Drink $drink): Response
    {
        if ($user->role === 'editor' && $drink->user_id === $user->id) {
            return Response::allow();
        }

        return Response::deny('Можно изменять только свои записи.');
    }

    public function delete(User $user, Drink $drink): Response
    {
        if ($user->role === 'editor' && $drink->user_id === $user->id) {
            return Response::allow();
        }

        return Response::deny('Можно удалять только свои записи.');
    }
}
