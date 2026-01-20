<?php

namespace App\Http\Controllers\Hrd;

use App\Http\Controllers\Controller;
use App\Models\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        // Ambil log dengan relasi ke allocation → penilaian → siklus
        $logs = LogActivity::with([
            'user', // penilai
            'allocation.dinilai', // yang dinilai
            'allocation.siklus'
        ])
            ->whereHas('allocation.siklus', function ($q) {
                $q->where('status', 'active'); // hanya siklus aktif
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10); // 10 per halaman

        return view('hrd.monitoring.index', compact('logs'));
    }
}