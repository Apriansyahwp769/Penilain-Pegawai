<?php

namespace App\Http\Controllers\KetuaDivisi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display profile page
     */
    public function index()
    {
        try {
            $user = Auth::user()->load(['division', 'position']);
            
            return view('ketua-divisi.profile.index', compact('user'));
            
        } catch (\Exception $e) {
            Log::error("Profile Index Error: " . $e->getMessage());
            return back()->with('error', 'Gagal memuat halaman profile.');
        }
    }

    /**
     * Update profile
     */
    public function update(Request $request)
    {
        try {
            $user = Auth::user();

            // Validation rules
            $rules = [
                'name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($user->id)
                ],
                'phone' => 'nullable|string|max:20',
                'current_password' => 'nullable|required_with:password',
                'password' => 'nullable|min:8|confirmed',
            ];

            $messages = [
                'name.required' => 'Nama wajib diisi',
                'email.required' => 'Email wajib diisi',
                'email.email' => 'Format email tidak valid',
                'email.unique' => 'Email sudah digunakan oleh user lain',
                'current_password.required_with' => 'Password lama wajib diisi jika ingin mengganti password',
                'password.min' => 'Password minimal 8 karakter',
                'password.confirmed' => 'Konfirmasi password tidak cocok',
            ];

            $validated = $request->validate($rules, $messages);

            // Jika user ingin ganti password
            if ($request->filled('password')) {
                // Cek password lama
                if (!Hash::check($request->current_password, $user->password)) {
                    return back()
                        ->withErrors(['current_password' => 'Password lama tidak sesuai'])
                        ->withInput();
                }

                // Update dengan password baru
                $user->update([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'password' => Hash::make($validated['password'])
                ]);

                return back()->with('success', 'Profile dan password berhasil diperbarui!');
            }

            // Update tanpa password
            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? $user->phone,
            ]);

            return back()->with('success', 'Profile berhasil diperbarui!');

        } catch (\Exception $e) {
            Log::error("Profile Update Error: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            return back()->with('error', 'Gagal memperbarui profile.')->withInput();
        }
    }
}