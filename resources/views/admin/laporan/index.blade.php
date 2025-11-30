{{-- resources/views/admin/laporan/index.blade.php --}}
@extends('layouts.admin')

@section('page-title', 'Laporan dan Ekspor Data')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-end mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Laporan dan Ekspor Data</h1>
            <p class="text-gray-600 text-sm lg:text-base">Visualisasi dan penyediaan data akhir</p>
        </div>
        <div class="flex flex-col sm:flex-row sm:space-x-3 space-y-2 sm:space-y-0">
            <a href="{{ route('admin.laporan.pdf', request()->only(['siklus', 'divisi'])) }}"
                class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center justify-center space-x-2 w-full sm:w-auto">
                <i data-lucide="download" class="w-5 h-5"></i>
                <span>Cetak PDF</span>
            </a>
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

    @if(session('info'))
    <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm text-blue-700 font-medium">{{ session('info') }}</p>
        </div>
    </div>
    @endif

    <!-- Filter Section -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
            <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                <div class="w-full sm:w-48">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Filter Siklus</label>
                    <select id="siklusFilter" onchange="applyFilters()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="">Semua Siklus</option>
                        @foreach($siklusList as $siklus)
                        <option value="{{ $siklus->id }}">{{ $siklus->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-full sm:w-48">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Filter Divisi</label>
                    <select id="divisiFilter" onchange="applyFilters()" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="">Semua Divisi</option>
                        @foreach($divisionsList as $division)
                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <button onclick="resetFilters()" class="flex items-center justify-center space-x-2 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span>Reset</span>
            </button>
        </div>

        <!-- Active Filters Display -->
        <div id="activeFilters" class="mt-3 flex flex-wrap gap-2 hidden"></div>
    </div>

    <!-- Grafik + KPI Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Grafik -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Grafik Distribusi Skor</h2>
            <p class="text-gray-600 text-sm mb-6">Sebaran frekuensi skor di perusahaan</p>
            <div class="h-80">
                <canvas id="scoreDistributionChart"></canvas>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <p class="text-xs text-gray-500">Total Pegawai Dinilai</p>
                <p id="kpi-total" class="text-2xl font-bold text-gray-900 mt-1">0</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <p class="text-xs text-gray-500">Rata-rata Skor</p>
                <p id="kpi-avg" class="text-2xl font-bold text-green-600 mt-1">0.0</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <p class="text-xs text-gray-500">Sangat Baik (≥4.0)</p>
                <p id="kpi-sangat-baik" class="text-2xl font-bold text-green-600 mt-1">0</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                <p class="text-xs text-gray-500">Perlu Perbaikan (<2.5)< /p>
                        <p id="kpi-perlu-perbaikan" class="text-2xl font-bold text-red-600 mt-1">0</p>
            </div>
        </div>
    </div>

    <!-- Tabel Rata-rata Divisi -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-2">Tabel Rata-rata Divisi</h2>
        <p class="text-gray-600 text-sm mb-6">Ringkasan kinerja per divisi (diurutkan dari tertinggi ke terendah)</p>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 lg:px-6 py-3 text-left text-sm font-semibold text-gray-900">Divisi</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-sm font-semibold text-gray-900">Jumlah Pegawai</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-sm font-semibold text-gray-900">Rata-rata Skor</th>
                        <th class="px-4 lg:px-6 py-3 text-left text-sm font-semibold text-gray-900">Kategori</th>
                    </tr>
                </thead>
                <tbody id="divisiTableBody" class="divide-y divide-gray-200">
                    <tr id="emptyState">
                        <td colspan="4" class="px-4 lg:px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center space-y-2">
                                <svg class="w-12 h-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-sm">Tidak ada data penilaian yang selesai</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Info Footer -->
    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-start space-x-2">
            <i data-lucide="lightbulb" class="w-5 h-5 text-blue-600 mt-0.5"></i>
            <p class="text-sm text-blue-800">
                Laporan ini membantu Eksekutif dan HR membandingkan kinerja tim secara makro.
                <span id="filterInfo"></span>
            </p>
        </div>
    </div>
</div>

<!-- Load Chart.js dari CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Data dari backend (SEMUA data tanpa filter)
    const allData = @json($allData);

    let myChart = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi Lucide Icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Inisialisasi chart pertama kali
        const ctx = document.getElementById('scoreDistributionChart').getContext('2d');
        myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Skor 1', 'Skor 2', 'Skor 3', 'Skor 4', 'Skor 5'],
                datasets: [{
                    label: 'Jumlah Pegawai',
                    data: [0, 0, 0, 0, 0],
                    backgroundColor: [
                        'rgba(239, 68, 68, 0.7)',
                        'rgba(245, 158, 11, 0.7)',
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(4, 120, 87, 0.7)'
                    ],
                    borderColor: [
                        'rgb(239, 68, 68)',
                        'rgb(245, 158, 11)',
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(4, 120, 87)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.raw} pegawai`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 5,
                            precision: 0
                        }
                    }
                }
            }
        });

        // Apply filter pertama kali
        applyFilters();
    });

    function applyFilters() {
        const siklusFilter = document.getElementById('siklusFilter').value;
        const divisiFilter = document.getElementById('divisiFilter').value;

        // Filter data berdasarkan pilihan
        let filteredData = allData;

        if (siklusFilter) {
            filteredData = filteredData.filter(item => item.siklus_id == siklusFilter);
        }

        if (divisiFilter) {
            filteredData = filteredData.filter(item => item.division_id == divisiFilter);
        }

        // Update KPI
        updateKPI(filteredData);

        // Update Chart
        updateChart(filteredData);

        // Update Tabel Divisi
        updateDivisiTable(filteredData);

        // Update Active Filters Display
        updateActiveFilters(siklusFilter, divisiFilter);

        // Update Info Footer
        updateFilterInfo(siklusFilter, divisiFilter);
    }

    function updateKPI(data) {
        // Group by penilaian_id untuk KPI (hindari duplikat)
        const uniquePenilaian = {};
        data.forEach(item => {
            const key = `${item.siklus_id}_${item.division_id}_${item.skor_akhir}`;
            if (!uniquePenilaian[key]) {
                uniquePenilaian[key] = item;
            }
        });

        const penilaianArray = Object.values(uniquePenilaian);
        const total = penilaianArray.length;
        const avg = total > 0 ? (penilaianArray.reduce((sum, item) => sum + parseFloat(item.skor_akhir), 0) / total).toFixed(1) : 0;
        const sangatBaik = penilaianArray.filter(item => parseFloat(item.skor_akhir) >= 4.0).length;
        const perluPerbaikan = penilaianArray.filter(item => parseFloat(item.skor_akhir) < 2.5).length;

        document.getElementById('kpi-total').textContent = total;
        document.getElementById('kpi-avg').textContent = avg;
        document.getElementById('kpi-sangat-baik').textContent = sangatBaik;
        document.getElementById('kpi-perlu-perbaikan').textContent = perluPerbaikan;
    }

    function updateChart(data) {
        // Hitung frekuensi skor dari hasil_penilaian (skor 1-5)
        const distribusi = {
            skor_1: data.filter(item => item.skor === 1).length,
            skor_2: data.filter(item => item.skor === 2).length,
            skor_3: data.filter(item => item.skor === 3).length,
            skor_4: data.filter(item => item.skor === 4).length,
            skor_5: data.filter(item => item.skor === 5).length,
        };

        myChart.data.datasets[0].data = [
            distribusi.skor_1,
            distribusi.skor_2,
            distribusi.skor_3,
            distribusi.skor_4,
            distribusi.skor_5
        ];
        myChart.update();
    }

    function updateDivisiTable(data) {
        const tableBody = document.getElementById('divisiTableBody');
        const emptyState = document.getElementById('emptyState');

        // Group by penilaian untuk mendapatkan unique pegawai
        const uniquePenilaian = {};
        data.forEach(item => {
            const key = `${item.siklus_id}_${item.division_id}_${item.skor_akhir}`;
            if (!uniquePenilaian[key]) {
                uniquePenilaian[key] = item;
            }
        });

        // Group by division dari unique penilaian
        const divisiMap = {};
        Object.values(uniquePenilaian).forEach(item => {
            if (!item.division_id) return;

            if (!divisiMap[item.division_id]) {
                divisiMap[item.division_id] = {
                    nama: item.division_nama,
                    count: 0,
                    totalSkor: 0
                };
            }
            divisiMap[item.division_id].count++;
            divisiMap[item.division_id].totalSkor += parseFloat(item.skor_akhir);
        });

        // Convert to array dan sort
        const divisiArray = Object.values(divisiMap).map(div => ({
            nama: div.nama,
            count: div.count,
            avg: (div.totalSkor / div.count).toFixed(1)
        })).sort((a, b) => parseFloat(b.avg) - parseFloat(a.avg));

        // Clear table
        tableBody.innerHTML = '';

        if (divisiArray.length === 0) {
            tableBody.appendChild(emptyState);
        } else {
            divisiArray.forEach(div => {
                const avg = parseFloat(div.avg);
                let kategori, badge, textColor;

                if (avg >= 4.0) {
                    kategori = 'Sangat Baik';
                    badge = 'bg-green-100 text-green-800';
                    textColor = 'text-green-600';
                } else if (avg >= 3.5) {
                    kategori = 'Baik';
                    badge = 'bg-blue-100 text-blue-800';
                    textColor = 'text-blue-600';
                } else if (avg >= 3.0) {
                    kategori = 'Cukup Baik';
                    badge = 'bg-yellow-100 text-yellow-800';
                    textColor = 'text-yellow-600';
                } else {
                    kategori = 'Perlu Perbaikan';
                    badge = 'bg-red-100 text-red-800';
                    textColor = 'text-red-600';
                }

                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';
                row.innerHTML = `
                    <td class="px-4 lg:px-6 py-3 font-medium text-gray-900">${div.nama}</td>
                    <td class="px-4 lg:px-6 py-3 text-gray-700">${div.count}</td>
                    <td class="px-4 lg:px-6 py-3 font-semibold ${textColor}">${div.avg}</td>
                    <td class="px-4 lg:px-6 py-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${badge}">
                            ${kategori}
                        </span>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }
    }

    function updateActiveFilters(siklus, divisi) {
        const activeFilters = document.getElementById('activeFilters');
        activeFilters.innerHTML = '';
        let hasActiveFilters = false;

        if (siklus) {
            const siklusName = document.querySelector(`#siklusFilter option[value="${siklus}"]`).textContent;
            activeFilters.innerHTML += `
                <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                    Siklus: ${siklusName}
                    <button onclick="removeFilter('siklus')" class="ml-1 text-blue-600 hover:text-blue-800">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </span>
            `;
            hasActiveFilters = true;
        }

        if (divisi) {
            const divisiName = document.querySelector(`#divisiFilter option[value="${divisi}"]`).textContent;
            activeFilters.innerHTML += `
                <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                    Divisi: ${divisiName}
                    <button onclick="removeFilter('divisi')" class="ml-1 text-green-600 hover:text-green-800">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </span>
            `;
            hasActiveFilters = true;
        }

        activeFilters.classList.toggle('hidden', !hasActiveFilters);
    }

    function updateFilterInfo(siklus, divisi) {
        const filterInfo = document.getElementById('filterInfo');
        if (siklus || divisi) {
            filterInfo.innerHTML = '<strong>Filter aktif</strong> - data ditampilkan sesuai filter yang dipilih.';
        } else {
            filterInfo.innerHTML = '';
        }
    }

    function removeFilter(type) {
        if (type === 'siklus') {
            document.getElementById('siklusFilter').value = '';
        } else if (type === 'divisi') {
            document.getElementById('divisiFilter').value = '';
        }
        applyFilters();
    }

    function resetFilters() {
        document.getElementById('siklusFilter').value = '';
        document.getElementById('divisiFilter').value = '';
        applyFilters();
    }
</script>

@endsection