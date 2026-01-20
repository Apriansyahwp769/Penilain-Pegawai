{{-- resources/views/hrd/dashboard.blade.php --}}
@extends('layouts.hrd')

@section('page-title', 'Dashboard HRD')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- Welcome Header -->
    <div class="mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-gray-900">👋 Selamat Datang, {{ Auth::user()->name }}</h1>
        <p class="text-gray-600 text-sm lg:text-base">Dashboard Monitoring & Verifikasi Penilaian Kinerja</p>
    </div>

    @if(!$siklusAktif)
        <!-- Alert jika tidak ada Siklus aktif -->
        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        Tidak ada siklus aktif. Silakan hubungi Admin untuk mengaktifkan siklus penilaian.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
        
        <!-- Total Penilaian Menunggu Verifikasi -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-white bg-opacity-30 rounded-lg">
                    <i data-lucide="clock" class="w-6 h-6"></i>
                </div>
                <span class="text-3xl font-bold">{{ $menungguVerifikasi }}</span>
            </div>
            <h3 class="text-sm font-medium opacity-90">Menunggu Verifikasi</h3>
            <p class="text-xs opacity-75 mt-1">Perlu segera ditindak lanjuti</p>
        </div>

        <!-- Total Sudah Selesai -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-white bg-opacity-30 rounded-lg">
                    <i data-lucide="check-circle" class="w-6 h-6"></i>
                </div>
                <span class="text-3xl font-bold">{{ $selesai }}</span>
            </div>
            <h3 class="text-sm font-medium opacity-90">Penilaian Selesai</h3>
            <p class="text-xs opacity-75 mt-1">Sudah terverifikasi</p>
        </div>

        <!-- Total Belum Dinilai -->
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-white bg-opacity-30 rounded-lg">
                    <i data-lucide="alert-circle" class="w-6 h-6"></i>
                </div>
                <span class="text-3xl font-bold">{{ $belumDinilai }}</span>
            </div>
            <h3 class="text-sm font-medium opacity-90">Belum Dinilai</h3>
            <p class="text-xs opacity-75 mt-1">Menunggu ketua divisi</p>
        </div>

        <!-- Total Staff Aktif -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-white bg-opacity-30 rounded-lg">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
                <span class="text-3xl font-bold">{{ $totalStaff }}</span>
            </div>
            <h3 class="text-sm font-medium opacity-90">Total Staff</h3>
            <p class="text-xs opacity-75 mt-1">Dalam siklus aktif</p>
        </div>

    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        
        <!-- Left Column (2/3) - Penilaian Menunggu Verifikasi -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                            <i data-lucide="clipboard-list" class="w-5 h-5 text-orange-600 mr-2"></i>
                            Penilaian Menunggu Verifikasi
                        </h2>
                        <p class="text-sm text-gray-500 mt-1">Segera verifikasi penilaian berikut</p>
                    </div>
                    <a href="{{ route('hrd.verifikasi.index') }}" 
                       class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center">
                        Lihat Semua
                        <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
                    </a>
                </div>

                <!-- List -->
                <div class="divide-y divide-gray-100">
                    @forelse($pendingVerifikasi as $item)
                    <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center mb-1">
                                    <h3 class="font-semibold text-gray-900 mr-2">{{ $item['name'] }}</h3>
                                    <span class="px-2 py-0.5 bg-orange-100 text-orange-800 text-xs font-medium rounded">
                                        Perlu Verifikasi
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600">{{ $item['position'] }} • {{ $item['division'] }}</p>
                                <div class="flex items-center mt-2 text-xs text-gray-500">
                                    <i data-lucide="user-check" class="w-3.5 h-3.5 mr-1"></i>
                                    Dinilai oleh: {{ $item['penilai'] }}
                                    <span class="mx-2">•</span>
                                    <i data-lucide="calendar" class="w-3.5 h-3.5 mr-1"></i>
                                    {{ $item['tanggal'] }}
                                </div>
                            </div>
                            <div class="ml-4">
                                <a href="{{ route('hrd.verifikasi.show', $item['penilaian_id']) }}" 
                                   class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                    <i data-lucide="eye" class="w-4 h-4 mr-1"></i>
                                    Lihat
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="px-6 py-12 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                            <i data-lucide="check-circle-2" class="w-8 h-8 text-gray-400"></i>
                        </div>
                        <p class="text-gray-500 font-medium">Tidak ada penilaian yang menunggu verifikasi</p>
                        <p class="text-sm text-gray-400 mt-1">Semua penilaian sudah terverifikasi</p>
                    </div>
                    @endforelse
                </div>

            </div>
        </div>

        <!-- Right Column (1/3) - Progress & Quick Stats -->
        <div class="space-y-6">
            
            <!-- Progress Penilaian -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i data-lucide="pie-chart" class="w-5 h-5 text-blue-600 mr-2"></i>
                    Progress Penilaian
                </h2>
                
                <div class="space-y-4">
                    <!-- Selesai -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Selesai</span>
                            <span class="text-sm font-bold text-green-600">{{ $progressSelesai }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-green-500 h-2.5 rounded-full" style="width: {{ $progressSelesai }}%"></div>
                        </div>
                    </div>

                    <!-- Menunggu Verifikasi -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Menunggu Verifikasi</span>
                            <span class="text-sm font-bold text-orange-600">{{ $progressMenunggu }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-orange-500 h-2.5 rounded-full" style="width: {{ $progressMenunggu }}%"></div>
                        </div>
                    </div>

                    <!-- Belum Dinilai -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Belum Dinilai</span>
                            <span class="text-sm font-bold text-red-600">{{ $progressBelum }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-red-500 h-2.5 rounded-full" style="width: {{ $progressBelum }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Siklus Aktif -->
            @if($siklusAktif)
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <i data-lucide="calendar-check" class="w-8 h-8 opacity-80"></i>
                    <span class="px-2.5 py-1 bg-white bg-opacity-30 text-xs font-semibold rounded-full">
                        AKTIF
                    </span>
                </div>
                <h3 class="text-lg font-semibold mb-1">{{ $siklusAktif->nama }}</h3>
                <p class="text-sm opacity-90">
                    {{ $startDate }} - {{ $endDate }}
                </p>
                <div class="mt-4 pt-4 border-t border-white border-opacity-30">
                    <p class="text-xs opacity-75">Sisa Waktu</p>
                    <p class="text-sm font-semibold">
                        @if($sisaHari > 0)
                            {{ $sisaHari }} hari lagi
                        @elseif($sisaHari == 0)
                            Hari ini adalah deadline!
                        @else
                            Melewati deadline {{ abs($sisaHari) }} hari
                        @endif
                    </p>
                </div>
            </div>
            @else
            <div class="bg-gradient-to-br from-gray-400 to-gray-500 rounded-xl p-6 text-white shadow-lg">
                <div class="text-center py-4">
                    <i data-lucide="calendar-x" class="w-12 h-12 opacity-50 mx-auto mb-3"></i>
                    <h3 class="text-lg font-semibold mb-1">Tidak Ada Siklus Aktif</h3>
                    <p class="text-sm opacity-75">Belum ada siklus penilaian yang aktif</p>
                </div>
            </div>
            @endif

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <i data-lucide="zap" class="w-5 h-5 text-blue-600 mr-2"></i>
                    Quick Actions
                </h2>
                <div class="space-y-2">
                    <a href="{{ route('hrd.verifikasi.index') }}" 
                       class="flex items-center justify-between px-4 py-3 bg-orange-50 text-orange-700 rounded-lg hover:bg-orange-100 transition-colors group">
                        <span class="text-sm font-medium">Verifikasi Penilaian</span>
                        <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                    <a href="{{ route('hrd.monitoring.index') }}" 
                       class="flex items-center justify-between px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors group">
                        <span class="text-sm font-medium">Monitoring Aktivitas</span>
                        <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>

        </div>

    </div>

    <!-- Aktivitas Terbaru -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
        
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i data-lucide="activity" class="w-5 h-5 text-blue-600 mr-2"></i>
                    Aktivitas Terbaru
                </h2>
                <p class="text-sm text-gray-500 mt-1">Log aktivitas penilaian dalam sistem</p>
            </div>
            <a href="{{ route('hrd.monitoring.index') }}" 
               class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center">
                Lihat Semua
                <i data-lucide="arrow-right" class="w-4 h-4 ml-1"></i>
            </a>
        </div>

        <!-- Timeline -->
        <div class="px-6 py-4">
            @forelse($recentActivities as $log)
            <div class="flex items-start space-x-4 pb-4 mb-4 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                <!-- Icon -->
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                        <i data-lucide="file-text" class="w-5 h-5 text-blue-600"></i>
                    </div>
                </div>
                
                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-900">
                        <span class="font-semibold">{{ $log->user->name }}</span>
                        {{ $log->action }}
                        <span class="font-semibold">{{ $log->allocation->dinilai->name }}</span>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $log->created_at->diffForHumans() }}
                    </p>
                </div>

                <!-- Badge -->
                <div class="flex-shrink-0">
                    <span class="px-2.5 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded">
                        {{ $log->allocation->siklus->nama }}
                    </span>
                </div>
            </div>
            @empty
            <div class="text-center py-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                    <i data-lucide="inbox" class="w-8 h-8 text-gray-400"></i>
                </div>
                <p class="text-gray-500 font-medium">Belum ada aktivitas</p>
                <p class="text-sm text-gray-400 mt-1">Aktivitas penilaian akan muncul di sini</p>
            </div>
            @endforelse
        </div>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>

@endsection