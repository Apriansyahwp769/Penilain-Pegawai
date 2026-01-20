<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Division;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with(['division', 'position']);

        // Apply filters
        if ($request->filled('division_id')) {
            $query->where('division_id', $request->division_id);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $isActive = $request->status === 'active' ? 1 : 0;
            $query->where('is_active', $isActive);
        }

        // Apply search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        // Pagination with per_page option
        $perPage = $request->get('per_page', 10);
        $users = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $divisions = Division::where('status', true)->get();
        $positions = Position::where('status', true)->get();

        return view('admin.users.index', compact('users', 'divisions', 'positions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8',
                'nip' => 'nullable|string|unique:users,nip',
                'division_id' => 'nullable|exists:divisions,id',
                'position_id' => 'nullable|exists:positions,id',
                'phone' => 'nullable|string|max:20',
                'join_date' => 'nullable|date',
                'role' => 'required|in:admin,ketua_divisi,staff',
                'is_active' => 'required|boolean'
            ], [
                'name.required' => 'Nama lengkap wajib diisi',
                'email.required' => 'Email wajib diisi',
                'email.email' => 'Format email tidak valid',
                'email.unique' => 'Email sudah terdaftar',
                'password.required' => 'Password wajib diisi',
                'password.min' => 'Password minimal 8 karakter',
                'nip.unique' => 'NIP sudah terdaftar, gunakan NIP yang berbeda',
                'role.required' => 'Role wajib dipilih',
                'is_active.required' => 'Status wajib dipilih'
            ]);

            $validated['password'] = Hash::make($validated['password']);
            User::create($validated);

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil dibuat!');
                
        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal membuat user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update the specified resource in storage.
     */
 public function update(Request $request, User $user)
{
    try {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8',
            'nip' => 'nullable|string|unique:users,nip,' . $user->id,
            'division_id' => 'nullable|exists:divisions,id',
            'position_id' => 'nullable|exists:positions,id',
            'phone' => 'nullable|string|max:20',
            'join_date' => 'nullable|date',
            'role' => 'required|in:admin,ketua_divisi,staff',
            'is_active' => 'required|boolean'
        ], [
            'name.required' => 'Nama lengkap wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.min' => 'Password minimal 8 karakter',
            'nip.unique' => 'NIP sudah terdaftar, gunakan NIP yang berbeda',
            'role.required' => 'Role wajib dipilih',
            'is_active.required' => 'Status wajib dipilih'
        ]);

        // Only update password if provided
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        // Check if request is AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'User berhasil diperbarui!'
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil diperbarui!');
            
    } catch (ValidationException $e) {
        // Return validation errors as JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'errors' => $e->validator->errors()
            ], 422);
        }
        
        return redirect()->back()
            ->withErrors($e->validator)
            ->withInput();
    } catch (\Exception $e) {
        // Return error as JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui user: ' . $e->getMessage()
            ], 500);
        }
        
        return redirect()->back()
            ->with('error', 'Gagal memperbarui user: ' . $e->getMessage())
            ->withInput();
    }
}
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            // Prevent deleting own account
            if ((int) $user->id === (int) auth()->id()) {
                return redirect()->back()
                    ->with('error', 'Tidak dapat menghapus akun sendiri!');
            }

            $user->delete();

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }

    /**
     * Return user JSON for AJAX request.
     */
    public function json(User $user)
    {
        try {
            // Load relationships
            $user->load(['division', 'position']);
            
            // Return user data as JSON
            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'nip' => $user->nip,
                'phone' => $user->phone,
                'division_id' => $user->division_id,
                'position_id' => $user->position_id,
                'role' => $user->role,
                'is_active' => $user->is_active ? 1 : 0,
                'join_date' => $user->join_date,
                'division' => $user->division ? [
                    'id' => $user->division->id,
                    'name' => $user->division->name
                ] : null,
                'position' => $user->position ? [
                    'id' => $user->position->id,
                    'name' => $user->position->name
                ] : null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan'
            ], 404);
        }
    }
}