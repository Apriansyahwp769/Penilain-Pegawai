<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penilaian;
use App\Models\Siklus;
use App\Models\Division;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Ambil list siklus dan divisi untuk filter
            $siklusList = Siklus::orderBy('tanggal_mulai', 'DESC')->get();
            $divisionsList = Division::where('status', true)->orderBy('name')->get();

            // Query SEMUA hasil penilaian (dari tabel hasil_penilaian)
            $hasilPenilaianList = DB::table('hasil_penilaian')
                ->join('penilaian', 'hasil_penilaian.penilaian_id', '=', 'penilaian.id')
                ->join('allocations', 'penilaian.allocation_id', '=', 'allocations.id')
                ->join('siklus', 'allocations.siklus_id', '=', 'siklus.id')
                ->join('users', 'allocations.dinilai_id', '=', 'users.id')
                ->join('divisions', 'users.division_id', '=', 'divisions.id')
                ->where('penilaian.status', 'selesai')
                ->select(
                    'hasil_penilaian.skor',
                    'siklus.id as siklus_id',
                    'siklus.nama as siklus_nama',
                    'divisions.id as division_id',
                    'divisions.name as division_nama',
                    'penilaian.skor_akhir'
                )
                ->get();

            // Transform data untuk JavaScript
            $allData = $hasilPenilaianList->map(function ($item) {
                return [
                    'skor' => (int)$item->skor, // Skor kriteria (1-5)
                    'siklus_id' => $item->siklus_id,
                    'siklus_nama' => $item->siklus_nama,
                    'division_id' => $item->division_id,
                    'division_nama' => $item->division_nama,
                    'skor_akhir' => $item->skor_akhir, // Untuk KPI dan tabel divisi
                ];
            });

            return view('admin.laporan.index', compact(
                'siklusList',
                'divisionsList',
                'allData'
            ));
        } catch (\Exception $e) {
            Log::error("Report Index Error: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());

            return view('admin.laporan.index', [
                'siklusList' => Siklus::orderBy('tanggal_mulai', 'DESC')->get(),
                'divisionsList' => Division::where('status', true)->orderBy('name')->get(),
                'allData' => collect()
            ])->with('error', 'Gagal memuat data laporan: ' . $e->getMessage());
        }
    }

    /**
     * Export ke PDF
     */
    public function exportPdf(Request $request)
    {
        try {
            // Ambil filter opsional dari query string
            $siklusId = $request->query('siklus');
            $divisiId = $request->query('divisi');

            // Ambil data menggunakan query yang sama seperti di index()
            $query = DB::table('hasil_penilaian')
                ->join('penilaian', 'hasil_penilaian.penilaian_id', '=', 'penilaian.id')
                ->join('allocations', 'penilaian.allocation_id', '=', 'allocations.id')
                ->join('siklus', 'allocations.siklus_id', '=', 'siklus.id')
                ->join('users', 'allocations.dinilai_id', '=', 'users.id')
                ->join('divisions', 'users.division_id', '=', 'divisions.id')
                ->where('penilaian.status', 'selesai');

            // Apply filter jika ada
            if ($siklusId) {
                $query->where('siklus.id', $siklusId);
            }

            if ($divisiId) {
                $query->where('divisions.id', $divisiId);
            }

            $hasilPenilaianList = $query->select(
                'hasil_penilaian.skor',
                'siklus.id as siklus_id',
                'siklus.nama as siklus_nama',
                'divisions.id as division_id',
                'divisions.name as division_nama',
                'penilaian.skor_akhir',
                'users.name as pegawai_nama',
                'users.nip as pegawai_nip'
            )->get();

            // Transform data
            $allData = $hasilPenilaianList->map(function ($item) {
                return [
                    'skor' => (int)$item->skor,
                    'siklus_id' => $item->siklus_id,
                    'siklus_nama' => $item->siklus_nama,
                    'division_id' => $item->division_id,
                    'division_nama' => $item->division_nama,
                    'skor_akhir' => $item->skor_akhir,
                    'pegawai_nama' => $item->pegawai_nama,
                    'pegawai_nip' => $item->pegawai_nip,
                ];
            });

            // Hitung statistik untuk PDF
            $uniquePenilaian = [];
            foreach ($allData as $item) {
                $key = $item['siklus_id'] . '_' . $item['division_id'] . '_' . $item['skor_akhir'];
                if (!isset($uniquePenilaian[$key])) {
                    $uniquePenilaian[$key] = $item;
                }
            }

            $totalPegawai = count($uniquePenilaian);
            $avgSkor = $totalPegawai > 0 
                ? round(array_sum(array_column($uniquePenilaian, 'skor_akhir')) / $totalPegawai, 1) 
                : 0;

            // Group by division
            $divisiStats = [];
            foreach ($uniquePenilaian as $item) {
                $divId = $item['division_id'];
                if (!isset($divisiStats[$divId])) {
                    $divisiStats[$divId] = [
                        'nama' => $item['division_nama'],
                        'count' => 0,
                        'total_skor' => 0,
                    ];
                }
                $divisiStats[$divId]['count']++;
                $divisiStats[$divId]['total_skor'] += $item['skor_akhir'];
            }

            // Calculate averages and sort
            $divisiArray = [];
            foreach ($divisiStats as $stat) {
                $avg = $stat['count'] > 0 ? round($stat['total_skor'] / $stat['count'], 1) : 0;
                $divisiArray[] = [
                    'nama' => $stat['nama'],
                    'count' => $stat['count'],
                    'avg' => $avg,
                ];
            }

            // Sort by average (highest first)
            usort($divisiArray, function($a, $b) {
                return $b['avg'] <=> $a['avg'];
            });

            // Get siklus and divisi names for display
            $siklusName = 'Semua Siklus';
            $divisiName = 'Semua Divisi';

            if ($siklusId) {
                $siklus = Siklus::find($siklusId);
                $siklusName = $siklus ? $siklus->nama : 'Siklus Tidak Ditemukan';
            }

            if ($divisiId) {
                $divisi = Division::find($divisiId);
                $divisiName = $divisi ? $divisi->name : 'Divisi Tidak Ditemukan';
            }

            // Prepare data untuk PDF
            $pdfData = [
                'allData' => $allData,
                'divisiArray' => $divisiArray,
                'totalPegawai' => $totalPegawai,
                'avgSkor' => $avgSkor,
                'siklusName' => $siklusName,
                'divisiName' => $divisiName,
                'appliedFilters' => [
                    'siklus' => $siklusId,
                    'divisi' => $divisiId,
                ],
                'generatedAt' => now()->format('d M Y H:i'),
            ];

            // Generate PDF
            $pdf = Pdf::loadView('admin.laporan.pdf', $pdfData)
                ->setPaper('a4', 'potrait');

            $filename = 'Laporan-Penilaian-' . now()->format('Y-m-d-His') . '.pdf';

            return $pdf->download($filename);

        } catch (\Exception $e) {
            Log::error("Export PDF Error: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            
            return back()->with('error', 'Gagal export PDF: ' . $e->getMessage());
        }
    }
}