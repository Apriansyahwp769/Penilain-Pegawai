@extends('layouts.staff')

@section('page-title', 'Riwayat Kinerja')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900 flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <span>Riwayat Kinerja</span>
            </h1>
            <p class="text-gray-600 text-sm">Analisis kinerja dari waktu ke waktu</p>
        </div>
    </div>

    <!-- Filter Kategori -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('staff.riwayat.index') }}" class="flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
            <label class="text-sm font-medium text-gray-700">Filter Kategori:</label>
            <select name="kategori" onchange="this.form.submit()" class="w-full sm:w-64 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                <option value="">Semua Kategori</option>
                @foreach(['Bagus', 'Normal', 'Rendah'] as $kat)
                <option value="{{ $kat }}" {{ request('kategori') == $kat ? 'selected' : '' }}>
                    {{ $kat }}
                </option>
                @endforeach
            </select>
        </form>
    </div>

    @if($riwayat->count() > 0)
    <!-- Grafik Tren Kinerja -->
    @if($chartData->isNotEmpty())
    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-2">Grafik Tren Kinerja</h2>
        <p class="text-gray-600 text-sm mb-4">Perkembangan Skor Akhir dari {{ $chartData->count() }} periode</p>

        <div class="relative h-64">
            <canvas id="performanceChart"></canvas>
        </div>
    </div>
    @endif

    <!-- Tabel Riwayat -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-2">Tabel Riwayat</h2>
        <p class="text-gray-600 text-sm mb-4">Daftar lengkap hasil penilaian per periode</p>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[600px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Periode</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Skor Akhir</th>
                        <!-- Kolom "Kategori" dihapus -->
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Status</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-900">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($riwayat as $item)
                    <tr>
                        <td class="px-4 py-3 font-semibold text-gray-900">{{ $item['periode'] }}</td>
                        <td class="px-4 py-3 font-bold 
                                @if($item['status_label'] == 'Bagus') text-green-600
                                @elseif($item['status_label'] == 'Normal') text-blue-600
                                @else text-red-600
                                @endif">
                            {{ number_format($item['skor_akhir'], 1) }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-3 py-1 {{ $item['status_badge'] }} text-xs font-medium rounded-full">
                                {{ $item['status_label'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('staff.riwayat.show', $item['penilaian_id']) }}"
                                class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200 transition-colors"
                                title="Lihat Detail">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tip Bantuan -->
    <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
        <div class="flex items-start space-x-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm text-blue-800">
                Klik icon mata untuk membuka Halaman Hasil Penilaian dari periode historis tersebut.
            </p>
        </div>
    </div>

    @else
    <div class="bg-yellow-50 border border-yellow-300 rounded-xl p-8 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-yellow-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Riwayat</h3>
        <p class="text-gray-600">
            Anda belum memiliki riwayat penilaian yang selesai.
        </p>
    </div>
    @endif

</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    @if($chartData->isNotEmpty())
    const chartData = @json($chartData);
    document.addEventListener('DOMContentLoaded', function () {
        const labels = chartData.map(item => item.periode);
        const scores = chartData.map(item => parseFloat(item.skor));

        // Gradient untuk garis & area
        const ctx = document.getElementById('performanceChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(59, 130, 246, 0.4)');
        gradient.addColorStop(1, 'rgba(59, 130, 246, 0.05)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Skor Kinerja',
                    data: scores,
                    borderColor: '#4F46E5', // indigo-600
                    backgroundColor: gradient,
                    borderWidth: 4,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#FFFFFF',
                    pointBorderColor: '#4F46E5',
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 9,
                    pointHoverBackgroundColor: '#4F46E5',
                    pointHoverBorderColor: '#FFFFFF',
                    pointHoverBorderWidth: 3,
                    cubicInterpolationMode: 'monotone'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1E293B', // slate-800
                        titleColor: '#F1F5F9', // slate-100
                        bodyColor: '#E2E8F0', // slate-200
                        padding: 14,
                        borderRadius: 12,
                        displayColors: false,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed.y;
                                let status = 'Rendah';
                                if (value >= 4.0) status = 'Bagus';
                                else if (value >= 3.0) status = 'Normal';
                                
                                return `Skor: ${value.toFixed(1)} (${status})`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        min: 0,
                        max: 5,
                        ticks: {
                            stepSize: 1,
                            color: '#94A3B8', // slate-400
                            font: {
                                size: 12
                            },
                            callback: function(value) {
                                return value.toFixed(1);
                            }
                        },
                        grid: {
                            color: 'rgba(148, 163, 184, 0.15)'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#64748B', // slate-500
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                animation: {
                    duration: 1200,
                    easing: 'easeOutQuart'
                }
            }
        });
    });
    @endif
</script>
@endsection