<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Penilaian;
use App\Models\HasilPenilaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VerifikasiController extends Controller
{
    public function index()
    {
        try {
            // Hanya tampilkan status: menunggu_verifikasi dan selesai (dari siklus aktif)
            $penilaians = Penilaian::with([
                'allocation.dinilai.position',
                'allocation.dinilai.division',
                'allocation.siklus'
            ])
                ->whereHas('allocation.siklus', function ($q) {
                    $q->where('status', 'active');
                })
                ->whereIn('status', ['menunggu_verifikasi', 'selesai'])
                ->orderBy('updated_at', 'desc')
                ->get();

            $employees = $penilaians->map(function ($penilaian) {
                $statusConfig = [
                    'menunggu_verifikasi' => [
                        'label' => 'Menunggu Verifikasi',
                        'badge' => 'bg-orange-100 text-orange-800'
                    ],
                    'selesai' => [
                        'label' => 'Selesai',
                        'badge' => 'bg-green-100 text-green-800'
                    ]
                ];

                $config = $statusConfig[$penilaian->status] ?? [
                    'label' => 'Tidak Diketahui',
                    'badge' => 'bg-gray-100 text-gray-800'
                ];

                return [
                    'penilaian_id' => $penilaian->id,
                    'nip' => $penilaian->allocation->dinilai->nip ?? '-',
                    'name' => $penilaian->allocation->dinilai->name ?? '-',
                    'position' => $penilaian->allocation->dinilai->position->name ?? '-',
                    'division' => $penilaian->allocation->dinilai->division->name ?? '-',
                    'skor_akhir' => $penilaian->skor_akhir,
                    'updated_at' => $penilaian->updated_at->format('d M Y, H:i'),
                    'siklus' => $penilaian->allocation->siklus->nama ?? '-',
                    'status_badge' => $config['badge'],
                    'status_label' => $config['label'],
                ];
            });

            return view('hrd.verifikasi.index', compact('employees'));
        } catch (\Exception $e) {
            Log::error("HRD Verifikasi Index Error: " . $e->getMessage());
            return back()->with('error', 'Gagal memuat data penilaian.');
        }
    }

    public function show(Penilaian $penilaian)
    {
        try {
            if ($penilaian->allocation->siklus->status !== 'active') {
                abort(404, 'Siklus tidak aktif.');
            }

            if (!in_array($penilaian->status, ['menunggu_verifikasi', 'selesai'])) {
                abort(403, 'Penilaian tidak dalam status yang dapat dilihat.');
            }

            $hasilKuantitatif = HasilPenilaian::with('criterion')
                ->where('penilaian_id', $penilaian->id)
                ->whereHas('criterion', function ($q) {
                    $q->where('category', 'Kuantitatif')->where('status', 1);
                })
                ->get();

            $hasilKompetensi = HasilPenilaian::with('criterion')
                ->where('penilaian_id', $penilaian->id)
                ->whereHas('criterion', function ($q) {
                    $q->where('category', 'Kompetensi')->where('status', 1);
                })
                ->get();

            return view('hrd.verifikasi.show', compact(
                'penilaian',
                'hasilKuantitatif',
                'hasilKompetensi'
            ));
        } catch (\Exception $e) {
            Log::error("HRD Verifikasi Show Error: " . $e->getMessage());
            return back()->with('error', 'Gagal memuat detail penilaian.');
        }
    }

    public function verify(Request $request, Penilaian $penilaian)
    {
        $request->validate([
            'action' => 'required|in:terima,tolak'
        ]);

        try {
            if ($penilaian->status !== 'menunggu_verifikasi') {
                return back()->with('error', 'Hanya penilaian menunggu verifikasi yang bisa diproses.');
            }

            DB::beginTransaction();

            if ($request->action === 'terima') {
                $penilaian->update(['status' => 'selesai']);
                $message = '✅ Penilaian berhasil diterima dan ditutup.';
            } else {
                // TOLAK → HAPUS data penilaian & hasil → status kembali ke "belum_dinilai"
                HasilPenilaian::where('penilaian_id', $penilaian->id)->delete();
                $penilaian->delete(); // ← ini membuat allocation kembali ke "belum_dinilai"
                $message = '❌ Penilaian ditolak. Ketua divisi diminta mengisi ulang.';
            }

            DB::commit();

            return redirect()->route('hrd.verifikasi.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("HRD Verifikasi Verify Error: " . $e->getMessage());
            return back()->with('error', 'Gagal memverifikasi penilaian.');
        }
    }
}