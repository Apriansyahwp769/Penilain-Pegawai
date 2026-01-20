{{-- resources/views/hrd/verifikasi/show.blade.php --}}
@extends('layouts.hrd')

@section('page-title', 'Verifikasi Penilaian')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900">✅ Verifikasi Penilaian</h1>
            <p class="text-gray-600 text-sm lg:text-base">Tinjau dan verifikasi penilaian dari Ketua Divisi</p>
        </div>
        <a href="{{ route('hrd.verifikasi.index') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium transition-colors">
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
                <div class="font-medium text-gray-900">{{ $penilaian->allocation->dinilai->name ?? '-' }}</div>
                <div class="text-gray-600 text-sm">
                    {{ $penilaian->allocation->dinilai->position->name ?? '-' }} • 
                    {{ $penilaian->allocation->dinilai->division->name ?? '-' }} • 
                    NIP: {{ $penilaian->allocation->dinilai->nip ?? '-' }}
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
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Tujuan Kuantitatif</h3>
            <p class="text-gray-600 mb-4 text-sm">Skor kuantitatif dari Ketua Divisi (hanya untuk ditinjau).</p>

            @forelse($hasilKuantitatif as $hasil)
            <div class="border border-gray-200 rounded-lg p-4 mb-4 bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <strong class="text-gray-900 text-sm">{{ $hasil->criterion->name }}</strong>
                            <span class="text-xs text-blue-600 font-medium">Bobot: {{ $hasil->criterion->weight }}%</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="range" 
                                   min="1" max="5" 
                                   value="{{ $hasil->skor }}"
                                   disabled
                                   class="w-full h-2 bg-gray-300 rounded-lg appearance-none cursor-not-allowed">
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

                <!-- File Penunjang -->
                @if($hasil->file_penunjang)
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <a href="{{ route('ketua-divisi.penilaian.download-file', $hasil->id) }}" 
                           class="text-xs text-blue-600 hover:underline flex items-center">
                            <i data-lucide="file-text" class="w-3 h-3 mr-1"></i>
                            Unduh dokumen pendukung
                        </a>
                    </div>
                @endif
            </div>
            @empty
            <div class="text-center py-8 text-gray-500">
                <i data-lucide="alert-circle" class="w-12 h-12 mx-auto mb-2 text-gray-300"></i>
                <p>Tidak ada kriteria kuantitatif yang dinilai.</p>
            </div>
            @endforelse
        </div>

        <!-- Tab 2: Kompetensi -->
        <div id="competencyTab" class="bg-white rounded-xl border border-gray-200 p-6 hidden">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Kompetensi</h3>
            <p class="text-gray-600 mb-4 text-sm">Penilaian kompetensi dari Ketua Divisi.</p>

            @forelse($hasilKompetensi as $hasil)
            <div class="border border-gray-200 rounded-lg p-4 mb-4 bg-gray-50">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <strong class="text-gray-900 text-sm">{{ $hasil->criterion->name }}</strong>
                            <span class="text-xs text-blue-600 font-medium">Bobot: {{ $hasil->criterion->weight }}%</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="range" 
                                   min="1" max="5" 
                                   value="{{ $hasil->skor }}"
                                   disabled
                                   class="w-full h-2 bg-gray-300 rounded-lg appearance-none cursor-not-allowed">
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

                <!-- File Penunjang -->
                @if($hasil->file_penunjang)
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <a href="{{ route('ketua-divisi.penilaian.download-file', $hasil->id) }}" 
                           class="text-xs text-blue-600 hover:underline flex items-center">
                            <i data-lucide="file-text" class="w-3 h-3 mr-1"></i>
                            Unduh dokumen pendukung
                        </a>
                    </div>
                @endif
            </div>
            @empty
            <div class="text-center py-8 text-gray-500">
                <i data-lucide="alert-circle" class="w-12 h-12 mx-auto mb-2 text-gray-300"></i>
                <p>Tidak ada kriteria kompetensi yang dinilai.</p>
            </div>
            @endforelse
        </div>

        <!-- Tab 3: Umpan Balik -->
        <div id="feedbackTab" class="bg-white rounded-xl border border-gray-200 p-6 hidden">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Ringkasan dan Umpan Balik</h3>
            <p class="text-gray-600 mb-4 text-sm">Catatan dari Ketua Divisi.</p>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Penilaian</label>
                <div class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 text-sm text-gray-700 min-h-[120px] whitespace-pre-line">
                    {{ $penilaian->catatan ?? 'Tidak ada catatan.' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Conditional Action Buttons or Info -->
    @if($penilaian->status === 'menunggu_verifikasi')
        <div class="bg-white rounded-xl border border-gray-200 p-4 mt-6 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
            <button type="button" onclick="openRejectModal()"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium transition-colors">
                ❌ Tolak
            </button>
            <button type="button" onclick="openAcceptModal()"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium transition-colors">
                ✅ Terima
            </button>
        </div>

        <!-- Hidden Form -->
        <form id="verifikasiForm" method="POST" action="{{ route('hrd.verifikasi.verify', $penilaian->id) }}">
            @csrf
            <input type="hidden" name="action" id="actionInput">
        </form>

        <!-- Accept Modal -->
        <div id="acceptModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden p-4">
            <div class="bg-white rounded-xl w-full max-w-md border border-gray-200 shadow-lg">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <i data-lucide="check" class="w-6 h-6 text-green-600"></i>
                        </div>
                        <h3 class="ml-3 text-lg font-semibold text-gray-900">Konfirmasi Penerimaan</h3>
                    </div>
                    <p class="text-gray-600 mb-6">
                        Yakin ingin menerima penilaian ini? Status akan diubah menjadi <strong class="text-green-600">SELESAI</strong> dan tidak dapat diubah lagi.
                    </p>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('acceptModal')"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm">
                            Batal
                        </button>
                        <button type="button" onclick="submitAction('terima')"
                                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm font-medium">
                            Terima
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden p-4">
            <div class="bg-white rounded-xl w-full max-w-md border border-gray-200 shadow-lg">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                            <i data-lucide="x" class="w-6 h-6 text-red-600"></i>
                        </div>
                        <h3 class="ml-3 text-lg font-semibold text-gray-900">Konfirmasi Penolakan</h3>
                    </div>
                    <p class="text-gray-600 mb-6">
                        Yakin ingin menolak penilaian ini? Data penilaian akan <strong>dihapus</strong> dan Ketua Divisi harus mengisi ulang dari awal.
                    </p>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeModal('rejectModal')"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 text-sm">
                            Batal
                        </button>
                        <button type="button" onclick="submitAction('tolak')"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium">
                            Tolak
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl border border-gray-200 p-4 mt-6">
            <div class="text-center text-gray-600">
                <i data-lucide="check-circle" class="w-12 h-12 text-green-500 mx-auto mb-2"></i>
                <p class="font-medium text-gray-900">Penilaian ini telah <span class="text-green-600">SELESAI</span>.</p>
                <p class="text-sm mt-1">Tidak diperlukan aksi lebih lanjut.</p>
            </div>
        </div>
    @endif
</div>

<style>
input:disabled {
    background-color: #f3f4f6;
    cursor: not-allowed;
}
</style>

<script>
    function switchTab(tabName) {
        document.getElementById('quantitativeTab').classList.add('hidden');
        document.getElementById('competencyTab').classList.add('hidden');
        document.getElementById('feedbackTab').classList.add('hidden');

        document.getElementById(tabName + 'Tab').classList.remove('hidden');

        const tabs = ['quantitative', 'competency', 'feedback'];
        tabs.forEach(name => {
            const btn = document.getElementById('tab' + name.charAt(0).toUpperCase() + name.slice(1));
            btn.classList.toggle('bg-blue-600', name === tabName);
            btn.classList.toggle('text-white', name === tabName);
            btn.classList.toggle('border', name !== tabName);
            btn.classList.toggle('border-gray-300', name !== tabName);
            btn.classList.toggle('text-gray-700', name !== tabName);
            btn.classList.toggle('hover:bg-gray-50', name !== tabName);
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

    function openAcceptModal() {
        document.getElementById('acceptModal').classList.remove('hidden');
    }

    function openRejectModal() {
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    function submitAction(action) {
        document.getElementById('actionInput').value = action;
        document.getElementById('verifikasiForm').submit();
    }

    // Close modal on outside click
    document.querySelectorAll('#acceptModal, #rejectModal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) closeModal(this.id);
        });
    });

    // Initialize
    document.addEventListener('DOMContentLoaded', function () {
        switchTab('quantitative');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });
</script>
@endsection