{{-- resources/views/admin/monitoring/show.blade.php --}}
@extends('layouts.admin')

@section('page-title', 'Detail Penilaian')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Detail Penilaian</h1>
            <p class="text-gray-600 text-sm lg:text-base">Hasil penilaian pegawai - Admin dapat mengubah status</p>
        </div>
        <a href="{{ route('admin.monitoring.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium transition-colors">
            Kembali
        </a>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-red-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
        </div>
    </div>
    @endif

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
                    <div class="text-gray-600 text-sm mt-2">
                        <span class="font-medium">Penilai:</span> {{ $penilaian->allocation->penilai->name ?? '-' }}
                    </div>
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

    <!-- Status Control (Admin Only) -->
<div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
    <form action="{{ route('admin.monitoring.updateStatus', $penilaian->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="flex flex-col lg:flex-row lg:items-center lg:space-x-4 space-y-4 lg:space-y-0">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Status Penilaian</label>
                <select name="status" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        style="font-size: 0.875rem;">
                    <option value="selesai" {{ $penilaian->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="draft" {{ $penilaian->status == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="belum_dinilai" {{ $penilaian->status == 'belum_dinilai' ? 'selected' : '' }}>Belum Dinilai</option>
                </select>
            </div>
            <div class="lg:mt-6">
                <button type="submit" 
                        class="w-full lg:w-auto px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                        style="font-size: 0.875rem; padding-top: 0.5rem; padding-bottom: 0.5rem;">
                    Update Status
                </button>
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-2">
            Status saat ini: 
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $penilaian->status_badge }}">
                {{ $penilaian->status_label }}
            </span>
        </p>
    </form>
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