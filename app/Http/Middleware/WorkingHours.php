<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WorkingHours
{
    public function handle(Request $request, Closure $next, string $from, string $to): Response
    {
        $hour = now()->hour;

        if ($hour < (int) $from || $hour >= (int) $to) {
            return response()->view('closed', ['from' => $from, 'to' => $to], 403);
        }

        return $next($request);
    }
}
