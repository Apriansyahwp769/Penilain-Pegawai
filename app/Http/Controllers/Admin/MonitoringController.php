<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penilaian;
use App\Models\HasilPenilaian;
use App\Models\Siklus;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class MonitoringController extends Controller
{
    public function index()
    {
        try {
            $siklusAktif = Siklus::where('status', 'active')->first();

            if (!$siklusAktif) {
                return view('admin.monitoring.index', [
                    'siklusAktif' => null,
                    'monitoring' => collect(),
                    'divisions' => Division::where('status', true)->get(),
                    'totalPenilaian' => 0,
                    'skorRataRata' => 0,
                    'daysLeft' => 0,
                    'deadlinePassed' => false,
                    'deadlineDate' => '-',
                ]);
            }

            $divisions = Division::where('status', true)->get();

            // Ambil penilaian selesai dari siklus aktif
            $penilaianList = Penilaian::with([
                'allocation.dinilai.position',
                'allocation.dinilai.division',
                'allocation.penilai',
            ])
            ->whereHas('allocation', function($q) use ($siklusAktif) {
                $q->where('siklus_id', $siklusAktif->id);
            })
            ->where('status', 'selesai')
            ->orderBy('updated_at', 'DESC')
            ->get();

            $totalPenilaian = $penilaianList->count();
            $skorRataRata = $totalPenilaian > 0 
                ? round($penilaianList->avg('skor_akhir'), 1) 
                : 0;

            // Hitung deadline dengan aman
            $deadline = Carbon::parse($siklusAktif->tanggal_finalisasi)->startOfDay();
            $today = Carbon::today();
            $daysLeft = (int) $today->diffInDays($deadline, false);
            $deadlinePassed = $deadline->lt($today);
            $deadlineDate = $deadline->format('d M Y');

            // Transform data
            $monitoring = $penilaianList->map(function($penilaian) {
                $skor = $penilaian->skor_akhir;

                if ($skor >= 4.0) {
                    $status = 'Tinggi';
                    $badge = 'bg-green-100 text-green-800';
                } elseif ($skor >= 3.0) {
                    $status = 'Normal';
                    $badge = 'bg-yellow-100 text-yellow-800';
                } else {
                    $status = 'Rendah';
                    $badge = 'bg-red-100 text-red-800';
                }

                return [
                    'penilaian_id' => $penilaian->id,
                    'pegawai_nama' => $penilaian->allocation->dinilai->name ?? '-',
                    'pegawai_jabatan' => $penilaian->allocation->dinilai->position->name ?? '-',
                    'pegawai_divisi' => $penilaian->allocation->dinilai->division->name ?? '-',
                    'division_id' => $penilaian->allocation->dinilai->division_id ?? null,
                    'skor_akhir' => $skor,
                    'penilai_nama' => $penilaian->allocation->penilai->name ?? '-',
                    'status' => $status,
                    'status_badge' => $badge,
                    'tanggal_dinilai' => $penilaian->updated_at->format('d M Y'),
                ];
            });

            return view('admin.monitoring.index', compact(
                'siklusAktif',
                'monitoring',
                'divisions',
                'totalPenilaian',
                'skorRataRata',
                'daysLeft',
                'deadlinePassed',
                'deadlineDate'
            ));

        } catch (\Exception $e) {
            Log::error("Monitoring Index Error: " . $e->getMessage());
            return view('admin.monitoring.index', [
                'siklusAktif' => null,
                'monitoring' => collect(),
                'divisions' => collect(),
                'totalPenilaian' => 0,
                'skorRataRata' => 0,
                'daysLeft' => 0,
                'deadlinePassed' => false,
                'deadlineDate' => '-',
            ])->with('error', 'Gagal memuat data monitoring.');
        }
    }

    /**
     * Show detail penilaian (Admin dapat mengubah status)
     */
    public function show($id)
    {
        try {
            $penilaian = Penilaian::with([
                'allocation.dinilai.position',
                'allocation.dinilai.division',
                'allocation.siklus',
                'allocation.penilai'
            ])->findOrFail($id);

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

            return view('admin.monitoring.show', compact(
                'penilaian',
                'hasilKuantitatif',
                'hasilKompetensi'
            ));

        } catch (\Exception $e) {
            Log::error("Monitoring Show Error: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            return back()->with('error', 'Gagal memuat detail penilaian.');
        }
    }

    /**
     * Update status penilaian
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:selesai,draft,belum_dinilai'
            ]);

            $penilaian = Penilaian::findOrFail($id);
            $penilaian->update(['status' => $validated['status']]);

            return redirect()->route('admin.monitoring.show', $id)
                ->with('success', 'Status penilaian berhasil diperbarui!');

        } catch (\Exception $e) {
            Log::error("Update Status Error: " . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui status: ' . $e->getMessage());
        }
    }
}