<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Penilaian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        try {
            $userId = Auth::id();

            // Query penilaian yang sudah selesai untuk staff ini
            $query = Penilaian::with([
                'allocation.siklus',
                'allocation.penilai.position'
            ])
            ->whereHas('allocation', function($q) use ($userId) {
                $q->where('dinilai_id', $userId);
            })
            ->where('status', 'selesai');

            // Filter berdasarkan kategori (Bagus/Normal/Rendah)
            $selectedKategori = $request->get('kategori');
            if (in_array($selectedKategori, ['Bagus', 'Normal', 'Rendah'])) {
                $query->where(function($q) use ($selectedKategori) {
                    if ($selectedKategori === 'Bagus') {
                        $q->where('skor_akhir', '>=', 4.0);
                    } elseif ($selectedKategori === 'Normal') {
                        $q->whereBetween('skor_akhir', [3.0, 3.99]);
                    } else { // Rendah
                        $q->where('skor_akhir', '<', 3.0);
                    }
                });
            }

            $riwayatList = $query->orderBy('updated_at', 'DESC')->get();

            // Transform data untuk view
            $riwayat = $riwayatList->map(function ($penilaian) {
                $kategori = $this->getKategoriSkor($penilaian->skor_akhir);
                return [
                    'penilaian_id' => $penilaian->id,
                    'periode' => $penilaian->allocation->siklus->nama ?? '-',
                    'skor_akhir' => $penilaian->skor_akhir,
                    'status_label' => $kategori['label'], // <-- ini yang ditampilkan di kolom "Status"
                    'status_badge' => $kategori['bg'] . ' ' . $kategori['text'],
                    'tanggal' => $penilaian->updated_at->format('d M Y'),
                ];
            });

            // Data untuk chart: ambil SEMUA riwayat (tanpa batas) setelah filter, urutkan kronologis
            $chartData = $riwayatList
                ->sortBy('allocation.siklus.tanggal_mulai') // urut dari lama ke baru
                ->values()
                ->map(function ($penilaian) {
                    return [
                        'periode' => $penilaian->allocation->siklus->nama ?? '-',
                        'skor' => (float) $penilaian->skor_akhir
                    ];
                });

            // List semua kategori untuk filter dropdown
            $kategoriList = ['Bagus', 'Normal', 'Rendah'];

            return view('staff.riwayat.index', compact('riwayat', 'chartData', 'kategoriList', 'selectedKategori'));

        } catch (\Exception $e) {
            Log::error("Riwayat Staff Error: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            return back()->with('error', 'Gagal memuat data riwayat.');
        }
    }

    public function show($id)
    {
        try {
            $penilaian = Penilaian::with([
                'allocation.dinilai.position',
                'allocation.dinilai.division',
                'allocation.siklus',
                'allocation.penilai.position',
                'hasilPenilaian.criterion'
            ])->findOrFail($id);

            if ($penilaian->allocation->dinilai_id !== Auth::id()) {
                abort(403, 'Unauthorized access');
            }

            $hasilKuantitatif = $penilaian->hasilPenilaian
                ->filter(fn($h) => $h->criterion->category === 'Kuantitatif' && $h->criterion->status == 1);

            $hasilKompetensi = $penilaian->hasilPenilaian
                ->filter(fn($h) => $h->criterion->category === 'Kompetensi' && $h->criterion->status == 1);

            $skorAkhir = $penilaian->skor_akhir;
            $kategoriSkor = $this->getKategoriSkor($skorAkhir);
            $penilai = $penilaian->allocation->penilai;
            $catatan = $penilaian->catatan;
            $siklusNama = $penilaian->allocation->siklus->nama ?? 'Unknown';

            return view('staff.riwayat.show', compact(
                'penilaian',
                'hasilKuantitatif',
                'hasilKompetensi',
                'skorAkhir',
                'kategoriSkor',
                'penilai',
                'catatan',
                'siklusNama'
            ));

        } catch (\Exception $e) {
            Log::error("Riwayat Show Error: " . $e->getMessage());
            return back()->with('error', 'Gagal memuat detail riwayat.');
        }
    }

    private function getKategoriSkor($skor)
    {
        if ($skor >= 4.0) {
            return [
                'label' => 'Bagus',
                'bg' => 'bg-green-100',
                'text' => 'text-green-800',
            ];
        } elseif ($skor >= 3.0) {
            return [
                'label' => 'Normal',
                'bg' => 'bg-blue-100',
                'text' => 'text-blue-800',
            ];
        } else {
            return [
                'label' => 'Rendah',
                'bg' => 'bg-red-100',
                'text' => 'text-red-800',
            ];
        }
    }
}