{{-- resources/views/ketua-divisi/penilaian/create.blade.php --}}
@extends('layouts.ketua-divisi')

@section('page-title', 'Form Penilaian')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900">📋 Form Penilaian</h1>
            <p class="text-gray-600 text-sm lg:text-base">Isi penilaian untuk bawahan Anda</p>
        </div>
        <a href="{{ route('ketua-divisi.penilaian.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium transition-colors">
            ← Kembali
        </a>
    </div>

    <!-- Pegawai Info -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
        <div class="flex items-center space-x-4">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                <i data-lucide="user" class="text-white w-5 h-5"></i>
            </div>
            <div>
                <div class="font-medium text-gray-900">{{ $allocation->dinilai->name ?? '-' }}</div>
                <div class="text-gray-600 text-sm">
                    {{ $allocation->dinilai->position->name ?? '-' }} • 
                    {{ $allocation->dinilai->division->name ?? '-' }} • 
                    NIP: {{ $allocation->dinilai->nip ?? '-' }}
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('ketua-divisi.penilaian.store', $allocation->id) }}" method="POST" id="penilaianForm">
        @csrf

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
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Input Skor Kuantitatif</h3>
                <p class="text-gray-600 mb-4 text-sm">Berikan penilaian untuk setiap kriteria dengan skala 1–5.</p>

                @forelse($kuantitatif as $kriteria)
                <div class="border border-gray-200 rounded-lg p-4 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <strong class="text-gray-900 text-sm">{{ $kriteria->name }}</strong>
                                <span class="text-xs text-blue-600 font-medium">Bobot: {{ $kriteria->weight }}%</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <input type="range" 
                                       min="1" max="5" 
                                       value="{{ $hasilPenilaian[$kriteria->id] ?? 3 }}"
                                       id="slider_{{ $kriteria->id }}"
                                       class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider"
                                       oninput="updateScore(this, '{{ $kriteria->id }}')">
                                <div class="flex space-x-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="text-xs text-gray-500 w-6 text-center">{{ $i }}</span>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <div>
                            <input type="number" 
                                   id="score_{{ $kriteria->id }}" 
                                   name="skor[{{ $kriteria->id }}]"
                                   value="{{ $hasilPenilaian[$kriteria->id] ?? 3 }}" 
                                   min="1" max="5" 
                                   required
                                   oninput="updateSlider(this, '{{ $kriteria->id }}')"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-center text-sm font-semibold">
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <i data-lucide="alert-circle" class="w-12 h-12 mx-auto mb-2 text-gray-300"></i>
                    <p>Tidak ada kriteria kuantitatif yang aktif saat ini.</p>
                </div>
                @endforelse
            </div>

            <!-- Tab 2: Kompetensi -->
            <div id="competencyTab" class="bg-white rounded-xl border border-gray-200 p-6 hidden">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Input Skor Kompetensi</h3>
                <p class="text-gray-600 mb-4 text-sm">Berikan penilaian untuk kompetensi perilaku kerja.</p>

                @forelse($kompetensi as $kriteria)
                <div class="border border-gray-200 rounded-lg p-4 mb-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <strong class="text-gray-900 text-sm">{{ $kriteria->name }}</strong>
                                <span class="text-xs text-blue-600 font-medium">Bobot: {{ $kriteria->weight }}%</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <input type="range" 
                                       min="1" max="5" 
                                       value="{{ $hasilPenilaian[$kriteria->id] ?? 3 }}"
                                       id="slider_{{ $kriteria->id }}"
                                       class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer slider"
                                       oninput="updateScore(this, '{{ $kriteria->id }}')">
                                <div class="flex space-x-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span class="text-xs text-gray-500 w-6 text-center">{{ $i }}</span>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <div>
                            <input type="number" 
                                   id="score_{{ $kriteria->id }}" 
                                   name="skor[{{ $kriteria->id }}]"
                                   value="{{ $hasilPenilaian[$kriteria->id] ?? 3 }}" 
                                   min="1" max="5" 
                                   required
                                   oninput="updateSlider(this, '{{ $kriteria->id }}')"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-center text-sm font-semibold">
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <i data-lucide="alert-circle" class="w-12 h-12 mx-auto mb-2 text-gray-300"></i>
                    <p>Tidak ada kriteria kompetensi yang aktif saat ini.</p>
                </div>
                @endforelse
            </div>

            <!-- Tab 3: Umpan Balik -->
            <div id="feedbackTab" class="bg-white rounded-xl border border-gray-200 p-6 hidden">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Ringkasan dan Umpan Balik</h3>
                <p class="text-gray-600 mb-4 text-sm">Berikan umpan balik konstruktif untuk pengembangan pegawai.</p>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Penilaian</label>
                    <textarea name="catatan" 
                              rows="8" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                              placeholder="Berikan catatan penilaian, kekuatan, dan area yang perlu ditingkatkan dari pegawai ini...">{{ old('catatan', $penilaian->catatan ?? '') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Contoh: "Pegawai menunjukkan kinerja yang baik dalam menyelesaikan tugas. Kekuatan: teliti dan responsif. Area pengembangan: komunikasi tim dan inisiatif."</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white rounded-xl border border-gray-200 p-4 mt-6 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
            <button type="submit" name="status" value="draft" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 text-sm font-medium transition-colors">
                💾 Simpan Draft
            </button>
            <button type="submit" name="status" value="selesai" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium transition-colors">
                ✉️ Kirim ke HRD
            </button>
        </div>
    </form>
</div>

<style>
.slider::-webkit-slider-thumb {
    appearance: none;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #3b82f6;
    cursor: pointer;
    box-shadow: 0 0 5px rgba(59, 130, 246, 0.5);
}
.slider::-moz-range-thumb {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #3b82f6;
    cursor: pointer;
    border: none;
    box-shadow: 0 0 5px rgba(59, 130, 246, 0.5);
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') lucide.createIcons();
        switchTab('quantitative');
    });

    function switchTab(tabName) {
        // Sembunyikan semua tab
        document.getElementById('quantitativeTab').classList.add('hidden');
        document.getElementById('competencyTab').classList.add('hidden');
        document.getElementById('feedbackTab').classList.add('hidden');

        // Tampilkan tab yang dipilih
        document.getElementById(tabName + 'Tab').classList.remove('hidden');

        // Update tombol aktif
        const tabs = ['quantitative', 'competency', 'feedback'];
        tabs.forEach(name => {
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

    // Slider → Input
    function updateScore(slider, criterionId) {
        const input = document.getElementById('score_' + criterionId);
        if (input) {
            input.value = slider.value;
        }
    }

    // Input → Slider
    function updateSlider(input, criterionId) {
        let value = parseInt(input.value);
        // Validasi: pastikan nilai antara 1–5
        if (isNaN(value)) value = 3;
        if (value < 1) value = 1;
        if (value > 5) value = 5;
        input.value = value; // normalize

        const slider = document.getElementById('slider_' + criterionId);
        if (slider) {
            slider.value = value;
        }
    }
</script>
@endsection