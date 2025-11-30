{{-- resources/views/ketua-divisi/riwayat/index.blade.php --}}
@extends('layouts.ketua-divisi')

@section('page-title', 'Riwayat Tim')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900">📊 Riwayat Penilaian</h1>
            <p class="text-gray-600 text-sm lg:text-base">Melihat data historis penilaian tim untuk referensi dan perbandingan</p>
        </div>
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

    <!-- Filter Section -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <div class="flex flex-col lg:flex-row lg:items-center space-y-2 lg:space-y-0 lg:space-x-4">
                <!-- Filter Periode -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter Periode</label>
                    <select id="siklusFilter" onchange="filterHistory()" class="w-full lg:w-48 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="">Semua Periode</option>
                        @foreach($allSiklus as $siklus)
                        <option value="{{ $siklus->id }}">{{ $siklus->nama }}</option>
                        @endforeach
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
                            onkeyup="filterHistory()"
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
            <table class="w-full min-w-[1000px] lg:min-w-0">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Pegawai</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Jabatan</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Siklus</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Skor Akhir</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Status</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="historyTableBody">
                    @forelse($riwayat as $item)
                    <tr class="hover:bg-gray-50 transition-colors history-row"
                        data-siklus="{{ $item['siklus_id'] }}"
                        data-name="{{ strtolower($item['name']) }}"
                        data-position="{{ strtolower($item['position']) }}">
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                                    <i data-lucide="user" class="text-white w-4 h-4"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 text-sm lg:text-base">{{ $item['name'] }}</div>
                                    <div class="text-gray-500 text-xs">NIP: {{ $item['nip'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="text-gray-900 text-sm">{{ $item['position'] }}</div>
                            <div class="text-gray-500 text-xs">{{ $item['division'] }}</div>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="text-gray-900 text-sm font-medium">{{ $item['siklus_nama'] }}</div>
                            <div class="text-gray-500 text-xs">{{ $item['tanggal_dinilai'] }}</div>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($item['skor_akhir'] >= 4.0) bg-green-100 text-green-800
                                @elseif($item['skor_akhir'] >= 3.0) bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ number_format($item['skor_akhir'], 2) }}
                            </span>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item['status_badge'] }}">
                                {{ $item['status_label'] }}
                            </span>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('ketua-divisi.penilaian.show', $item['penilaian_id']) }}" 
                                   class="p-1 text-gray-400 hover:text-blue-600 transition-colors"
                                   title="Lihat Detail">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i data-lucide="inbox" class="w-12 h-12 text-gray-300 mb-2"></i>
                                <p>Belum ada riwayat penilaian yang diselesaikan</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Filter & Search Logic
function filterHistory() {
    const siklusFilter = document.getElementById('siklusFilter').value;
    const searchQuery = document.getElementById('searchInput').value.toLowerCase();

    const rows = document.querySelectorAll('.history-row');
    let visibleCount = 0;

    updateActiveFilters(siklusFilter, searchQuery);

    rows.forEach(row => {
        const siklus = row.getAttribute('data-siklus');
        const name = row.getAttribute('data-name') || '';
        const position = row.getAttribute('data-position') || '';

        let matchesSiklus = !siklusFilter || siklus === siklusFilter;
        let matchesSearch = !searchQuery || name.includes(searchQuery) || position.includes(searchQuery);

        if (matchesSiklus && matchesSearch) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
}

function updateActiveFilters(siklus, search) {
    const container = document.getElementById('activeFilters');
    container.innerHTML = '';

    let hasActive = false;

    if (siklus) {
        const siklusText = document.getElementById('siklusFilter').options[document.getElementById('siklusFilter').selectedIndex].text;
        container.innerHTML += `
            <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                Periode: ${siklusText}
                <button onclick="removeFilter('siklus')" class="ml-1 text-blue-600 hover:text-blue-800">
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
    if (type === 'siklus') {
        document.getElementById('siklusFilter').value = '';
    } else if (type === 'search') {
        document.getElementById('searchInput').value = '';
    }
    filterHistory();
}

function resetFilters() {
    document.getElementById('siklusFilter').value = '';
    document.getElementById('searchInput').value = '';
    filterHistory();
}

// Initialize on load
document.addEventListener('DOMContentLoaded', function() {
    filterHistory();
    if (typeof lucide !== 'undefined') lucide.createIcons();
});
</script>
@endsection