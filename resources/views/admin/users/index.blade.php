@extends('layouts.admin')

@section('page-title', 'Manajemen User')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Manajemen User</h1>
            <p class="text-gray-600 text-sm lg:text-base">Kelola data pengguna dan akses sistem</p>
        </div>
        <button onclick="openCreateModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center space-x-2 w-full lg:w-auto">
            <i data-lucide="user-plus" class="w-4 h-4 lg:w-5 lg:h-5"></i>
            <span class="text-sm lg:text-base">Tambah User</span>
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

    <!-- Filter Section -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <div class="flex flex-col lg:flex-row lg:items-center space-y-2 lg:space-y-0 lg:space-x-4">
                <!-- Filter Divisi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter Divisi</label>
                    <select id="divisionFilter" onchange="filterUsers()" class="w-full lg:w-48 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="">Semua Divisi</option>
                        @foreach($divisions as $division)
                            <option value="{{ $division->id }}">{{ $division->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Role -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter Role</label>
                    <select id="roleFilter" onchange="filterUsers()" class="w-full lg:w-48 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="">Semua Role</option>
                        <option value="admin">Admin</option>
                        <option value="ketua_divisi">Ketua Divisi</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>

                <!-- Filter Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter Status</label>
                    <select id="statusFilter" onchange="filterUsers()" class="w-full lg:w-48 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="">Semua Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
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
            <!-- Active filters akan ditampilkan di sini -->
        </div>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[1000px] lg:min-w-0">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">User</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Email</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Divisi</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Jabatan</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Role</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Status</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="usersTableBody">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors user-row" 
                        data-division="{{ $user->division_id }}" 
                        data-role="{{ $user->role }}"
                        data-status="{{ $user->is_active ? 'active' : 'inactive' }}">
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                                    <i data-lucide="user" class="text-white w-4 h-4"></i>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 text-sm lg:text-base">{{ $user->name }}</div>
                                    @if($user->nip)
                                        <div class="text-gray-500 text-xs">{{ $user->nip }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="text-gray-900 text-sm">{{ $user->email }}</div>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="text-gray-900 text-sm">{{ $user->division->name ?? '-' }}</div>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="text-gray-900 text-sm">{{ $user->position->name ?? '-' }}</div>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            @php
                                $roleColors = [
                                    'admin' => 'bg-purple-100 text-purple-800',
                                    'ketua_divisi' => 'bg-blue-100 text-blue-800',
                                    'staff' => 'bg-gray-100 text-gray-800'
                                ];
                                $roleText = [
                                    'admin' => 'Admin',
                                    'ketua_divisi' => 'Ketua Divisi', 
                                    'staff' => 'Staff'
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $roleColors[$user->role] }}">
                                {{ $roleText[$user->role] }}
                            </span>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="flex items-center space-x-2">
                                <button onclick="editUser('{{ $user->id }}')" class="p-1 text-gray-400 hover:text-green-600 transition-colors">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </button>
                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-gray-400 hover:text-red-600 transition-colors">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 lg:px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center space-y-2">
                                <i data-lucide="users" class="w-12 h-12 text-gray-300"></i>
                                <p class="text-sm lg:text-base">Belum ada user</p>
                                <button onclick="openCreateModal()" class="text-blue-600 hover:text-blue-700 text-sm">
                                    Tambah user pertama
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
        <div class="px-4 py-3 border-t border-gray-200">
            {{ $users->links('vendor.pagination.tailwind') }}
        </div>
        @endif
    </div>
</div>

<!-- Create Modal -->
<div id="createModal" class="fixed inset-0 flex items-center justify-center z-50 p-4 hidden">
    <div class="bg-white rounded-xl w-full max-w-2xl border border-gray-200 shadow-lg">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Tambah User Baru</h2>
            <button onclick="closeCreateModal()" class="text-gray-500 hover:text-gray-700 transition-colors p-1 hover:bg-gray-100 rounded">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="p-4 space-y-4 max-h-[70vh] overflow-y-auto">
                <!-- Error Messages -->
                @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <!-- Nama -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                        <input
                            type="text"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            placeholder="Masukkan nama lengkap"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm"
                        >
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            placeholder="email@example.com"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm"
                        >
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password *</label>
                        <input
                            type="password"
                            name="password"
                            required
                            placeholder="Minimal 8 karakter"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm"
                        >
                    </div>

                    <!-- NIP -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                        <input
                            type="text"
                            name="nip"
                            value="{{ old('nip') }}"
                            placeholder="Nomor Induk Pegawai"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm"
                        >
                    </div>

                    <!-- Divisi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Divisi</label>
                        <select name="division_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="">Pilih Divisi</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}" {{ old('division_id') == $division->id ? 'selected' : '' }}>{{ $division->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Jabatan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                        <select name="position_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="">Pilih Jabatan</option>
                            @foreach($positions as $position)
                                <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>{{ $position->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Telepon -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                        <input
                            type="text"
                            name="phone"
                            value="{{ old('phone') }}"
                            placeholder="Nomor telepon"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm"
                        >
                    </div>

                    <!-- Tanggal Bergabung -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bergabung</label>
                        <input
                            type="date"
                            name="join_date"
                            value="{{ old('join_date') }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm"
                        >
                    </div>

                    <!-- Role -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                        <select name="role" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="ketua_divisi" {{ old('role') == 'ketua_divisi' ? 'selected' : '' }}>Ketua Divisi</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                        <select name="is_active" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex space-x-2 pt-4">
                    <button
                        type="button"
                        onclick="closeCreateModal()"
                        class="flex-1 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium"
                    >
                        Simpan User
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 flex items-center justify-center z-50 p-4 hidden">
    <div class="bg-white rounded-xl w-full max-w-2xl border border-gray-200 shadow-lg">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Edit User</h2>
            <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700 transition-colors p-1 hover:bg-gray-100 rounded">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Form -->
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="p-4 space-y-4 max-h-[70vh] overflow-y-auto">
                <!-- Error Messages -->
                <div id="editErrors" class="hidden bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                    <ul id="editErrorList" class="list-disc pl-5 space-y-1"></ul>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <!-- Nama -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                        <input
                            type="text"
                            name="name"
                            id="edit_name"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm"
                        >
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input
                            type="email"
                            name="email"
                            id="edit_email"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm"
                        >
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input
                            type="password"
                            name="password"
                            id="edit_password"
                            placeholder="Kosongkan jika tidak ingin mengubah"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm"
                        >
                    </div>

                    <!-- NIP -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIP</label>
                        <input
                            type="text"
                            name="nip"
                            id="edit_nip"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm"
                        >
                    </div>

                    <!-- Divisi -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Divisi</label>
                        <select name="division_id" id="edit_division_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="">Pilih Divisi</option>
                            @foreach($divisions as $division)
                                <option value="{{ $division->id }}">{{ $division->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Jabatan -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan</label>
                        <select name="position_id" id="edit_position_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="">Pilih Jabatan</option>
                            @foreach($positions as $position)
                                <option value="{{ $position->id }}">{{ $position->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Telepon -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                        <input
                            type="text"
                            name="phone"
                            id="edit_phone"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm"
                        >
                    </div>

                    <!-- Tanggal Bergabung -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bergabung</label>
                        <input
                            type="date"
                            name="join_date"
                            id="edit_join_date"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm"
                        >
                    </div>

                    <!-- Role -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Role *</label>
                        <select name="role" id="edit_role" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="staff">Staff</option>
                            <option value="ketua_divisi">Ketua Divisi</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                        <select name="is_active" id="edit_is_active" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex space-x-2 pt-4">
                    <button
                        type="button"
                        onclick="closeEditModal()"
                        class="flex-1 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium"
                    >
                        Batal
                    </button>
                    <button
                        type="submit"
                        class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium"
                    >
                        Update User
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
// Pagination and filter already working.

// Filter Functions
function filterUsers() {
    const divisionFilter = document.getElementById('divisionFilter').value;
    const roleFilter = document.getElementById('roleFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    
    const userRows = document.querySelectorAll('.user-row');
    let visibleCount = 0;
    
    updateActiveFilters(divisionFilter, roleFilter, statusFilter);
    
    userRows.forEach(row => {
        const division = row.getAttribute('data-division');
        const role = row.getAttribute('data-role');
        const status = row.getAttribute('data-status');
        
        let showRow = true;
        if (divisionFilter && division !== divisionFilter) showRow = false;
        if (roleFilter && role !== roleFilter) showRow = false;
        if (statusFilter && status !== statusFilter) showRow = false;
        
        row.style.display = showRow ? '' : 'none';
        if (showRow) visibleCount++;
    });
    
    const emptyState = document.querySelector('tbody tr:only-child');
    if (emptyState && emptyState.querySelector('.text-gray-500')) {
        emptyState.style.display = visibleCount === 0 ? '' : 'none';
    }
}

function updateActiveFilters(division, role, status) {
    const activeFilters = document.getElementById('activeFilters');
    activeFilters.innerHTML = '';
    let hasActiveFilters = false;
    
    if (division) {
        const divisionName = document.querySelector(`#divisionFilter option[value="${division}"]`).textContent;
        activeFilters.innerHTML += `<span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Divisi: ${divisionName}<button onclick="removeFilter('division')" class="ml-1 text-blue-600 hover:text-blue-800"><i data-lucide="x" class="w-3 h-3"></i></button></span>`;
        hasActiveFilters = true;
    }
    
    if (role) {
        const roleText = role === 'admin' ? 'Admin' : role === 'ketua_divisi' ? 'Ketua Divisi' : 'Staff';
        activeFilters.innerHTML += `<span class="inline-flex items-center px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">Role: ${roleText}<button onclick="removeFilter('role')" class="ml-1 text-purple-600 hover:text-purple-800"><i data-lucide="x" class="w-3 h-3"></i></button></span>`;
        hasActiveFilters = true;
    }
    
    if (status) {
        const statusText = status === 'active' ? 'Active' : 'Inactive';
        activeFilters.innerHTML += `<span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Status: ${statusText}<button onclick="removeFilter('status')" class="ml-1 text-green-600 hover:text-green-800"><i data-lucide="x" class="w-3 h-3"></i></button></span>`;
        hasActiveFilters = true;
    }
    
    activeFilters.classList.toggle('hidden', !hasActiveFilters);
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function removeFilter(filterType) {
    switch(filterType) {
        case 'division': document.getElementById('divisionFilter').value = ''; break;
        case 'role': document.getElementById('roleFilter').value = ''; break;
        case 'status': document.getElementById('statusFilter').value = ''; break;
    }
    filterUsers();
}

function resetFilters() {
    document.getElementById('divisionFilter').value = '';
    document.getElementById('roleFilter').value = '';
    document.getElementById('statusFilter').value = '';
    filterUsers();
}

// Modal Functions
function openCreateModal() { document.getElementById('createModal').classList.remove('hidden'); }
function closeCreateModal() { document.getElementById('createModal').classList.add('hidden'); }

function editUser(id) {
    console.log('Fetching user data for ID:', id);
    
    fetch(`/admin/users/${id}/json`)
        .then(res => {
            console.log('Response status:', res.status);
            if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            return res.json();
        })
        .then(data => {
            console.log('User data received:', data);
            
            // Set form action
            document.getElementById('editForm').action = `/admin/users/${id}`;
            
            // Fill form fields
            document.getElementById('edit_name').value = data.name || '';
            document.getElementById('edit_email').value = data.email || '';
            document.getElementById('edit_nip').value = data.nip || '';
            document.getElementById('edit_phone').value = data.phone || '';
            document.getElementById('edit_division_id').value = data.division_id || '';
            document.getElementById('edit_position_id').value = data.position_id || '';
            document.getElementById('edit_role').value = data.role || 'staff';
            document.getElementById('edit_join_date').value = data.join_date || '';
            document.getElementById('edit_is_active').value = data.is_active ? '1' : '0';
            
            // Clear password field
            document.getElementById('edit_password').value = '';
            
            // Hide errors
            document.getElementById('editErrors').classList.add('hidden');
            
            // Show modal
            document.getElementById('editModal').classList.remove('hidden');
            
            console.log('Modal opened successfully');
        })
        .catch(err => {
            console.error('Error fetching user data:', err);
            alert("Gagal memuat data user. Silakan coba lagi.");
        });
}

function closeEditModal() { 
    document.getElementById('editModal').classList.add('hidden'); 
    document.getElementById('editErrors').classList.add('hidden');
}

// Fixed modal backdrop click handlers
document.getElementById('createModal').addEventListener('click', function(e) { 
    if (e.target === this) closeCreateModal(); 
});

document.getElementById('editModal').addEventListener('click', function(e) { 
    if (e.target === this) closeEditModal(); 
});

document.addEventListener('DOMContentLoaded', () => {
    filterUsers();
    
    // Handle edit form submission with error display
    document.getElementById('editForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const url = this.action;
        
        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else if (data.errors) {
                const errorDiv = document.getElementById('editErrors');
                const errorList = document.getElementById('editErrorList');
                errorList.innerHTML = '';
                for (let field in data.errors) {
                    data.errors[field].forEach(msg => {
                        const li = document.createElement('li');
                        li.textContent = msg;
                        errorList.appendChild(li);
                    });
                }
                errorDiv.classList.remove('hidden');
            } else {
                alert('Terjadi kesalahan');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Gagal mengirim data');
        });
    });
});
</script>
@endsection