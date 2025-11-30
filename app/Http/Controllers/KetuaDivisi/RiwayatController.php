<?php

namespace App\Http\Controllers\KetuaDivisi;

use App\Http\Controllers\Controller;
use App\Models\Penilaian;
use App\Models\Siklus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RiwayatController extends Controller
{
    /**
     * Display riwayat penilaian untuk ketua divisi yang login
     */
    public function index(Request $request)
    {
        try {
            $userId = Auth::id();

            // Ambil semua siklus untuk filter dropdown
            $allSiklus = Siklus::orderBy('tanggal_mulai', 'DESC')->get();

            // Query penilaian yang sudah selesai
            $query = Penilaian::with([
                'allocation.dinilai.position',
                'allocation.dinilai.division',
                'allocation.siklus'
            ])
            ->whereHas('allocation', function($q) use ($userId) {
                $q->where('penilai_id', $userId);
            })
            ->where('status', 'selesai');

            // Filter berdasarkan siklus jika dipilih
            if ($request->filled('siklus_id')) {
                $query->whereHas('allocation', function($q) use ($request) {
                    $q->where('siklus_id', $request->siklus_id);
                });
            }

            // Filter berdasarkan search query
            if ($request->filled('search')) {
                $search = strtolower($request->search);
                $query->whereHas('allocation.dinilai', function($q) use ($search) {
                    $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                })->orWhereHas('allocation.dinilai.position', function($q) use ($search) {
                    $q->whereRaw('LOWER(name) LIKE ?', ["%{$search}%"]);
                });
            }

            $riwayatList = $query->orderBy('updated_at', 'DESC')->get();

            // Transform data untuk view
            $riwayat = $riwayatList->map(function ($penilaian) {
                return [
                    'penilaian_id' => $penilaian->id,
                    'nip' => $penilaian->allocation->dinilai->nip ?? '-',
                    'name' => $penilaian->allocation->dinilai->name ?? '-',
                    'position' => $penilaian->allocation->dinilai->position->name ?? '-',
                    'division' => $penilaian->allocation->dinilai->division->name ?? '-',
                    'siklus_id' => $penilaian->allocation->siklus->id ?? null,
                    'siklus_nama' => $penilaian->allocation->siklus->nama ?? '-',
                    'skor_akhir' => $penilaian->skor_akhir,
                    'status' => $penilaian->status,
                    'status_badge' => $penilaian->status_badge,
                    'status_label' => $penilaian->status_label,
                    'tanggal_dinilai' => $penilaian->updated_at->format('d M Y'),
                ];
            });

            return view('ketua-divisi.riwayat.index', compact('riwayat', 'allSiklus'));

        } catch (\Exception $e) {
            Log::error("Riwayat Index Error: " . $e->getMessage());
            return back()->with('error', 'Gagal memuat data riwayat.');
        }
    }
}