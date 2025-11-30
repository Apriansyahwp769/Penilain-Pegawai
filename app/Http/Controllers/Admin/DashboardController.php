<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siklus;
use App\Models\Allocation;
use App\Models\Penilaian;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Ambil siklus aktif
            $siklusAktif = Siklus::where('status', 'active')->first();

            // Default values jika tidak ada siklus aktif
            $siklusNama = 'Tidak ada siklus aktif';
            $sisaHari = 0;
            $deadlineDate = '-';
            $totalPenilaian = 0;
            $penilaianSelesai = 0;
            $persentaseSelesai = 0;
            $divisiStats = collect();

            if ($siklusAktif) {
                // 1. Data Siklus Aktif & Deadline
                $siklusNama = $siklusAktif->nama;
                
                $today = Carbon::today();
                $deadline = Carbon::parse($siklusAktif->tanggal_finalisasi)->startOfDay();
                $sisaHari = (int) $today->diffInDays($deadline, false);
                $deadlineDate = $deadline->format('d M Y');

                // 2. Total Penilaian & Penilaian Selesai
                $totalPenilaian = Allocation::where('siklus_id', $siklusAktif->id)->count();
                
                $penilaianSelesai = Penilaian::whereHas('allocation', function($q) use ($siklusAktif) {
                    $q->where('siklus_id', $siklusAktif->id);
                })
                ->where('status', 'selesai')
                ->count();

                $persentaseSelesai = $totalPenilaian > 0 
                    ? round(($penilaianSelesai / $totalPenilaian) * 100) 
                    : 0;

                // 3. Status per Divisi (Ketua Divisi)
                $ketuaDivisiList = User::where('role', 'ketua_divisi')
                    ->where('is_active', 1)
                    ->with('division')
                    ->get();

                $divisiStats = $ketuaDivisiList->map(function($ketua) use ($siklusAktif) {
                    // Total alokasi untuk ketua divisi ini
                    $totalAlokasi = Allocation::where('penilai_id', $ketua->id)
                        ->where('siklus_id', $siklusAktif->id)
                        ->count();

                    // Total penilaian selesai
                    $totalSelesai = Penilaian::whereHas('allocation', function($q) use ($ketua, $siklusAktif) {
                        $q->where('penilai_id', $ketua->id)
                          ->where('siklus_id', $siklusAktif->id);
                    })
                    ->where('status', 'selesai')
                    ->count();

                    // Hitung persentase
                    $persentase = $totalAlokasi > 0 
                        ? round(($totalSelesai / $totalAlokasi) * 100) 
                        : 0;

                    // Tentukan status badge
                    if ($persentase == 100) {
                        $status = 'Selesai';
                        $statusBadge = 'bg-green-100 text-green-800';
                        $showWarning = false;
                    } elseif ($persentase < 30) {
                        $status = 'Peringatan';
                        $statusBadge = 'bg-red-100 text-red-800';
                        $showWarning = true;
                    } else {
                        $status = 'Progress';
                        $statusBadge = 'bg-yellow-100 text-yellow-800';
                        $showWarning = false;
                    }

                    return [
                        'ketua_nama' => $ketua->name,
                        'divisi_nama' => $ketua->division->name ?? '-',
                        'total_bawahan' => $totalAlokasi,
                        'total_selesai' => $totalSelesai,
                        'persentase' => $persentase,
                        'status' => $status,
                        'status_badge' => $statusBadge,
                        'show_warning' => $showWarning,
                    ];
                });
            }

            return view('admin.dashboard', compact(
                'siklusNama',
                'sisaHari',
                'deadlineDate',
                'totalPenilaian',
                'penilaianSelesai',
                'persentaseSelesai',
                'divisiStats',
                'siklusAktif'
            ));

        } catch (\Exception $e) {
            Log::error("Admin Dashboard Error: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            
            return view('admin.dashboard', [
                'siklusNama' => 'Error',
                'sisaHari' => 0,
                'deadlineDate' => '-',
                'totalPenilaian' => 0,
                'penilaianSelesai' => 0,
                'persentaseSelesai' => 0,
                'divisiStats' => collect(),
                'siklusAktif' => null
            ])->with('error', 'Gagal memuat data dashboard.');
        }
    }
}