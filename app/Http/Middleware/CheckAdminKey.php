<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $adminKey = config('app.admin_key');

        if (! is_string($adminKey) || $adminKey === '') {
            abort(403, 'Нужен ключ администратора');
        }

        $providedKey = $request->query('key');

        if (is_string($providedKey) && hash_equals($adminKey, $providedKey)) {
            $request->session()->put('is_admin', true);
        }

        if (! $request->session()->get('is_admin')) {
            abort(403, 'Нужен ключ администратора');
        }

        return $next($request);
    }
}
