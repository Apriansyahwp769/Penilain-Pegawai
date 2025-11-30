{{-- resources/views/ketua-divisi/penilaian/index.blade.php --}}
@extends('layouts.ketua-divisi')

@section('page-title', 'Penilaian Tim')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900">📋 Penilaian Tim</h1>
            <p class="text-gray-600 text-sm lg:text-base">Kelola dan lakukan penilaian terhadap bawahan Anda</p>
        </div>
        <button onclick="openPenilaianGuide()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center space-x-2 w-full lg:w-auto">
            <i data-lucide="book-open" class="w-4 h-4 lg:w-5 lg:h-5"></i>
            <span class="text-sm lg:text-base">Panduan Penilaian</span>
        </button>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        {{ session('error') }}
    </div>
    @endif

    @if(session('info'))
    <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg mb-6">
        {{ session('info') }}
    </div>
    @endif

    <!-- Filter Section -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <div class="flex flex-col lg:flex-row lg:items-center space-y-2 lg:space-y-0 lg:space-x-4">
                <!-- Filter Status Penilaian -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status Penilaian</label>
                    <select id="statusFilter" onchange="filterEmployees()" class="w-full lg:w-48 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="">Semua Status</option>
                        <option value="selesai">Selesai</option>
                        <option value="draft">Draft</option>
                        <option value="belum_dinilai">Belum Dinilai</option>
                    </select>
                </div>

                <!-- Search Pegawai -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari Pegawai</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                        </div>
                        <input
                            type="text"
                            id="searchInput"
                            placeholder="Nama atau jabatan..."
                            onkeyup="filterEmployees()"
                            class="pl-10 w-full lg:w-64 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm"
                        >
                    </div>
                </div>
            </div>

            <!-- Reset Filter -->
            <div>
                <button onclick="resetFilters()" class="flex items-center space-x-2 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    <span>Reset Filter</span>
                </button>
            </div>
        </div>

        <!-- Active Filters Display -->
        <div id="activeFilters" class="mt-3 flex flex-wrap gap-2 hidden">
            <!-- Dinamis diisi oleh JS -->
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] lg:min-w-0">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Pegawai</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Jabatan</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Skor Akhir</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Status Penilaian</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="employeesTableBody">
                    @forelse($employees as $employee)
                    <tr class="hover:bg-gray-50 transition-colors employee-row"
                        data-status="{{ $employee['status'] }}"
                        data-name="{{ strtolower($employee['name']) }}"
                        data-position="{{ strtolower($employee['position']) }}">
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                                    <i data-lucide="user" class="text-white w-4 h-4"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 text-sm lg:text-base">{{ $employee['name'] }}</div>
                                    <div class="text-gray-500 text-xs">NIP: {{ $employee['nip'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="text-gray-900 text-sm">{{ $employee['position'] }}</div>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            @if($employee['skor_akhir'])
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 text-xs font-medium">
                                    {{ number_format($employee['skor_akhir'], 2) }}
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">—</span>
                            @endif
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $employee['status_badge'] }}">
                                {{ $employee['status_label'] }}
                            </span>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="flex items-center space-x-2">
                                @if($employee['status'] === 'selesai')
                                    <a href="{{ route('ketua-divisi.penilaian.show', $employee['penilaian_id']) }}" 
                                       class="p-1 text-gray-400 hover:text-blue-600 transition-colors"
                                       title="Lihat Detail">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                @else
                                    <a href="{{ route('ketua-divisi.penilaian.create', $employee['allocation_id']) }}" 
                                       class="p-1 text-gray-400 hover:text-green-600 transition-colors"
                                       title="Mulai/Lanjutkan Penilaian">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i data-lucide="inbox" class="w-12 h-12 text-gray-300 mb-2"></i>
                                <p>Belum ada pegawai yang dialokasikan untuk Anda</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Panduan Penilaian Modal -->
<div id="guideModal" class="fixed inset-0 flex items-center justify-center z-50 p-4 hidden">
    <div class="bg-white rounded-xl w-full max-w-2xl border border-gray-200 shadow-lg">
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Panduan Penilaian</h2>
            <button onclick="closeGuideModal()" class="text-gray-500 hover:text-gray-700 p-1 hover:bg-gray-100 rounded">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="p-4 max-h-[60vh] overflow-y-auto text-sm text-gray-700">
            <ul class="list-disc pl-5 space-y-2">
                <li>Status <span class="font-medium text-green-700">Selesai</span>: Penilaian telah disubmit dan tidak bisa diedit.</li>
                <li>Status <span class="font-medium text-yellow-700">Draft</span>: Penilaian sedang dikerjakan, bisa dilanjutkan kapan saja.</li>
                <li>Status <span class="font-medium text-red-700">Belum Dinilai</span>: Belum pernah diakses. Klik "Edit" untuk mulai menilai.</li>
                <li>Skor akhir akan muncul setelah penilaian disubmit.</li>
                <li>Penilaian harus diselesaikan sebelum deadline siklus aktif.</li>
            </ul>
        </div>
        <div class="p-4 border-t border-gray-200 flex justify-end">
            <button onclick="closeGuideModal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm font-medium">
                Mengerti
            </button>
        </div>
    </div>
</div>

<script>
// Filter & Search Logic
function filterEmployees() {
    const statusFilter = document.getElementById('statusFilter').value;
    const searchQuery = document.getElementById('searchInput').value.toLowerCase();

    const rows = document.querySelectorAll('.employee-row');
    let visibleCount = 0;

    updateActiveFilters(statusFilter, searchQuery);

    rows.forEach(row => {
        const status = row.getAttribute('data-status');
        const name = row.getAttribute('data-name') || '';
        const position = row.getAttribute('data-position') || '';

        let matchesStatus = !statusFilter || status === statusFilter;
        let matchesSearch = !searchQuery || name.includes(searchQuery) || position.includes(searchQuery);

        if (matchesStatus && matchesSearch) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
}

function updateActiveFilters(status, search) {
    const container = document.getElementById('activeFilters');
    container.innerHTML = '';

    let hasActive = false;

    if (status) {
        const statusText = status === 'selesai' ? 'Selesai' : status === 'draft' ? 'Draft' : 'Belum Dinilai';
        container.innerHTML += `
            <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                Status: ${statusText}
                <button onclick="removeFilter('status')" class="ml-1 text-blue-600 hover:text-blue-800">
                    <i data-lucide="x" class="w-3 h-3"></i>
                </button>
            </span>
        `;
        hasActive = true;
    }

    if (search) {
        container.innerHTML += `
            <span class="inline-flex items-center px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded-full">
                Cari: "${search}"
                <button onclick="removeFilter('search')" class="ml-1 text-gray-600 hover:text-gray-800">
                    <i data-lucide="x" class="w-3 h-3"></i>
                </button>
            </span>
        `;
        hasActive = true;
    }

    container.classList.toggle('hidden', !hasActive);

    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function removeFilter(type) {
    if (type === 'status') {
        document.getElementById('statusFilter').value = '';
    } else if (type === 'search') {
        document.getElementById('searchInput').value = '';
    }
    filterEmployees();
}

function resetFilters() {
    document.getElementById('statusFilter').value = '';
    document.getElementById('searchInput').value = '';
    filterEmployees();
}

// Modal Functions
function openPenilaianGuide() {
    document.getElementById('guideModal').classList.remove('hidden');
}

function closeGuideModal() {
    document.getElementById('guideModal').classList.add('hidden');
}

// Close modal on outside click
document.getElementById('guideModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeGuideModal();
});

// Initialize on load
document.addEventListener('DOMContentLoaded', function() {
    filterEmployees();
    if (typeof lucide !== 'undefined') lucide.createIcons();
});
</script>
@endsection