<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLoginForm()
    {
        // Jika sudah login, redirect berdasarkan role
        if (Auth::check()) {
            $user = Auth::user();
            
            // Pastikan user masih aktif
            if (!$user->is_active) {
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'Akun Anda tidak aktif. Hubungi administrator.');
            }
            
            return $this->redirectBasedOnRole($user);
        }

        return view('auth.login');
    }

    /**
     * Proses login
     */
    public function login(Request $request)
    {
        // Validasi input
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'password.required' => 'Password harus diisi',
        ]);

        // Cek apakah user exists dan aktif
        $user = User::where('email', $credentials['email'])->first();

        if ($user && !$user->is_active) {
            return back()->withErrors([
                'email' => 'Akun Anda tidak aktif. Hubungi administrator.',
            ])->with('error', 'Akun tidak aktif!');
        }

        // Coba login
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            // Regenerate session untuk keamanan
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect berdasarkan role
            return $this->redirectBasedOnRole($user);
        }

        // Jika gagal, kembalikan dengan error
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email')->with('error', 'Login gagal! Periksa email dan password Anda.');
    }

    /**
     * Redirect berdasarkan role user
     */
    protected function redirectBasedOnRole($user)
    {
        // Pastikan user object lengkap dengan relasi yang dibutuhkan
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Session tidak valid. Silakan login kembali.');
        }

        $message = 'Selamat datang, ' . $user->name . '!';

        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard')
                    ->with('success', $message . ' (Administrator)');

            case 'ketua_divisi':
                // Jika jabatan supervisor (HRD)
                if ($user->position_id == 2) {
                    return redirect()->route('hrd.dashboard')
                        ->with('success', $message . ' (HRD)');
                }

                // Ketua divisi - Load relasi division jika belum
                if (!$user->relationLoaded('division')) {
                    $user->load('division');
                }

                $divisionName = $user->division ? $user->division->name : '';
                
                return redirect()->route('ketua-divisi.dashboard')
                    ->with('success', $message . ' (Ketua Divisi ' . $divisionName . ')');

            case 'staff':
                return redirect()->route('staff.hasil_penilaian.index')
                    ->with('success', $message . ' (Staff)');

            default:
                // Fallback jika role tidak dikenali
                Auth::logout();
                return redirect()->route('login')
                    ->with('error', 'Role tidak valid. Hubungi administrator.');
        }
    }

    /**
     * Proses logout
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidate session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda berhasil logout!');
    }

    /**
     * Tampilkan halaman register (optional)
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Proses register (optional)
     */
    public function register(Request $request)
    {
        // Validasi
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ], [
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.unique' => 'Email sudah terdafar',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        // Buat user baru
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'staff', // Default role
            'is_active' => true,
        ]);

        // Auto login setelah register
        Auth::login($user);

        // Redirect berdasarkan role
        return $this->redirectBasedOnRole($user);
    }
}