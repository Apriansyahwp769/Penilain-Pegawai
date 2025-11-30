<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Division;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with(['division', 'position'])
            ->orderBy('created_at', 'desc')
            ->get();

        $divisions = Division::where('status', true)->get();
        $positions = Position::where('status', true)->get();

        return view('admin.users.index', compact('users', 'divisions', 'positions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $divisions = Division::where('status', true)->get();
        $positions = Position::where('status', true)->get();

        return view('admin.users.create', compact('divisions', 'positions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'nip' => 'nullable|unique:users',
            'division_id' => 'nullable|exists:divisions,id',
            'position_id' => 'nullable|exists:positions,id',
            'phone' => 'nullable|string|max:20',
            'join_date' => 'nullable|date',
            'role' => 'required|in:admin,ketua_divisi,staff',
            'is_active' => 'required|boolean'
        ]);

        try {
            $validated['password'] = Hash::make($validated['password']);

            User::create($validated);

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil dibuat!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal membuat user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $divisions = Division::where('status', true)->get();
        $positions = Position::where('status', true)->get();

        return view('admin.users.edit', compact('user', 'divisions', 'positions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'password'   => 'nullable|min:8',
            'nip'        => 'nullable|unique:users,nip,' . $user->id,
            'division_id'=> 'nullable|exists:divisions,id',
            'position_id'=> 'nullable|exists:positions,id',
            'phone'      => 'nullable|string|max:20',
            'join_date'  => 'nullable|date',
            'role'       => 'required|in:admin,ketua_divisi,staff',
            'is_active'  => 'required|boolean'
        ]);

        try {
            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $user->update($validated);

            return redirect()->route('admin.users.index')
                ->with('success', 'User berhasil diperbarui!');
        } catch (\Exception $e) {
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
            $user->load(['division', 'position']);
            return response()->json($user);
        } catch (\Exception $e) {
            return response()->json(['error' => 'User tidak ditemukan'], 404);
        }
    }
}
