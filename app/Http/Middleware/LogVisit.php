<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogVisit
{
    public function handle(Request $request, Closure $next): Response
    {
        $url = $request->query->has('key')
            ? (preg_replace('/([?&]key=)[^&]*/', '$1***', $request->fullUrl()) ?? $request->fullUrl())
            : $request->fullUrl();

        Log::info('Визит', [
            'url' => $url,
            'ip' => $request->ip(),
        ]);

        return $next($request);
    }
}
