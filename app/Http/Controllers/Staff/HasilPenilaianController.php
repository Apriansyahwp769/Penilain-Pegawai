<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Penilaian;
use App\Models\HasilPenilaian;
use App\Models\Siklus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HasilPenilaianController extends Controller
{
    /**
     * Display hasil penilaian untuk staff yang login (hanya siklus aktif)
     */
    public function index()
    {
        try {
            $userId = Auth::id();

            // Ambil siklus aktif
            $siklusAktif = Siklus::where('status', 'active')->first();

            // Inisialisasi data kosong
            $penilaian = null;
            $hasilKuantitatif = collect();
            $hasilKompetensi = collect();
            $skorAkhir = null;
            $kategoriSkor = null;
            $penilai = null;
            $catatan = null;

            if ($siklusAktif) {
                // Query penilaian untuk staff yang login di siklus aktif
                $penilaian = Penilaian::with([
                    'allocation.penilai.position',
                    'allocation.siklus'
                ])
                ->whereHas('allocation', function($q) use ($userId, $siklusAktif) {
                    $q->where('dinilai_id', $userId)
                      ->where('siklus_id', $siklusAktif->id);
                })
                ->where('status', 'selesai')
                ->first();

                if ($penilaian) {
                    // Ambil hasil penilaian per kategori
                    $hasilKuantitatif = HasilPenilaian::with('criterion')
                        ->where('penilaian_id', $penilaian->id)
                        ->whereHas('criterion', function($q) {
                            $q->where('category', 'Kuantitatif')
                              ->where('status', 1);
                        })
                        ->get();

                    $hasilKompetensi = HasilPenilaian::with('criterion')
                        ->where('penilaian_id', $penilaian->id)
                        ->whereHas('criterion', function($q) {
                            $q->where('category', 'Kompetensi')
                              ->where('status', 1);
                        })
                        ->get();

                    $skorAkhir = $penilaian->skor_akhir;
                    $kategoriSkor = $this->getKategoriSkor($skorAkhir);
                    $penilai = $penilaian->allocation->penilai;
                    $catatan = $penilaian->catatan;
                }
            }

            return view('staff.hasil_penilaian.index', compact(
                'penilaian',
                'hasilKuantitatif',
                'hasilKompetensi',
                'skorAkhir',
                'kategoriSkor',
                'penilai',
                'catatan',
                'siklusAktif'
            ));

        } catch (\Exception $e) {
            Log::error("Hasil Penilaian Staff Error: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            return back()->with('error', 'Gagal memuat hasil penilaian.');
        }
    }

    /**
     * Helper function untuk menentukan kategori skor
     */
    private function getKategoriSkor($skor)
    {
        if ($skor >= 4.0) {
            return [
                'label' => 'Bagus',
                'color' => 'green',
                'bg' => 'bg-green-100',
                'text' => 'text-green-800',
                'badge' => 'text-green-500'
            ];
        } elseif ($skor >= 3.0) {
            return [
                'label' => 'Normal',
                'color' => 'blue',
                'bg' => 'bg-blue-100',
                'text' => 'text-blue-800',
                'badge' => 'text-blue-500'
            ];
        } else {
            return [
                'label' => 'Rendah',
                'color' => 'red',
                'bg' => 'bg-red-100',
                'text' => 'text-red-800',
                'badge' => 'text-red-600'
            ];
        }
    }
}