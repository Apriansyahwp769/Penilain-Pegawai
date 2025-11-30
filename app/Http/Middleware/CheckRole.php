<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Cek apakah user aktif
        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')
                ->with('error', 'Akun Anda tidak aktif.');
        }

        // Cek apakah role user sesuai
        if (!in_array($user->role, $roles)) {
            // Redirect ke dashboard sesuai role mereka
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('admin.dashboard')
                        ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
                case 'ketua_divisi':
                    return redirect()->route('ketua-divisi.dashboard')
                        ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
                case 'staff':
                    return redirect()->route('staff.dashboard')
                        ->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
            }
        }

        return $next($request);
    }
}