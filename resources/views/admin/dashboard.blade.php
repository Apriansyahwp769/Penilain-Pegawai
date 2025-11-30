{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('page-title', 'Dashboard')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-gray-900">👋 Halo!! Admin Selamat Datang</h1>
        <p class="text-gray-600 text-sm lg:text-base">Monitoring Progres Siklus Aktif</p>
    </div>

    @if(!$siklusAktif)
        <!-- Alert jika tidak ada siklus aktif -->
        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-600" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        Tidak ada siklus aktif. Silakan buat atau aktifkan siklus terlebih dahulu.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Card 1: Siklus Aktif -->
        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-xl p-6 shadow-sm">
            <div class="flex items-center mb-3">
                <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h3 class="text-sm font-medium text-blue-800">Siklus Aktif</h3>
            </div>
            <p class="text-3xl font-bold text-blue-700 mt-1 truncate">{{ $siklusNama }}</p>
            <p class="text-xs mt-2 text-gray-600">
                @if($sisaHari > 0)
                    Sisa: <strong>{{ $sisaHari }} hari</strong> menuju deadline ({{ $deadlineDate }})
                @elseif($sisaHari == 0)
                    <strong>Hari ini</strong> adalah deadline! ({{ $deadlineDate }})
                @else
                    <strong class="text-red-600">Melewati deadline</strong> {{ abs($sisaHari) }} hari ({{ $deadlineDate }})
                @endif
            </p>
        </div>

        <!-- Card 2: Penilaian Selesai -->
        <div class="bg-green-50 border-l-4 border-green-500 rounded-xl p-6 shadow-sm">
            <div class="flex items-center mb-3">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-sm font-medium text-green-800">Penilaian Selesai</h3>
            </div>
            <p class="text-3xl font-bold text-green-700 mt-1">{{ $penilaianSelesai }}</p>
            <p class="text-xs mt-2 text-gray-600">Total penilaian yang sudah disubmit</p>
        </div>

        <!-- Card 3: Persentase Progres -->
        <div class="bg-orange-50 border-l-4 border-orange-500 rounded-xl p-6 shadow-sm">
            <div class="flex items-center mb-3">
                <svg class="w-5 h-5 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                <h3 class="text-sm font-medium text-orange-800">Persentase Progres</h3>
            </div>
            <p class="text-3xl font-bold text-orange-700 mt-1">{{ $persentaseSelesai }}%</p>
            <p class="text-xs mt-2 text-gray-600">{{ $penilaianSelesai }} Selesai dari {{ $totalPenilaian }} Penilaian</p>
        </div>
    </div>

    <!-- Grafik Progres Penilaian -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            Grafik Progres Penilaian
        </h2>

        <div class="space-y-6">
            <!-- Selesai -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Selesai</span>
                    <span class="text-sm font-bold text-green-600">{{ $persentaseSelesai }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-6">
                    <div class="bg-green-500 h-6 rounded-full flex items-center justify-end pr-3" style="width: {{ $persentaseSelesai }}%;">
                        @if($persentaseSelesai > 10)
                            <span class="text-white text-xs font-semibold">{{ $penilaianSelesai }}/{{ $totalPenilaian }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Belum Dinilai -->
            @php
                $belumDinilai = $totalPenilaian - $penilaianSelesai;
                $persentaseBelum = 100 - $persentaseSelesai;
            @endphp
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Belum Dinilai</span>
                    <span class="text-sm font-bold text-orange-600">{{ $persentaseBelum }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-6">
                    <div class="bg-orange-500 h-6 rounded-full flex items-center justify-end pr-3" style="width: {{ $persentaseBelum }}%;">
                        @if($persentaseBelum > 10)
                            <span class="text-white text-xs font-semibold">{{ $belumDinilai }}/{{ $totalPenilaian }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Penilaian per Divisi -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                Status Penilaian per Ketua Divisi
            </h2>
        </div>

        <div class="overflow-x-auto">
            @if($divisiStats->isEmpty())
                <div class="p-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                    </svg>
                    <p>Tidak ada data ketua divisi.</p>
                </div>
            @else
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-4 lg:px-6 py-3 text-left text-xs lg:text-sm font-semibold text-gray-900">Ketua Divisi</th>
                            <th class="px-4 lg:px-6 py-3 text-left text-xs lg:text-sm font-semibold text-gray-900">Divisi</th>
                            <th class="px-4 lg:px-6 py-3 text-center text-xs lg:text-sm font-semibold text-gray-900">Jumlah Bawahan</th>
                            <th class="px-4 lg:px-6 py-3 text-center text-xs lg:text-sm font-semibold text-gray-900">Progress</th>
                            <th class="px-4 lg:px-6 py-3 text-center text-xs lg:text-sm font-semibold text-gray-900">Status</th>
                            <th class="px-4 lg:px-6 py-3 text-center text-xs lg:text-sm font-semibold text-gray-900">⚠️</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($divisiStats as $stat)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 lg:px-6 py-4 font-medium text-gray-900 text-sm">
                                {{ $stat['ketua_nama'] }}
                            </td>
                            <td class="px-4 lg:px-6 py-4 text-gray-700 text-sm">
                                {{ $stat['divisi_nama'] }}
                            </td>
                            <td class="px-4 lg:px-6 py-4 text-center text-gray-700 text-sm">
                                {{ $stat['total_bawahan'] }}
                            </td>
                            <td class="px-4 lg:px-6 py-4 text-center text-sm">
                                <div class="flex items-center justify-center gap-2">
                                    <span class="font-semibold">{{ $stat['total_selesai'] }}/{{ $stat['total_bawahan'] }}</span>
                                    <span class="text-xs text-gray-500">({{ $stat['persentase'] }}%)</span>
                                </div>
                            </td>
                            <td class="px-4 lg:px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $stat['status_badge'] }}">
                                    {{ $stat['status'] }}
                                </span>
                            </td>
                            <td class="px-4 lg:px-6 py-4 text-center">
                                @if($stat['show_warning'])
                                    <span class="text-red-600 text-xl" title="Perlu perhatian khusus">⚠️</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

</div>

<script>
    // Jika kamu menggunakan Lucide di layout, biarkan. Jika tidak, script ini tetap aman.
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>

@endsection