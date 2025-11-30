<?php

namespace App\Http\Controllers\KetuaDivisi;

use App\Http\Controllers\Controller;
use App\Models\Allocation;
use App\Models\Penilaian;
use App\Models\HasilPenilaian;
use App\Models\Criterion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PenilaianController extends Controller
{
    /**
     * Display penilaian list untuk ketua divisi yang login
     */
   public function index(Request $request)
{
    try {
        $userId = Auth::id();

        $allocations = Allocation::with([
                'dinilai.position',
                'dinilai.division',
                'siklus',
                'penilaian'
            ])
            ->whereHas('siklus', function ($query) {
                // Sesuaikan dengan struktur kolom di tabel siklus
                // Pilihan 1: jika ada kolom `is_active` (boolean)
                // $query->where('is_active', true);

                // Pilihan 2: jika ada kolom `status` dengan nilai 'active'
                $query->where('status', 'active');
            })
            ->byPenilai($userId)
            ->orderBy('created_at', 'DESC')
            ->get();

        $employees = $allocations->map(function ($allocation) {
            $penilaian = $allocation->penilaian;

            return [
                'allocation_id' => $allocation->id,
                'penilaian_id' => $penilaian ? $penilaian->id : null,
                'nip' => $allocation->dinilai->nip ?? '-',
                'name' => $allocation->dinilai->name ?? '-',
                'position' => $allocation->dinilai->position->name ?? '-',
                'division' => $allocation->dinilai->division->name ?? '-',
                'skor_akhir' => $penilaian ? $penilaian->skor_akhir : null,
                'status' => $penilaian ? $penilaian->status : 'belum_dinilai',
                'status_badge' => $penilaian ? $penilaian->status_badge : 'bg-red-100 text-red-800',
                'status_label' => $penilaian ? $penilaian->status_label : 'Belum Dinilai',
            ];
        });

        return view('ketua-divisi.penilaian.index', compact('employees'));

    } catch (\Exception $e) {
        Log::error("Penilaian Index Error: " . $e->getMessage());
        return back()->with('error', 'Gagal memuat data penilaian.');
    }
}

    /**
     * Show form untuk melakukan penilaian
     */
    public function create($allocationId)
    {
        try {
            $allocation = Allocation::with(['dinilai.position', 'dinilai.division', 'siklus'])
                ->where('penilai_id', Auth::id())
                ->findOrFail($allocationId);

            // Cek apakah sudah ada penilaian
            $penilaian = Penilaian::where('allocation_id', $allocationId)->first();

            // Jika sudah selesai, redirect ke view
            if ($penilaian && $penilaian->status === 'selesai') {
                return redirect()->route('ketua-divisi.penilaian.show', $penilaian->id)
                    ->with('info', 'Penilaian sudah diselesaikan. Anda hanya bisa melihat.');
            }

            // Ambil kriteria aktif berdasarkan kategori
            $kuantitatif = Criterion::where('status', 1)
                ->where('category', 'Kuantitatif')
                ->orderBy('name')
                ->get();

            $kompetensi = Criterion::where('status', 1)
                ->where('category', 'Kompetensi')
                ->orderBy('name')
                ->get();

            // Ambil hasil penilaian sebelumnya (jika draft)
            $hasilPenilaian = [];
            if ($penilaian) {
                $hasilPenilaian = HasilPenilaian::where('penilaian_id', $penilaian->id)
                    ->pluck('skor', 'criterion_id')
                    ->toArray();
            }

            return view('ketua-divisi.penilaian.create', compact(
                'allocation', 
                'penilaian', 
                'kuantitatif', 
                'kompetensi',
                'hasilPenilaian'
            ));

        } catch (\Exception $e) {
            Log::error("Penilaian Create Error: " . $e->getMessage());
            return back()->with('error', 'Gagal memuat form penilaian.');
        }
    }

    /**
     * Store atau update penilaian
     */
    public function store(Request $request, $allocationId)
    {
        try {
            $validated = $request->validate([
                'skor' => 'required|array',
                'skor.*' => 'required|numeric|min:1|max:5',
                'catatan' => 'nullable|string',
                'status' => 'required|in:draft,selesai'
            ]);

            $allocation = Allocation::where('penilai_id', Auth::id())
                ->findOrFail($allocationId);

            DB::beginTransaction();

            // Hitung skor akhir berdasarkan bobot
            $totalSkor = 0;
            $totalBobot = 0;

            foreach ($validated['skor'] as $criterionId => $skor) {
                $criterion = Criterion::find($criterionId);
                if ($criterion && $criterion->status == 1) {
                    $totalSkor += ($skor * $criterion->weight);
                    $totalBobot += $criterion->weight;
                }
            }

            $skorAkhir = $totalBobot > 0 ? $totalSkor / $totalBobot : 0;

            // Update atau create penilaian
            $penilaian = Penilaian::updateOrCreate(
                ['allocation_id' => $allocationId],
                [
                    'skor_akhir' => round($skorAkhir, 2),
                    'catatan' => $validated['catatan'] ?? null,
                    'status' => $validated['status']
                ]
            );

            // Hapus hasil penilaian lama (jika ada)
            HasilPenilaian::where('penilaian_id', $penilaian->id)->delete();

            // Simpan hasil penilaian baru
            foreach ($validated['skor'] as $criterionId => $skor) {
                HasilPenilaian::create([
                    'penilaian_id' => $penilaian->id,
                    'criterion_id' => $criterionId,
                    'skor' => $skor
                ]);
            }

            DB::commit();

            $message = $validated['status'] === 'selesai' 
                ? 'Penilaian berhasil diselesaikan dan dikirim!' 
                : 'Draft penilaian berhasil disimpan!';

            return redirect()->route('ketua-divisi.penilaian.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Penilaian Store Error: " . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan penilaian.')->withInput();
        }
    }

    /**
     * Show detail penilaian yang sudah selesai (UPDATED)
     */
    public function show($id)
    {
        try {
            $penilaian = Penilaian::with([
                'allocation.dinilai.position',
                'allocation.dinilai.division',
                'allocation.siklus'
            ])->findOrFail($id);

            // Cek apakah penilaian ini milik ketua divisi yang login
            if ($penilaian->allocation->penilai_id !== Auth::id()) {
                abort(403, 'Unauthorized access');
            }

            // Ambil hasil penilaian per kategori
            $hasilKuantitatif = HasilPenilaian::with('criterion')
                ->where('penilaian_id', $id)
                ->whereHas('criterion', function($q) {
                    $q->where('category', 'Kuantitatif')
                      ->where('status', 1);
                })
                ->get();

            $hasilKompetensi = HasilPenilaian::with('criterion')
                ->where('penilaian_id', $id)
                ->whereHas('criterion', function($q) {
                    $q->where('category', 'Kompetensi')
                      ->where('status', 1);
                })
                ->get();

            return view('ketua-divisi.penilaian.show', compact(
                'penilaian',
                'hasilKuantitatif',
                'hasilKompetensi'
            ));

        } catch (\Exception $e) {
            Log::error("Penilaian Show Error: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            return back()->with('error', 'Gagal memuat detail penilaian.');
        }
    }
}