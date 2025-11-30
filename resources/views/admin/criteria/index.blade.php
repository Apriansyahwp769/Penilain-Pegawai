@extends('layouts.admin')

@section('page-title', 'Kriteria Penilaian')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Kriteria Penilaian</h1>
            <p class="text-gray-600 text-sm lg:text-base">Kelola kriteria dan bobot penilaian kinerja</p>
        </div>
        <button onclick="openCreateModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center space-x-2 w-full lg:w-auto">
            <i data-lucide="plus" class="w-4 h-4 lg:w-5 lg:h-5"></i>
            <span class="text-sm lg:text-base">Tambah Kriteria</span>
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

    <!-- Table Container -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[800px] lg:min-w-0">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Nama Kriteria</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Bobot</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Kategori</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Status</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($criteria as $criterion)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="font-medium text-gray-900 text-sm lg:text-base">{{ $criterion->name }}</div>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="text-gray-900 text-sm">{{ $criterion->weight }}%</div>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="text-gray-900 text-sm">{{ $criterion->category }}</div>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $criterion->status ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $criterion->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="flex items-center space-x-2">
                                <button onclick="editCriterion('{{ $criterion->id }}')" class="p-1 text-gray-400 hover:text-green-600 transition-colors">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </button>

                                <form action="{{ route('admin.criteria.destroy', $criterion) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kriteria ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-gray-400 hover:text-red-600 transition-colors">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 lg:px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center space-y-2">
                                <i data-lucide="list-checks" class="w-12 h-12 text-gray-300"></i>
                                <p class="text-sm lg:text-base">Belum ada kriteria penilaian</p>
                                <button onclick="openCreateModal()" class="text-blue-600 hover:text-blue-700 text-sm">
                                    Tambah kriteria pertama Anda
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
<div id="createModal" class="fixed inset-0 flex items-center justify-center z-50 p-4 hidden">
    <div class="bg-white rounded-xl w-full max-w-md border border-gray-200 shadow-lg">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Tambah Kriteria Baru</h2>
            <button onclick="closeCreateModal()" class="text-gray-500 hover:text-gray-700 transition-colors p-1 hover:bg-gray-100 rounded">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.criteria.store') }}" method="POST">
            @csrf
            <div class="p-4 space-y-4">
                <!-- Nama Kriteria -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kriteria</label>
                    <input
                        type="text"
                        name="name"
                        required
                        placeholder="Masukkan nama kriteria"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>

                <!-- Bobot -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bobot (%)</label>
                    <input
                        type="number"
                        name="weight"
                        required
                        min="0"
                        max="100"
                        step="0.01"
                        placeholder="0-100"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="Kuantitatif">Kuantitatif</option>
                        <option value="Kompetensi">Kompetensi</option>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex space-x-2 pt-2">
                    <button
                        type="button"
                        onclick="closeCreateModal()"
                        class="flex-1 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium">
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 flex items-center justify-center z-50 p-4 hidden">
    <div class="bg-white rounded-xl w-full max-w-md border border-gray-200 shadow-lg">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Edit Kriteria</h2>
            <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700 transition-colors p-1 hover:bg-gray-100 rounded">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Form -->
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="p-4 space-y-4">
                <!-- Nama Kriteria -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kriteria</label>
                    <input
                        type="text"
                        name="name"
                        id="edit_name"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>

                <!-- Bobot -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bobot (%)</label>
                    <input
                        type="number"
                        name="weight"
                        id="edit_weight"
                        required
                        min="0"
                        max="100"
                        step="0.01"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="category" id="edit_category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                         <option value="Kuantitatif">Kuantitatif</option>
                        <option value="Kompetensi">Kompetensi</option>
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="edit_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex space-x-2 pt-2">
                    <button
                        type="button"
                        onclick="closeEditModal()"
                        class="flex-1 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium">
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                        Update
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- <script>
    // Create Modal Functions
    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
    }

    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
    }

    // Edit Modal Functions
    function openEditModal(criteriaId) {
        // Fetch data criteria dan isi form
        fetch(`/admin/criteria/${criteriaId}/edit`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('edit_name').value = data.name;
                document.getElementById('edit_weight').value = data.weight;
                document.getElementById('edit_category').value = data.category;
                document.getElementById('edit_status').value = data.status ? '1' : '0';

                // Set form action
                document.getElementById('editForm').action = `/admin/criteria/${criteriaId}`;

                // Show modal
                document.getElementById('editModal').classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal memuat data kriteria');
            });
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    // Close modal ketika klik outside
    document.getElementById('createModal').addEventListener('click', function(e) {
        if (e.target === this) closeCreateModal();
    });

    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditModal();
    });
    // Edit Modal Functions
    function editCriterion(criteriaId) {
        // Fetch data criteria dan isi form
        fetch(`/admin/criteria/${criteriaId}/edit`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Data received:', data);

                document.getElementById('edit_name').value = data.name || '';
                document.getElementById('edit_weight').value = data.weight || '';
                document.getElementById('edit_category').value = data.category || 'Technical';
                document.getElementById('edit_status').value = data.status ? '1' : '0';

                // Set form action
                document.getElementById('editForm').action = `/admin/criteria/${criteriaId}`;

                // Show modal
                document.getElementById('editModal').classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error fetching criteria data:', error);
                alert('Gagal memuat data kriteria: ' + error.message);
            });
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }
</script> -->

<script>
    // Create Modal Functions
    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
    }

    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
    }

    // EDIT CRITERIA
    function editCriterion(criteriaId) {
        fetch(`/admin/criteria/${criteriaId}/edit`)
            .then(response => response.json())
            .then(data => {
                // Isi field modal edit
                document.getElementById('edit_name').value = data.name;
                document.getElementById('edit_weight').value = data.weight;
                document.getElementById('edit_category').value = data.category;
                document.getElementById('edit_status').value = data.status ? '1' : '0';

                // Ubah action form
                document.getElementById('editForm').action = `/admin/criteria/${criteriaId}`;

                // Tampilkan modal
                document.getElementById('editModal').classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal memuat data kriteria');
            });
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    // Close modal ketika klik area hitam
    document.getElementById('createModal').addEventListener('click', function(e) {
        if (e.target === this) closeCreateModal();
    });

    document.getElementById('editModal').addEventListener('click', function(e) {
        if (e.target === this) closeEditModal();
    });
</script>

@endsection