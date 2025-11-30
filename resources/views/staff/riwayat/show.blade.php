@extends('layouts.staff')

@section('page-title', 'Detail Riwayat Penilaian')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start mb-6 space-y-4 lg:space-y-0">
        <div>
            <div class="flex items-center space-x-2 mb-2">
                <a href="{{ route('staff.riwayat.index') }}" class="text-blue-600 hover:text-blue-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Detail Riwayat Penilaian</h1>
            </div>
            <p class="text-gray-600 text-sm">Periode: <strong>{{ $siklusNama }}</strong></p>
        </div>
    </div>

    <!-- Overall Score Card -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">
            Hasil Penilaian Akhir [{{ $siklusNama }}]
        </h2>
        <div class="flex flex-col sm:flex-row sm:items-end sm:space-x-4">
            <div class="text-4xl lg:text-5xl font-bold {{ $kategoriSkor['badge'] ?? 'text-gray-500' }}">
                {{ number_format($skorAkhir, 1) }}
            </div>
            <div class="text-xl text-gray-500">/ 5.0</div>
            <div class="mt-2 sm:mt-0">
                <span class="inline-flex items-center px-3 py-1 {{ $kategoriSkor['bg'] ?? 'bg-gray-100' }} {{ $kategoriSkor['text'] ?? 'text-gray-800' }} text-sm font-medium rounded-full">
                    {{ $kategoriSkor['label'] ?? 'Unknown' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Feedback Card -->
    @if($catatan)
    <div class="bg-blue-50 border border-blue-300 rounded-xl p-6 mb-6">
        <div class="flex items-start space-x-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600 mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-1.247l-3.745.996.996-3.745A9.863 9.863 0 013 12c0-4.97 4.03-9 9-9s9 4.03 9 9z" />
            </svg>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Umpan Balik dan Saran dari Atasan</h3>
                <blockquote class="text-gray-800 italic text-base leading-relaxed border-l-4 border-blue-500 pl-4 mb-4">
                    "{{ $catatan }}"
                </blockquote>
                @if($penilai)
                <cite class="text-gray-600 text-sm">
                    — {{ $penilai->name }}, {{ $penilai->position->name ?? 'Penilai' }}
                </cite>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Detail Skor Per Kriteria -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Detail Skor per Kriteria</h3>
        
        <!-- Kriteria Kuantitatif -->
        @if($hasilKuantitatif->count() > 0)
        <div class="mb-6">
            <h4 class="text-md font-semibold text-gray-800 mb-3">Kriteria Kuantitatif</h4>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[600px]">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Kriteria</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Bobot</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Skor</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($hasilKuantitatif as $hasil)
                        @php
                            $skor = $hasil->skor;
                            if ($skor >= 4.0) {
                                $rowClass = 'bg-green-50';
                                $skorColor = 'text-green-700';
                                $statusLabel = 'Bagus';
                                $statusBorder = 'border-green-500';
                                $statusText = 'text-green-800';
                            } elseif ($skor >= 3.0) {
                                $rowClass = 'bg-white';
                                $skorColor = 'text-gray-900';
                                $statusLabel = 'Normal';
                                $statusBorder = 'border-blue-500';
                                $statusText = 'text-blue-800';
                            } else {
                                $rowClass = 'bg-red-50';
                                $skorColor = 'text-red-600';
                                $statusLabel = 'Rendah';
                                $statusBorder = 'border-red-500';
                                $statusText = 'text-red-800';
                            }
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td class="px-4 py-3 text-gray-900">{{ $hasil->criterion->name }}</td>
                            <td class="px-4 py-3 text-gray-900">{{ number_format($hasil->criterion->weight, 0) }}%</td>
                            <td class="px-4 py-3 font-bold {{ $skorColor }}">{{ number_format($skor, 1) }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-3 py-1 border {{ $statusBorder }} {{ $statusText }} text-xs font-medium rounded-full">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Kriteria Kompetensi -->
        @if($hasilKompetensi->count() > 0)
        <div>
            <h4 class="text-md font-semibold text-gray-800 mb-3">Kriteria Kompetensi</h4>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[600px]">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Kriteria</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Bobot</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Skor</th>
                            <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($hasilKompetensi as $hasil)
                        @php
                            $skor = $hasil->skor;
                            if ($skor >= 4.0) {
                                $rowClass = 'bg-green-50';
                                $skorColor = 'text-green-700';
                                $statusLabel = 'Bagus';
                                $statusBorder = 'border-green-500';
                                $statusText = 'text-green-800';
                            } elseif ($skor >= 3.0) {
                                $rowClass = 'bg-white';
                                $skorColor = 'text-gray-900';
                                $statusLabel = 'Normal';
                                $statusBorder = 'border-blue-500';
                                $statusText = 'text-blue-800';
                            } else {
                                $rowClass = 'bg-red-50';
                                $skorColor = 'text-red-600';
                                $statusLabel = 'Rendah';
                                $statusBorder = 'border-red-500';
                                $statusText = 'text-red-800';
                            }
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td class="px-4 py-3 text-gray-900">{{ $hasil->criterion->name }}</td>
                            <td class="px-4 py-3 text-gray-900">{{ number_format($hasil->criterion->weight, 0) }}%</td>
                            <td class="px-4 py-3 font-bold {{ $skorColor }}">{{ number_format($skor, 1) }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-3 py-1 border {{ $statusBorder }} {{ $statusText }} text-xs font-medium rounded-full">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>

    <!-- Back Button -->
    <div class="flex justify-center">
        <a href="{{ route('staff.riwayat.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Riwayat
        </a>
    </div>

</div>
@endsection