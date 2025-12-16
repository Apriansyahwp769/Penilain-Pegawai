{{-- resources/views/ketua-divisi/dashboard.blade.php --}}
@extends('layouts.ketua-divisi')

@section('page-title', 'Dashboard Ketua Divisi')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-gray-900">👋 Halo!! {{ auth()->user()->name }} (Kepala Divisi)</h1>
        <p class="text-gray-600 text-sm lg:text-base">Menampilkan summary tugas yang harus segera diselesaikan</p>
    </div>

    <!-- KPI Cards - 3 Columns -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

        <!-- Card 1: Pegawai Belum Dinilai -->
        @php
        $isHighPending = $pegawaiBelumDinilai > 3;
        $bgPending = $isHighPending ? 'bg-yellow-50' : 'bg-blue-50';
        $borderPending = $isHighPending ? 'border-yellow-500' : 'border-blue-500';
        $iconPending = $isHighPending ? 'text-yellow-600' : 'text-blue-600';
        $textPending = $isHighPending ? 'text-yellow-800' : 'text-blue-800';
        $valuePending = $isHighPending ? 'text-yellow-700' : 'text-blue-700';
        @endphp
        <div class="{{ $bgPending }} border-l-4 {{ $borderPending }} rounded-xl p-6 shadow-lg">
            <div class="flex items-center mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ $iconPending }} mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 17h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-sm font-medium {{ $textPending }}">Pegawai Belum Dinilai</h3>
            </div>
            <p class="text-3xl font-bold {{ $valuePending }} mt-1">{{ $pegawaiBelumDinilai }}</p>
            <p class="text-xs mt-1 text-gray-600">Status: Belum Dimulai</p>
            <!-- Progress bar -->
            <div class="mt-3 bg-gray-100 rounded-full h-2">
                <div class="bg-yellow-300 h-2 rounded-full" style="width: {{ $totalPegawai > 0 ? round(($pegawaiBelumDinilai / $totalPegawai) * 100) : 0 }}%;"></div>
            </div>
        </div>

        <!-- Card 2: Sisa Hari Deadline -->
        @php
        $isUrgentDeadline = $sisaHari <= 7;
            $bgDeadline=$isUrgentDeadline ? 'bg-red-50' : 'bg-blue-50' ;
            $borderDeadline=$isUrgentDeadline ? 'border-red-500' : 'border-blue-500' ;
            $iconDeadline=$isUrgentDeadline ? 'text-red-600' : 'text-blue-600' ;
            $textDeadline=$isUrgentDeadline ? 'text-red-800' : 'text-blue-800' ;
            $valueDeadline=$isUrgentDeadline ? 'text-red-700' : 'text-blue-700' ;
            $statusDeadline=$isUrgentDeadline ? 'text-red-600' : 'text-blue-600' ;
            @endphp
            <div class="{{ $bgDeadline }} border-l-4 {{ $borderDeadline }} rounded-xl p-6 shadow-lg">
            <div class="flex items-center mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ $iconDeadline }} mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-sm font-medium {{ $textDeadline }}">Sisa Hari Deadline</h3>
            </div>
            <p class="text-3xl font-bold {{ $valueDeadline }} mt-1">{{ $sisaHari > 0 ? $sisaHari : 0 }}</p>
            <p class="text-xs mt-1 text-gray-600">Deadline: {{ $deadlineDate }}</p>
            @if($sisaHari < 0)
                <p class="text-xs mt-1 text-red-600 font-semibold">⚠️ Deadline sudah terlewat!</p>
                @elseif($sisaHari <= 7)
                    <p class="text-xs mt-1 text-red-600">⏰ Segera selesaikan penilaian!</p>
                    @else
                    <p class="text-xs mt-1 {{ $statusDeadline }}">Masih aman – tersisa {{ $sisaHari }} hari</p>
                    @endif
    </div>

    <!-- Card 3: Mulai Penilaian Sekarang (tidak berubah) -->
    <div class="bg-blue-50 border-l-4 border-blue-500 rounded-xl p-6 shadow-lg">
        <div class="flex items-center mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6h12V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 002 2h2a2 2 0 002-2z" />
            </svg>
            <h3 class="text-sm font-medium text-blue-800">Mulai Penilaian Sekarang</h3>
        </div>
        <p class="text-xs text-gray-600 mb-4">Klik untuk memulai proses penilaian bawahan Anda</p>
        <a href="{{ route('ketua-divisi.penilaian.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-blue-500 text-blue-700 rounded-md hover:bg-blue-50 transition-colors duration-200">
            Mulai Penilaian →
        </a>
    </div>
</div>

<!-- Progres Tim & Skor Rata-rata -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Progres Tim -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-3">📊 Progres Tim</h2>
        <p class="text-sm text-gray-600 mb-4">Total Bawahan: {{ $totalPegawai }} pegawai</p>

        <!-- Progress Bar -->
        <div class="mb-4">
            <div class="w-full bg-gray-200 rounded-full h-6">
                <div class="bg-green-500 h-6 rounded-full flex items-center justify-center text-white font-medium text-xs" style="width: {{ $persentaseSelesai }}%;">
                    {{ $persentaseSelesai }}% Selesai ({{ $selesaiDinilai }} dari {{ $totalPegawai }})
                </div>
            </div>
        </div>

        <!-- Detail Progress -->
        <div class="space-y-2 text-sm">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="text-green-600">Selesai: {{ $selesaiDinilai }} pegawai</span>
            </div>
            @php
            $belumDinilaiWarna = $belumDinilai > 3 ? 'text-orange-600' : 'text-blue-600';
            @endphp
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 {{ $belumDinilaiWarna }} mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 17h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="{{ $belumDinilaiWarna }}">Belum Dinilai: {{ $belumDinilai }} pegawai</span>
            </div>
        </div>
    </div>

    <!-- Skor Rata-rata Tim -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-3">⭐ Skor Rata-rata Tim</h2>
        <p class="text-xs text-gray-500 mb-4">(dari periode penilaian saat ini)</p>

        <!-- Large Score Display -->
        <div class="flex items-end mb-4">
            <span class="text-4xl lg:text-5xl font-bold text-blue-600">{{ number_format($skorRataRata, 1) }}</span>
            <span class="ml-2 text-xl lg:text-2xl text-gray-500">/ 5.0</span>
        </div>

        <!-- Context Box -->
        <div class="bg-blue-50 p-3 rounded-lg">
            @if($skorRataRata > 0)
            @if($skorRataRata >= 4.0)
            <p class="text-xs text-blue-700">✅ Kinerja tim sangat baik! Pertahankan performa ini.</p>
            @elseif($skorRataRata >= 3.0)
            <p class="text-xs text-blue-700">📈 Kinerja tim cukup baik, masih ada ruang untuk perbaikan.</p>
            @else
            <p class="text-xs text-blue-700">⚠️ Perlu perhatian khusus untuk meningkatkan kinerja tim.</p>
            @endif
            @else
            <p class="text-xs text-blue-700">Belum ada data penilaian yang selesai untuk periode ini.</p>
            @endif
        </div>
    </div>
</div>

<!-- Quick Actions & Tips -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Quick Actions -->
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">⚡ Quick Actions - Akses Cepat</h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Lihat Daftar Tim -->
            <a href="{{ route('ketua-divisi.penilaian.index') }}" class="block p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors duration-200">
                <div class="flex items-center mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <span class="font-medium text-gray-900">Lihat Daftar Tim</span>
                </div>
                <p class="text-xs text-gray-600">Daftar lengkap pegawai bawahan</p>
            </a>

            <!-- Riwayat Kinerja Tim -->
            <a href="{{ route('ketua-divisi.riwayat.index') }}" class="block p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors duration-200">
                <div class="flex items-center mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="font-medium text-gray-900">Riwayat Kinerja Tim</span>
                </div>
                <p class="text-xs text-gray-600">Tren performa dari periode lalu</p>
            </a>

            <!-- Pengaturan Profile -->
            <a href="{{ route('ketua-divisi.profile.index') }}" class="block p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors duration-200">
                <div class="flex items-center mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="font-medium text-gray-900">Pengaturan Profile</span>
                </div>
                <p class="text-xs text-gray-600">Kelola profil dan preferensi</p>
            </a>
        </div>
    </div>

    <!-- Tips Produktivitas -->
    <div class="bg-blue-50 rounded-xl border border-blue-200 p-6">
        <h2 class="text-lg font-semibold text-blue-900 mb-4">💡 Tips Produktivitas</h2>
        <ul class="space-y-2 text-sm text-blue-800">
            <li class="flex items-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Prioritaskan penilaian pegawai dengan deadline terdekat</span>
            </li>
            <li class="flex items-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Gunakan fitur "Mulai Penilaian" untuk akses cepat ke tugas</span>
            </li>
            <li class="flex items-start">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Simpan sebagai draft jika belum selesai, lanjutkan nanti</span>
            </li>
        </ul>
    </div>
</div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>

@endsection