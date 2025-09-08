<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SqlRunnerKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // cek kunci di query param ?key=xxx atau bisa pakai header
        if ($request->get('key') !== config('app.sql_runner_key')) {
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}
