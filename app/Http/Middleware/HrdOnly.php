<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HrdOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (
            auth()->user()->role === 'ketua_divisi' &&
            auth()->user()->position_id == 2
        ) {
            return $next($request);
        }

        abort(403, 'Halaman ini hanya untuk HRD.');
    }
}
