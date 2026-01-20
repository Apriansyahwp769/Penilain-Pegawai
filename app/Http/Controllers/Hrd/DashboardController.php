<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\Allocation;
use App\Models\LogActivity;
use App\Models\Penilaian;
use App\Models\Siklus;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            // Ambil siklus aktif
            $siklusAktif = Siklus::where('status', 'active')->first();

            // Default values
            $menungguVerifikasi = 0;
            $selesai = 0;
            $belumDinilai = 0;
            $totalStaff = 0;
            $progressSelesai = 0;
            $progressMenunggu = 0;
            $progressBelum = 0;
            $sisaHari = 0;
            $startDate = '-';
            $endDate = '-';
            $pendingVerifikasi = collect();
            $recentActivities = collect();

            if ($siklusAktif) {

                // === FORMAT TANGGAL (AMAN seperti Admin) ===
                if ($siklusAktif->start_date) {
                    $startDate = Carbon::parse($siklusAktif->start_date)->format('d M Y');
                }

                if ($siklusAktif->end_date) {
                    $endDate = Carbon::parse($siklusAktif->end_date)->format('d M Y');

                    $today = Carbon::today();
                    $deadline = Carbon::parse($siklusAktif->end_date)->startOfDay();
                    $sisaHari = (int) $today->diffInDays($deadline, false);
                }

                // === STATS CARDS ===
                $menungguVerifikasi = Penilaian::whereHas('allocation', function($q) use ($siklusAktif) {
                    $q->where('siklus_id', $siklusAktif->id);
                })
                ->where('status', 'menunggu_verifikasi')
                ->count();

                $selesai = Penilaian::whereHas('allocation', function($q) use ($siklusAktif) {
                    $q->where('siklus_id', $siklusAktif->id);
                })
                ->where('status', 'selesai')
                ->count();

                $belumDinilai = Allocation::where('siklus_id', $siklusAktif->id)
                    ->doesntHave('penilaian')
                    ->count();

                $totalStaff = Allocation::where('siklus_id', $siklusAktif->id)->count();

                // === PROGRESS ===
                $progressSelesai = $totalStaff > 0 ? round(($selesai / $totalStaff) * 100, 1) : 0;
                $progressMenunggu = $totalStaff > 0 ? round(($menungguVerifikasi / $totalStaff) * 100, 1) : 0;
                $progressBelum = $totalStaff > 0 ? round(($belumDinilai / $totalStaff) * 100, 1) : 0;

                // === PENDING VERIFIKASI ===
                $pendingVerifikasi = Penilaian::with([
                    'allocation.dinilai.position',
                    'allocation.dinilai.division',
                    'allocation.penilai'
                ])
                ->whereHas('allocation', function($q) use ($siklusAktif) {
                    $q->where('siklus_id', $siklusAktif->id);
                })
                ->where('status', 'menunggu_verifikasi')
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function($penilaian) {
                    return [
                        'penilaian_id' => $penilaian->id,
                        'name' => $penilaian->allocation->dinilai->name ?? '-',
                        'position' => $penilaian->allocation->dinilai->position->name ?? '-',
                        'division' => $penilaian->allocation->dinilai->division->name ?? '-',
                        'penilai' => $penilaian->allocation->penilai->name ?? '-',
                        'tanggal' => $penilaian->updated_at->format('d M Y, H:i'),
                    ];
                });

                // === RECENT ACTIVITIES ===
                $recentActivities = LogActivity::with([
                    'user',
                    'allocation.dinilai',
                    'allocation.siklus'
                ])
                ->whereHas('allocation', function($q) use ($siklusAktif) {
                    $q->where('siklus_id', $siklusAktif->id);
                })
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            }

            return view('hrd.dashboard', compact(
                'menungguVerifikasi',
                'selesai',
                'belumDinilai',
                'totalStaff',
                'progressSelesai',
                'progressMenunggu',
                'progressBelum',
                'siklusAktif',
                'sisaHari',
                'startDate',
                'endDate',
                'pendingVerifikasi',
                'recentActivities'
            ));

        } catch (\Exception $e) {
            Log::error("HRD Dashboard Error: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());

            return view('hrd.dashboard', [
                'menungguVerifikasi' => 0,
                'selesai' => 0,
                'belumDinilai' => 0,
                'totalStaff' => 0,
                'progressSelesai' => 0,
                'progressMenunggu' => 0,
                'progressBelum' => 0,
                'siklusAktif' => null,
                'sisaHari' => 0,
                'startDate' => '-',
                'endDate' => '-',
                'pendingVerifikasi' => collect(),
                'recentActivities' => collect(),
            ])->with('error', 'Gagal memuat data dashboard.');
        }
    }
}
