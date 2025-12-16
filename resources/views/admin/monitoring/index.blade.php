{{-- resources/views/admin/monitoring/index.blade.php --}}
@extends('layouts.admin')

@section('page-title', 'Monitoring Penilaian')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Monitoring Penilaian</h1>
            <p class="text-gray-600 text-sm lg:text-base">Pantau hasil penilaian staf dari periode aktif</p>
        </div>
    </div>

    <!-- Alert Messages -->
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

    @if(!$siklusAktif)
    <!-- Empty State -->
    <div class="bg-white rounded-xl border border-gray-200 p-12">
        <div class="flex flex-col items-center justify-center text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">Tidak Ada Periode Aktif</h3>
            <p class="text-gray-600 mb-4">Saat ini tidak ada periode penilaian yang aktif.</p>
            <a href="{{ route('admin.siklus.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Kelola Periode
            </a>
        </div>
    </div>
    @else
    <!-- Periode Info Card -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6">
        <!-- Periode Aktif -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-sm font-medium">Periode Aktif</h3>
            </div>
            <p class="text-2xl font-bold">{{ $siklusAktif->nama }}</p>
            <p class="text-xs mt-1 opacity-90">{{ $siklusAktif->tanggal_mulai->format('d M Y') }} - {{ $siklusAktif->tanggal_selesai->format('d M Y') }}</p>
        </div>

        <!-- Total Penilaian -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-sm font-medium text-gray-600">Total Penilaian</h3>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $totalPenilaian }}</p>
            <p class="text-xs text-gray-500 mt-1">Penilaian selesai</p>
        </div>

        <!-- Skor Rata-rata -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                </svg>
                <h3 class="text-sm font-medium text-gray-600">Skor Rata-rata</h3>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ number_format($skorRataRata, 1) }}</p>
            <p class="text-xs text-gray-500 mt-1">Dari skala 5.0</p>
        </div>

        <!-- Deadline -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center mb-2">
                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-sm font-medium text-gray-600">Deadline</h3>
            </div>
            @if($deadlinePassed)
                <p class="text-2xl font-bold text-red-600">Telah Lewat</p>
                <p class="text-xs text-gray-500 mt-1">Deadline: {{ $deadlineDate }}</p>
            @else
                <p class="text-2xl font-bold text-gray-900">{{ $daysLeft }}</p>
                <p class="text-xs text-gray-500 mt-1">Hari tersisa</p>
            @endif
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <div class="flex flex-col lg:flex-row lg:items-center space-y-2 lg:space-y-0 lg:space-x-4">
                <!-- Filter Divisi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter Divisi</label>
                    <select id="divisionFilter" onchange="filterMonitoring()" class="w-full lg:w-48 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="">Semua Divisi</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}">{{ $division->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter Status</label>
                    <select id="statusFilter" onchange="filterMonitoring()" class="w-full lg:w-48 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="">Semua Status</option>
                        <option value="Tinggi">Tinggi</option>
                        <option value="Normal">Normal</option>
                        <option value="Rendah">Rendah</option>
                    </select>
                </div>
            </div>

            <!-- Reset Filter -->
            <div>
                <button onclick="resetFilters()" class="flex items-center space-x-2 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Reset Filter</span>
                </button>
            </div>
        </div>

        <!-- Active Filters Display -->
        <div id="activeFilters" class="mt-3 flex flex-wrap gap-2 hidden"></div>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] lg:min-w-0">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Pegawai</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Skor Akhir</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Nama Penilai</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Status</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Tanggal</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="monitoringTableBody">
                    @forelse($monitoring as $item)
                    <tr class="hover:bg-gray-50 transition-colors monitoring-row" 
                        data-division="{{ $item['division_id'] }}" 
                        data-status="{{ $item['status'] }}">
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="text-gray-900 text-sm font-medium">{{ $item['pegawai_nama'] }}</div>
                            <div class="text-gray-500 text-xs">{{ $item['pegawai_jabatan'] }}</div>
                            <div class="text-blue-600 text-xs font-medium mt-1">{{ $item['pegawai_divisi'] }}</div>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <span class="font-semibold text-lg text-gray-900">{{ number_format($item['skor_akhir'], 2) }}</span>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="text-gray-900 text-sm">{{ $item['penilai_nama'] }}</div>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item['status_badge'] }}">
                                {{ $item['status'] }}
                            </span>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="text-gray-600 text-sm">{{ $item['tanggal_dinilai'] }}</div>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <a href="{{ route('admin.monitoring.show', $item['penilaian_id']) }}" 
                               class="inline-flex items-center px-3 py-1 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors text-sm">
                                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr id="emptyState">
                        <td colspan="6" class="px-4 lg:px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center space-y-2">
                                <svg class="w-12 h-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-sm lg:text-base">Belum ada penilaian yang selesai</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<script>
function filterMonitoring() {
    const divisionFilter = document.getElementById('divisionFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    
    const monitoringRows = document.querySelectorAll('.monitoring-row');
    let visibleCount = 0;
    
    updateActiveFilters(divisionFilter, statusFilter);
    
    monitoringRows.forEach(row => {
        const division = row.getAttribute('data-division');
        const status = row.getAttribute('data-status');
        
        let showRow = true;
        if (divisionFilter && division !== divisionFilter) showRow = false;
        if (statusFilter && status !== statusFilter) showRow = false;
        
        row.style.display = showRow ? '' : 'none';
        if (showRow) visibleCount++;
    });
    
    const emptyState = document.getElementById('emptyState');
    if (emptyState) emptyState.style.display = visibleCount === 0 ? '' : 'none';
}

function updateActiveFilters(division, status) {
    const activeFilters = document.getElementById('activeFilters');
    activeFilters.innerHTML = '';
    let hasActiveFilters = false;
    
    if (division) {
        const name = document.querySelector(`#divisionFilter option[value="${division}"]`).textContent;
        activeFilters.innerHTML += `<span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Divisi: ${name}<button onclick="removeFilter('division')" class="ml-1 text-blue-600 hover:text-blue-800"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></span>`;
        hasActiveFilters = true;
    }
    if (status) {
        activeFilters.innerHTML += `<span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Status: ${status}<button onclick="removeFilter('status')" class="ml-1 text-green-600 hover:text-green-800"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></span>`;
        hasActiveFilters = true;
    }
    
    activeFilters.classList.toggle('hidden', !hasActiveFilters);
}

function removeFilter(type) {
    document.getElementById(type === 'division' ? 'divisionFilter' : 'statusFilter').value = '';
    filterMonitoring();
}

function resetFilters() {
    document.getElementById('divisionFilter').value = '';
    document.getElementById('statusFilter').value = '';
    filterMonitoring();
}

document.addEventListener('DOMContentLoaded', function() {
    filterMonitoring();
    if (typeof lucide !== 'undefined') lucide.createIcons();
});
</script>
@endsection