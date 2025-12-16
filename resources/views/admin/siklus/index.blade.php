<!-- Siklus -> Periode -->
@extends('layouts.admin')

@section('page-title', 'Periode Penilaian')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Periode Penilaian</h1>
            <p class="text-gray-600 text-sm lg:text-base">Kelola periode dan periode penilaian kinerja</p>
        </div>
        <button onclick="openCreateModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center space-x-2 w-full lg:w-auto">
            <i data-lucide="plus" class="w-4 h-4 lg:w-5 lg:h-5"></i>
            <span class="text-sm lg:text-base">Buat Periode Baru</span>
        </button>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
        <span>{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
        <span>{{ session('error') }}</span>
        <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Table Container -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px] lg:min-w-0">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Nama Periode</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Tanggal Mulai</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Deadline</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Finalisasi</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Status</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($siklusList as $siklus)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="font-medium text-gray-900 text-sm lg:text-base">{{ $siklus->nama }}</div>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="text-gray-900 text-sm">
                                {{ $siklus->tanggal_mulai ? $siklus->tanggal_mulai->format('d/m/Y') : '-' }}
                            </div>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="text-gray-900 text-sm">
                                {{ $siklus->tanggal_selesai ? $siklus->tanggal_selesai->format('d/m/Y') : '-' }}
                            </div>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="text-gray-900 text-sm">
                                {{ $siklus->tanggal_finalisasi ? $siklus->tanggal_finalisasi->format('d/m/Y') : '-' }}
                            </div>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            @php
                            $statusColors = [
                            'draft' => 'bg-gray-100 text-gray-800',
                            'active' => 'bg-green-100 text-green-800',
                            'completed' => 'bg-blue-100 text-blue-800'
                            ];
                            $statusText = [
                            'draft' => 'Draft',
                            'active' => 'Aktif',
                            'completed' => 'Selesai'
                            ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$siklus->status] }}">
                                {{ $statusText[$siklus->status] }}
                            </span>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="flex items-center space-x-2">
                                <button onclick="editSiklus('{{ $siklus->id }}')"
                                    class="p-1 text-gray-400 hover:text-green-600 transition-colors"
                                    title="Edit">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </button>

                                <form action="{{ route('admin.siklus.destroy', $siklus->id) }}"
                                    method="POST"
                                    class="inline"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus siklus \'{{ $siklus->nama }}\'?\n\nData yang dihapus tidak dapat dikembalikan.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="p-1 text-gray-400 hover:text-red-600 transition-colors"
                                        title="Hapus">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 lg:px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center space-y-2">
                                <i data-lucide="calendar-x" class="w-12 h-12 text-gray-300"></i>
                                <p class="text-sm lg:text-base">Belum ada periode penilaian</p>
                                <button onclick="openCreateModal()" class="text-blue-600 hover:text-blue-700 text-sm">
                                    Buat siklus pertama Anda
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div id="createModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-900">Buat Periode Baru</h2>
            <button onclick="closeCreateModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('admin.siklus.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Periode</label>
                <input type="text" name="nama" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Contoh: Periode 2025 Januari">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Finalisasi</label>
                <input type="date" name="tanggal_finalisasi" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="draft">Draft</option>
                    <option value="active">Aktif</option>
                    <option value="completed">Selesai</option>
                </select>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeCreateModal()"
                    class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-900">Edit Siklus</h2>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="editForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Periode</label>
                <input type="text" id="edit_nama" name="nama" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                    <input type="date" id="edit_tanggal_mulai" name="tanggal_mulai" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                    <input type="date" id="edit_tanggal_selesai" name="tanggal_selesai" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Finalisasi</label>
                <input type="date" id="edit_tanggal_finalisasi" name="tanggal_finalisasi" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="edit_status" name="status" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="draft">Draft</option>
                    <option value="active">Aktif</option>
                    <option value="completed">Selesai</option>
                </select>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeEditModal()"
                    class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Modal Functions
    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => lucide.createIcons(), 100);
    }

    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function openEditModal() {
        document.getElementById('editModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => lucide.createIcons(), 100);
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Edit Siklus Function
    async function editSiklus(id) {
        try {
            const response = await fetch(`/admin/siklus/${id}/edit`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const data = await response.json();
            
            console.log('Data received:', data); // Debug log
            
            // Populate form with safe checks
            document.getElementById('edit_nama').value = data.nama || '';
            document.getElementById('edit_tanggal_mulai').value = data.tanggal_mulai || '';
            document.getElementById('edit_tanggal_selesai').value = data.tanggal_selesai || '';
            document.getElementById('edit_tanggal_finalisasi').value = data.tanggal_finalisasi || '';
            document.getElementById('edit_status').value = data.status || 'draft';
            
            // Update form action
            document.getElementById('editForm').action = `/admin/siklus/${id}`;
            
            // Open modal
            openEditModal();
        } catch (error) {
            console.error('Error fetching siklus data:', error);
            alert('Gagal mengambil data siklus. Error: ' + error.message);
        }
    }

    // Delete Confirmation
    function confirmDelete(button) {
        const siklusName = button.getAttribute('data-siklus-name');
        const form = button.closest('form');
        
        if (confirm(`Apakah Anda yakin ingin menghapus siklus "${siklusName}"?\n\nData yang dihapus tidak dapat dikembalikan.`)) {
            // Disable button to prevent double submission
            button.disabled = true;
            form.submit();
        }
    }

    // Close modal when clicking outside
    document.getElementById('createModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeCreateModal();
    });

    document.getElementById('editModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeEditModal();
    });

    // Initialize Lucide icons on page load
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>

@endsection