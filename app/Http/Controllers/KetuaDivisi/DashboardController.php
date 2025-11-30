<?php

namespace App\Http\Controllers\KetuaDivisi;

use App\Http\Controllers\Controller;
use App\Models\Allocation;
use App\Models\Penilaian;
use App\Models\Siklus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display dashboard untuk ketua divisi yang login
     */
    public function index()
    {
        try {
            $userId = Auth::id();

            // Ambil siklus aktif
            $siklusAktif = Siklus::where('status', 'active')->first();

            // Default values jika tidak ada siklus aktif
            $pegawaiBelumDinilai = 0;
            $sisaHari = 0;
            $deadlineDate = '-';
            $totalPegawai = 0;
            $selesaiDinilai = 0;
            $persentaseSelesai = 0;
            $skorRataRata = 0;

            if ($siklusAktif) {
                // 1. Hitung Pegawai Belum Dinilai
                $pegawaiBelumDinilai = Allocation::where('penilai_id', $userId)
                    ->where('siklus_id', $siklusAktif->id)
                    ->whereDoesntHave('penilaian', function($q) {
                        $q->where('status', 'selesai');
                    })
                    ->count();

                // 2. Hitung Sisa Hari Deadline (dari tanggal_finalisasi)
                if ($siklusAktif->tanggal_finalisasi) {
                    $today = Carbon::today();
                    $deadline = Carbon::parse($siklusAktif->tanggal_finalisasi);
                    $sisaHari = $today->diffInDays($deadline, false); // false = bisa negatif jika lewat deadline
                    $deadlineDate = $deadline->format('d F Y');
                }

                // 3. Hitung Progres Tim
                $totalPegawai = Allocation::where('penilai_id', $userId)
                    ->where('siklus_id', $siklusAktif->id)
                    ->count();

                $selesaiDinilai = Allocation::where('penilai_id', $userId)
                    ->where('siklus_id', $siklusAktif->id)
                    ->whereHas('penilaian', function($q) {
                        $q->where('status', 'selesai');
                    })
                    ->count();

                $persentaseSelesai = $totalPegawai > 0 ? round(($selesaiDinilai / $totalPegawai) * 100) : 0;

                // 4. Hitung Skor Rata-rata Tim (dari penilaian yang selesai)
                $skorRataRata = Penilaian::whereHas('allocation', function($q) use ($userId, $siklusAktif) {
                        $q->where('penilai_id', $userId)
                          ->where('siklus_id', $siklusAktif->id);
                    })
                    ->where('status', 'selesai')
                    ->avg('skor_akhir');

                $skorRataRata = $skorRataRata ? round($skorRataRata, 1) : 0;
            }

            $belumDinilai = $totalPegawai - $selesaiDinilai;

            return view('ketua-divisi.dashboard', compact(
                'pegawaiBelumDinilai',
                'sisaHari',
                'deadlineDate',
                'totalPegawai',
                'selesaiDinilai',
                'belumDinilai',
                'persentaseSelesai',
                'skorRataRata',
                'siklusAktif'
            ));

        } catch (\Exception $e) {
            Log::error("Dashboard Ketua Divisi Error: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            
            return view('ketua-divisi.dashboard', [
                'pegawaiBelumDinilai' => 0,
                'sisaHari' => 0,
                'deadlineDate' => '-',
                'totalPegawai' => 0,
                'selesaiDinilai' => 0,
                'belumDinilai' => 0,
                'persentaseSelesai' => 0,
                'skorRataRata' => 0,
                'siklusAktif' => null
            ]);
        }
    }
}