{{-- resources/views/ketua-divisi/penilaian/show.blade.php --}}
@extends('layouts.ketua-divisi')

@section('page-title', 'Detail Penilaian')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900">📋 Detail Penilaian</h1>
            <p class="text-gray-600 text-sm lg:text-base">Hasil penilaian yang telah diselesaikan</p>
        </div>
        <a href="{{ route('ketua-divisi.riwayat.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium transition-colors">
            ← Kembali
        </a>
    </div>

    <!-- Pegawai Info & Skor Akhir -->
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Info Pegawai -->
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <i data-lucide="user" class="text-white w-6 h-6"></i>
                </div>
                <div>
                    <div class="font-semibold text-gray-900 text-lg">{{ $penilaian->allocation->dinilai->name ?? '-' }}</div>
                    <div class="text-gray-600 text-sm mt-1">
                        {{ $penilaian->allocation->dinilai->position->name ?? '-' }} • 
                        {{ $penilaian->allocation->dinilai->division->name ?? '-' }}
                    </div>
                    <div class="text-gray-500 text-xs mt-1">NIP: {{ $penilaian->allocation->dinilai->nip ?? '-' }}</div>
                </div>
            </div>

            <!-- Skor Akhir & Info Siklus -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-200">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Skor Akhir</span>
                    <span class="text-3xl font-bold 
                        @if($penilaian->skor_akhir >= 4.0) text-green-600
                        @elseif($penilaian->skor_akhir >= 3.0) text-yellow-600
                        @else text-red-600
                        @endif">
                        {{ number_format($penilaian->skor_akhir, 2) }}
                    </span>
                </div>
                <div class="text-xs text-gray-600 space-y-1">
                    <div class="flex items-center">
                        <i data-lucide="calendar" class="w-3 h-3 mr-1"></i>
                        <span>{{ $penilaian->allocation->siklus->nama ?? '-' }}</span>
                    </div>
                    <div class="flex items-center">
                        <i data-lucide="clock" class="w-3 h-3 mr-1"></i>
                        <span>Dinilai pada: {{ $penilaian->updated_at->format('d M Y, H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <div class="flex space-x-2 overflow-x-auto">
                <button type="button" id="tabQuantitative" onclick="switchTab('quantitative')" class="px-4 py-2 rounded-lg bg-blue-600 text-white font-medium text-sm transition-colors whitespace-nowrap">
                    Tujuan Kuantitatif
                </button>
                <button type="button" id="tabCompetency" onclick="switchTab('competency')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium text-sm transition-colors whitespace-nowrap">
                    Kompetensi
                </button>
                <button type="button" id="tabFeedback" onclick="switchTab('feedback')" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50 font-medium text-sm transition-colors whitespace-nowrap">
                    Ringkasan/Umpan Balik
                </button>
            </div>
            <div class="text-sm text-gray-600">
                Progress: <span id="progressIndicator">1/3</span>
            </div>
        </div>
    </div>

    <!-- Tab Content -->
    <div id="tabContent" class="space-y-6">

        <!-- Tab 1: Tujuan Kuantitatif -->
        <div id="quantitativeTab" class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Skor Kuantitatif</h3>
            <p class="text-gray-600 mb-4 text-sm">Hasil penilaian untuk setiap kriteria kuantitatif.</p>

            @forelse($hasilKuantitatif as $hasil)
            <div class="border border-gray-200 rounded-lg p-4 mb-4 bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <strong class="text-gray-900 text-sm">{{ $hasil->criterion->name }}</strong>
                            <span class="text-xs text-blue-600 font-medium">Bobot: {{ $hasil->criterion->weight }}%</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="range" min="1" max="5" 
                                   value="{{ $hasil->skor }}"
                                   disabled
                                   class="w-full h-2 bg-gray-300 rounded-lg appearance-none cursor-not-allowed slider-disabled">
                            <div class="flex space-x-1">
                                @for($i = 1; $i <= 5; $i++)
                                <span class="text-xs text-gray-500 w-6 text-center">{{ $i }}</span>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div>
                        <input type="number" 
                               value="{{ $hasil->skor }}" 
                               disabled
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-center text-sm font-semibold cursor-not-allowed">
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-gray-500">
                <i data-lucide="alert-circle" class="w-12 h-12 mx-auto mb-2 text-gray-300"></i>
                <p>Tidak ada data kriteria kuantitatif.</p>
            </div>
            @endforelse
        </div>

        <!-- Tab 2: Kompetensi -->
        <div id="competencyTab" class="bg-white rounded-xl border border-gray-200 p-6 hidden">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Skor Kompetensi</h3>
            <p class="text-gray-600 mb-4 text-sm">Hasil penilaian untuk kompetensi perilaku kerja.</p>

            @forelse($hasilKompetensi as $hasil)
            <div class="border border-gray-200 rounded-lg p-4 mb-4 bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <strong class="text-gray-900 text-sm">{{ $hasil->criterion->name }}</strong>
                            <span class="text-xs text-blue-600 font-medium">Bobot: {{ $hasil->criterion->weight }}%</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="range" min="1" max="5" 
                                   value="{{ $hasil->skor }}"
                                   disabled
                                   class="w-full h-2 bg-gray-300 rounded-lg appearance-none cursor-not-allowed slider-disabled">
                            <div class="flex space-x-1">
                                @for($i = 1; $i <= 5; $i++)
                                <span class="text-xs text-gray-500 w-6 text-center">{{ $i }}</span>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div>
                        <input type="number" 
                               value="{{ $hasil->skor }}" 
                               disabled
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100 text-center text-sm font-semibold cursor-not-allowed">
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-8 text-gray-500">
                <i data-lucide="alert-circle" class="w-12 h-12 mx-auto mb-2 text-gray-300"></i>
                <p>Tidak ada data kriteria kompetensi.</p>
            </div>
            @endforelse
        </div>

        <!-- Tab 3: Umpan Balik -->
        <div id="feedbackTab" class="bg-white rounded-xl border border-gray-200 p-6 hidden">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Ringkasan dan Umpan Balik</h3>
            <p class="text-gray-600 mb-4 text-sm">Catatan dan umpan balik dari penilai.</p>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Penilai</label>
                    <div class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50 text-sm text-gray-700 whitespace-pre-line min-h-[150px]">{{ $penilaian->catatan ?? 'Tidak ada catatan.' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.slider-disabled::-webkit-slider-thumb {
    appearance: none;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #9ca3af;
    cursor: not-allowed;
}
.slider-disabled::-moz-range-thumb {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #9ca3af;
    cursor: not-allowed;
    border: none;
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') lucide.createIcons();
        switchTab('quantitative');
    });

    function switchTab(tabName) {
        // Hide all tabs
        document.getElementById('quantitativeTab').classList.add('hidden');
        document.getElementById('competencyTab').classList.add('hidden');
        document.getElementById('feedbackTab').classList.add('hidden');

        // Show selected tab
        document.getElementById(tabName + 'Tab').classList.remove('hidden');

        // Update active button
        ['quantitative', 'competency', 'feedback'].forEach(name => {
            const btn = document.getElementById('tab' + name.charAt(0).toUpperCase() + name.slice(1));
            if (name === tabName) {
                btn.classList.add('bg-blue-600', 'text-white');
                btn.classList.remove('border', 'border-gray-300', 'text-gray-700', 'hover:bg-gray-50');
            } else {
                btn.classList.remove('bg-blue-600', 'text-white');
                btn.classList.add('border', 'border-gray-300', 'text-gray-700', 'hover:bg-gray-50');
            }
        });

        updateProgress();
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function updateProgress() {
        let current = 1;
        if (!document.getElementById('competencyTab').classList.contains('hidden')) current = 2;
        if (!document.getElementById('feedbackTab').classList.contains('hidden')) current = 3;
        document.getElementById('progressIndicator').textContent = `${current}/3`;
    }
</script>
@endsection