<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class LogoutController extends Controller
{
    public function __invoke()
    {
        session()->forget('is_admin');

        return redirect()->route('home');
    }
}
