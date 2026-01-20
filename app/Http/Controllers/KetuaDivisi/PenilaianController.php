<?php

namespace App\Http\Controllers\KetuaDivisi;

use App\Http\Controllers\Controller;
use App\Models\Allocation;
use App\Models\Penilaian;
use App\Models\HasilPenilaian;
use App\Models\Criterion;
use App\Models\LogActivity; // ← Tambahkan ini
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
                    $query->where('status', 'active');
                })
                ->byPenilai($userId)
                ->orderBy('created_at', 'DESC')
                ->get();

            // Mapping status ke label & badge
            $statusConfig = [
                'selesai' => [
                    'label' => 'Selesai',
                    'badge' => 'bg-green-100 text-green-800'
                ],
                'draft' => [
                    'label' => 'Draft',
                    'badge' => 'bg-yellow-100 text-yellow-800'
                ],
                'menunggu_verifikasi' => [
                    'label' => 'Menunggu Verifikasi',
                    'badge' => 'bg-orange-100 text-orange-800'
                ],
                'belum_dinilai' => [
                    'label' => 'Belum Dinilai',
                    'badge' => 'bg-red-100 text-red-800'
                ],
            ];

            $employees = $allocations->map(function ($allocation) use ($statusConfig) {
                $penilaian = $allocation->penilaian;

                if ($penilaian) {
                    $status = $penilaian->status; // draft, menunggu_verifikasi, selesai
                } else {
                    $status = 'belum_dinilai';
                }

                $config = $statusConfig[$status] ?? [
                    'label' => 'Tidak Diketahui',
                    'badge' => 'bg-gray-100 text-gray-800'
                ];

                return [
                    'allocation_id' => $allocation->id,
                    'penilaian_id' => $penilaian ? $penilaian->id : null,
                    'nip' => $allocation->dinilai->nip ?? '-',
                    'name' => $allocation->dinilai->name ?? '-',
                    'position' => $allocation->dinilai->position->name ?? '-',
                    'division' => $allocation->dinilai->division->name ?? '-',
                    'skor_akhir' => $penilaian ? $penilaian->skor_akhir : null,
                    'status' => $status,
                    'status_badge' => $config['badge'],
                    'status_label' => $config['label'],
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

            // Jika status = selesai, redirect ke view (tidak boleh edit lagi)
            if ($penilaian && $penilaian->status === 'selesai') {
                return redirect()->route('ketua-divisi.penilaian.show', $penilaian->id)
                    ->with('info', 'Penilaian sudah selesai dan tidak dapat diedit.');
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

            // Ambil hasil penilaian sebelumnya (jika draft atau menunggu verifikasi)
            $hasilPenilaian = [];
            if ($penilaian) {
                $hasilPenilaian = HasilPenilaian::where('penilaian_id', $penilaian->id)
                    ->get()
                    ->keyBy('criterion_id');
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
                'file_penunjang.*' => 'nullable|file|mimes:pdf|max:5120',
                'catatan' => 'nullable|string',
                'status' => 'required|in:draft,menunggu_verifikasi'
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

            // Ambil hasil penilaian lama
            $existingHasil = HasilPenilaian::where('penilaian_id', $penilaian->id)
                ->get()
                ->keyBy('criterion_id');

            // Hapus semua hasil lama
            HasilPenilaian::where('penilaian_id', $penilaian->id)->delete();

            // Simpan hasil penilaian baru
            foreach ($validated['skor'] as $criterionId => $skor) {
                $filePath = null;

                if ($request->hasFile("file_penunjang.{$criterionId}")) {
                    $file = $request->file("file_penunjang.{$criterionId}");
                    $fileName = time() . '_' . $criterionId . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('penilaian/penunjang', $fileName, 'public');

                    // Hapus file lama jika ada
                    if (isset($existingHasil[$criterionId]) && $existingHasil[$criterionId]->file_penunjang) {
                        Storage::delete($existingHasil[$criterionId]->file_penunjang);
                    }
                } elseif (isset($existingHasil[$criterionId]) && !$request->input("hapus_file.{$criterionId}", false)) {
                    // Pertahankan file lama jika tidak dihapus
                    $filePath = $existingHasil[$criterionId]->file_penunjang;
                }

                HasilPenilaian::create([
                    'penilaian_id' => $penilaian->id,
                    'criterion_id' => $criterionId,
                    'skor' => $skor,
                    'file_penunjang' => $filePath
                ]);
            }

            DB::commit();
            $penilaianId = $penilaian->id;

            if ($validated['status'] === 'menunggu_verifikasi') {
                try {
                    LogActivity::create([
                        'user_id' => Auth::id(),
                        'allocation_id' => $allocationId,
                        'penilaian_id' => $penilaianId,
                        'action' => 'submit',
                    ]);
                } catch (\Exception $e) {
                    Log::warning("Gagal simpan log aktivitas: " . $e->getMessage());
                }
            }

            $message = $validated['status'] === 'menunggu_verifikasi'
                ? 'Penilaian berhasil dikirim untuk verifikasi!'
                : 'Draft penilaian berhasil disimpan!';

            return redirect()->route('ketua-divisi.penilaian.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Penilaian Store Error: " . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan penilaian. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Show detail penilaian yang sudah selesai
     */
    public function show($id)
    {
        try {
            $penilaian = Penilaian::with([
                'allocation.dinilai.position',
                'allocation.dinilai.division',
                'allocation.siklus'
            ])->findOrFail($id);

            // Cek akses
            if ($penilaian->allocation->penilai_id !== Auth::id()) {
                abort(403, 'Unauthorized access');
            }

            // Ambil hasil penilaian per kategori
            $hasilKuantitatif = HasilPenilaian::with('criterion')
                ->where('penilaian_id', $id)
                ->whereHas('criterion', function ($q) {
                    $q->where('category', 'Kuantitatif')
                        ->where('status', 1);
                })
                ->get();

            $hasilKompetensi = HasilPenilaian::with('criterion')
                ->where('penilaian_id', $id)
                ->whereHas('criterion', function ($q) {
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
            return back()->with('error', 'Gagal memuat detail penilaian.');
        }
    }

    /**
     * Download file penunjang
     */
    public function downloadFile($hasilPenilaianId)
    {
        try {
            $hasil = HasilPenilaian::with('penilaian.allocation')->findOrFail($hasilPenilaianId);

            // Cek akses: boleh diakses oleh PENILAI atau HRD
            $user = Auth::user();

            // Jika user adalah penilai
            $isPenilai = $hasil->penilaian->allocation->penilai_id === $user->id;

            // Jika user adalah HRD (ketua_divisi + position_id=2)
            $isHrd = ($user->role === 'ketua_divisi' && $user->position_id == 2);

            if (!$isPenilai && !$isHrd) {
                abort(403, 'Unauthorized access');
            }

            if (
                !$hasil->file_penunjang ||
                !Storage::disk('public')->exists($hasil->file_penunjang)
            ) {
                return back()->with('error', 'File tidak ditemukan.');
            }

            return Storage::disk('public')->download($hasil->file_penunjang);
        } catch (\Exception $e) {
            Log::error("Download File Error: " . $e->getMessage());
            return back()->with('error', 'Gagal mengunduh file.');
        }
    }
}
