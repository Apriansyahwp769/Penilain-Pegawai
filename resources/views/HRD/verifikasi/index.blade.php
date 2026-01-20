{{-- resources/views/hrd/verifikasi/index.blade.php --}}
@extends('layouts.hrd')

@section('page-title', 'Verifikasi Penilaian')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900">🔍 Verifikasi Penilaian</h1>
            <p class="text-gray-600 text-sm lg:text-base">Tinjau dan verifikasi penilaian dari Ketua Divisi</p>
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

    <!-- Table Container -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] lg:min-w-0">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Pegawai</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Jabatan</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Skor Akhir</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Status</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Siklus</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Dinilai</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($employees as $emp)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center">
                                    <i data-lucide="user" class="text-white w-4 h-4"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 text-sm lg:text-base">{{ $emp['name'] }}</div>
                                    <div class="text-gray-500 text-xs">NIP: {{ $emp['nip'] }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="text-gray-900 text-sm">{{ $emp['position'] }}</div>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            @if($emp['skor_akhir'])
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 text-xs font-medium">
                                    {{ number_format($emp['skor_akhir'], 2) }}
                                </span>
                            @else
                                <span class="text-gray-400 text-sm">—</span>
                            @endif
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $emp['status_badge'] }}">
                                {{ $emp['status_label'] }}
                            </span>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="text-gray-900 text-sm">{{ $emp['siklus'] }}</div>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="text-gray-600 text-xs">{{ $emp['updated_at'] }}</div>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            @if($emp['status_label'] === 'Menunggu Verifikasi')
                                <a href="{{ route('hrd.verifikasi.show', $emp['penilaian_id']) }}" 
                                   class="p-1 text-gray-400 hover:text-blue-600 transition-colors"
                                   title="Verifikasi Penilaian">
                                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                                </a>
                            @else
                                <a href="{{ route('hrd.verifikasi.show', $emp['penilaian_id']) }}" 
                                   class="p-1 text-gray-400 hover:text-green-600 transition-colors"
                                   title="Lihat Detail">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i data-lucide="inbox" class="w-12 h-12 text-gray-300 mb-2"></i>
                                <p>Tidak ada penilaian dari siklus aktif</p>
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
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });
</script>
@endsection