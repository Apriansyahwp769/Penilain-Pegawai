<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SiklusController;
use App\Http\Controllers\Admin\CriteriaController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AllocationController;
use App\Http\Controllers\Admin\MonitoringController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\KetuaDivisi\DashboardController as KetuaDivisiDashboardController;
use App\Http\Controllers\KetuaDivisi\PenilaianController;
use App\Http\Controllers\KetuaDivisi\RiwayatController;
use App\Http\Controllers\KetuaDivisi\ProfileController;
use App\Http\Controllers\Staff\HasilPenilaianController;
use App\Http\Controllers\Hrd\DashboardController as HrdDashboardController;
use App\Http\Controllers\Hrd\VerifikasiController;
use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

// Redirect root ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// AUTH ROUTES - Tanpa middleware guest, biar controller yang handle
Route::get('/auth/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/auth/login', [LoginController::class, 'login'])->name('login.process');

// Logout
Route::post('/auth/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ADMIN ROUTES (Only Admin)
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {

    // Dashboard dengan Controller (UPDATED)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Siklus Routes
    Route::resource('siklus', SiklusController::class);

    // Criteria Routes
    Route::resource('criteria', CriteriaController::class);

    // Users Routes
    Route::get('users/{user}/json', [UserController::class, 'json'])->name('users.json');
    Route::resource('users', UserController::class);

    // Allocations Routes
    Route::resource('allocations', AllocationController::class);

    // Monitoring Routes
    Route::get('monitoring', [MonitoringController::class, 'index'])->name('monitoring.index');
    Route::get('monitoring/{id}', [MonitoringController::class, 'show'])->name('monitoring.show');
    Route::put('monitoring/{id}/status', [MonitoringController::class, 'updateStatus'])->name('monitoring.updateStatus');

    // Laporan Routes
    Route::get('laporan', [ReportController::class, 'index'])->name('laporan.index');
    Route::get('laporan/pdf', [ReportController::class, 'exportPdf'])->name('laporan.pdf');
});

// KETUA DIVISI ROUTES (Only Ketua Divisi)
Route::prefix('ketua-divisi')->name('ketua-divisi.')->middleware(['auth', 'role:ketua_divisi'])->group(function () {

    // Dashboard dengan Controller
    Route::get('/dashboard', [KetuaDivisiDashboardController::class, 'index'])->name('dashboard');

    // Penilaian Routes
    Route::get('/penilaian', [PenilaianController::class, 'index'])->name('penilaian.index');
    Route::get('/penilaian/{allocation}/create', [PenilaianController::class, 'create'])->name('penilaian.create');
    Route::post('/penilaian/{allocation}/store', [PenilaianController::class, 'store'])->name('penilaian.store');
    Route::get('/penilaian/{penilaian}/show', [PenilaianController::class, 'show'])->name('penilaian.show');

    Route::get('/penilaian/file/{hasilPenilaian}', [PenilaianController::class, 'downloadFile'])
        ->name('penilaian.download-file');

    // Riwayat Routes
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// STAFF ROUTES (Only Staff)
Route::prefix('staff')->name('staff.')->middleware(['auth', 'role:staff'])->group(function () {

    // Hasil Penilaian
    Route::get('/hasil-penilaian', [HasilPenilaianController::class, 'index'])->name('hasil_penilaian.index');

    // Riwayat
    Route::get('/riwayat', [App\Http\Controllers\Staff\RiwayatController::class, 'index'])->name('riwayat.index');
    Route::get('/riwayat/{id}', [App\Http\Controllers\Staff\RiwayatController::class, 'show'])->name('riwayat.show');

    // Profile
    Route::get('/profile', [App\Http\Controllers\Staff\ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [App\Http\Controllers\Staff\ProfileController::class, 'update'])->name('profile.update');

});

// HRD ROUTES (Only HRD)
Route::prefix('hrd')
    ->name('hrd.')
    ->middleware(['auth', 'role:ketua_divisi', 'hrd'])
    ->group(function () {

        Route::get('/dashboard', [HrdDashboardController::class, 'index'])
            ->name('dashboard');

       Route::get('/verifikasi', [VerifikasiController::class, 'index'])->name('verifikasi.index');
        Route::get('/verifikasi/{penilaian}', [VerifikasiController::class, 'show'])->name('verifikasi.show');
        Route::post('/verifikasi/{penilaian}/verify', [VerifikasiController::class, 'verify'])->name('verifikasi.verify');

        // Monitoring
        Route::get('/monitoring', [\App\Http\Controllers\Hrd\MonitoringController::class, 'index'])
            ->name('monitoring.index');

        // Profile
        Route::get('/profile', [\App\Http\Controllers\Hrd\ProfileController::class, 'index'])
            ->name('profile.index');
        Route::put('/profile', [\App\Http\Controllers\Hrd\ProfileController::class, 'update'])
            ->name('profile.update');
    });