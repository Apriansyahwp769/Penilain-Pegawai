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
            $penilaiList = User::where('role', 'ketua_divisi')->where('is_active', 1)->with('position')->get();
            $dinilaiList = User::where('role', 'staff')->where('is_active', 1)->with('position')->get();

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
     * Store new allocation
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        
        try {
            $validated = $request->validate([
                'siklus_id'  => 'required|exists:siklus,id',
                'penilai_id' => 'required|exists:users,id',
                'dinilai_id' => 'required|exists:users,id|different:penilai_id',
            ]);

            $validated['status'] = 'pending';

            $penilai = User::find($validated['penilai_id']);
            $dinilai = User::find($validated['dinilai_id']);

            if ($penilai->role !== 'ketua_divisi') {
                return back()->with('error', 'Penilai harus Ketua Divisi')->withInput();
            }

            if ($dinilai->role !== 'staff') {
                return back()->with('error', 'Yang dinilai harus Staff')->withInput();
            }

            $exists = Allocation::where('siklus_id', $validated['siklus_id'])
                ->where('penilai_id', $validated['penilai_id'])
                ->where('dinilai_id', $validated['dinilai_id'])
                ->exists();

            if ($exists) {
                return back()->with('error', 'Alokasi dengan kombinasi tersebut sudah ada')->withInput();
            }

            // Create Allocation
            $allocation = Allocation::create($validated);

            // Auto create Penilaian record dengan status belum_dinilai
            Penilaian::create([
                'allocation_id' => $allocation->id,
                'status' => 'belum_dinilai',
                'skor_akhir' => null,
                'catatan' => null
            ]);

            DB::commit();

            return redirect()->route('admin.allocations.index')
                ->with('success', 'Alokasi berhasil ditambahkan');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Store Allocation Error: " . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan alokasi: ' . $e->getMessage())->withInput();
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

            // Jika request AJAX, kirim JSON
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'allocation' => $allocation
                ]);
            }

            // Jika akses langsung lewat browser
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

            if ($penilai->role !== 'ketua_divisi') {
                return back()->with('error', 'Penilai harus Ketua Divisi')->withInput();
            }

            if ($dinilai->role !== 'staff') {
                return back()->with('error', 'Yang dinilai harus Staff')->withInput();
            }

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
            // Penilaian akan terhapus otomatis karena onDelete('cascade')
            $allocation->delete();

            return redirect()->route('admin.allocations.index')
                ->with('success', 'Alokasi berhasil dihapus');

        } catch (\Exception $e) {
            Log::error("Delete Allocation Error: " . $e->getMessage());
            return back()->with('error', 'Gagal menghapus alokasi: ' . $e->getMessage());
        }
    }
}