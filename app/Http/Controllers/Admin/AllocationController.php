<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Allocation;
use App\Models\Penilaian;
use App\Models\Siklus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AllocationController extends Controller
{
    /**
     * Display all allocations
     */
    public function index()
    {
        try {
            $allocations = Allocation::with(['siklus', 'penilai.position', 'dinilai.position'])
                ->orderBy('id', 'DESC')
                ->get();

            $siklusList = Siklus::where('status', 'active')->get();
            
            // Penilai: semua user aktif dengan position_id = 1
            $penilaiList = User::where('position_id', 1)
                ->where('is_active', 1)
                ->with('position')
                ->get();

            // Yang dinilai: hanya staff aktif
            $dinilaiList = User::where('role', 'staff')
                ->where('is_active', 1)
                ->with('position')
                ->get();

            return view('admin.allocations.index', compact(
                'allocations',
                'siklusList',
                'penilaiList',
                'dinilaiList'
            ));
        } catch (\Exception $e) {
            Log::error("Allocation Index Error: " . $e->getMessage());
            return back()->with('error', 'Gagal memuat data alokasi.');
        }
    }

    /**
     * Auto Allocation berdasarkan position_id = 1 sebagai penilai
     */
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $request->validate([
                'siklus_id' => 'required|exists:siklus,id'
            ]);

            $siklusId = $request->siklus_id;
            $totalCreated = 0;

            // Ambil semua user dengan position_id = 1 sebagai penilai
            $penilaiList = User::where('position_id', 1)
                ->where('is_active', 1)
                ->get();

            if ($penilaiList->isEmpty()) {
                DB::rollBack();
                return back()->with('error', 'Tidak ada user dengan position_id = 1 (penilai) yang aktif.');
            }

            foreach ($penilaiList as $penilai) {
                // Ambil staff aktif di divisi yang sama
                $staffs = User::where('role', 'staff')
                    ->where('is_active', 1)
                    ->where('division_id', $penilai->division_id)
                    ->get();

                foreach ($staffs as $staff) {
                    // Hindari self-allocation (jika ada kesalahan data)
                    if ($penilai->id === $staff->id) {
                        continue;
                    }

                    // Cek duplikasi alokasi
                    if (Allocation::where([
                        'siklus_id'  => $siklusId,
                        'penilai_id' => $penilai->id,
                        'dinilai_id' => $staff->id,
                    ])->exists()) {
                        continue;
                    }

                    // Buat alokasi
                    $allocation = Allocation::create([
                        'siklus_id'  => $siklusId,
                        'penilai_id' => $penilai->id,
                        'dinilai_id' => $staff->id,
                        'status'     => 'in_progress'
                    ]);

                    // Buat record penilaian awal
                    Penilaian::create([
                        'allocation_id' => $allocation->id,
                        'status'        => 'belum_dinilai'
                    ]);

                    $totalCreated++;
                }
            }

            DB::commit();

            if ($totalCreated === 0) {
                return back()->with('warning', 'Tidak ada alokasi baru yang dibuat. Semua kombinasi sudah ada.');
            }

            return back()->with('success', "Berhasil membuat {$totalCreated} alokasi otomatis.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Auto Allocation Error: " . $e->getMessage());
            return back()->with('error', 'Gagal membuat alokasi otomatis: ' . $e->getMessage());
        }
    }

    /**
     * Return JSON for edit modal (AJAX)
     */
    public function edit($id)
    {
        try {
            $allocation = Allocation::find($id);

            if (!$allocation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data alokasi tidak ditemukan'
                ], 404);
            }

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'allocation' => $allocation
                ]);
            }

            return view('admin.allocations.edit', compact('allocation'));
        } catch (\Exception $e) {
            Log::error("Edit Allocation Error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data edit'
            ], 500);
        }
    }

    /**
     * Update allocation
     */
    public function update(Request $request, $id)
    {
        try {
            $allocation = Allocation::findOrFail($id);

            $validated = $request->validate([
                'siklus_id'  => 'required|exists:siklus,id',
                'penilai_id' => 'required|exists:users,id',
                'dinilai_id' => 'required|exists:users,id|different:penilai_id',
                'status'     => 'required|in:pending,in_progress,completed',
            ]);

            $penilai = User::find($validated['penilai_id']);
            $dinilai = User::find($validated['dinilai_id']);

            // ✅ Validasi: penilai HARUS position_id = 1
            if ($penilai->position_id !== 1) {
                return back()->with('error', 'Penilai harus memiliki position_id = 1')->withInput();
            }

            // ✅ Validasi: yang dinilai HARUS role = staff
            if ($dinilai->role !== 'staff') {
                return back()->with('error', 'Yang dinilai harus memiliki role "staff"')->withInput();
            }

            // Cek duplikat (kecuali diri sendiri)
            $duplicate = Allocation::where('siklus_id', $validated['siklus_id'])
                ->where('penilai_id', $validated['penilai_id'])
                ->where('dinilai_id', $validated['dinilai_id'])
                ->where('id', '!=', $allocation->id)
                ->exists();

            if ($duplicate) {
                return back()->with('error', 'Alokasi dengan kombinasi tersebut sudah ada')->withInput();
            }

            $allocation->update($validated);

            return redirect()->route('admin.allocations.index')
                ->with('success', 'Alokasi berhasil diperbarui');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error("Update Allocation Error: " . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui alokasi: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Delete allocation
     */
    public function destroy($id)
    {
        try {
            $allocation = Allocation::findOrFail($id);
            $allocation->delete();

            return redirect()->route('admin.allocations.index')
                ->with('success', 'Alokasi berhasil dihapus');
        } catch (\Exception $e) {
            Log::error("Delete Allocation Error: " . $e->getMessage());
            return back()->with('error', 'Gagal menghapus alokasi: ' . $e->getMessage());
        }
    }
}