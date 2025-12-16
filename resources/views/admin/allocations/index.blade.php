{{-- resources/views/admin/allocations/index.blade.php --}}
@extends('layouts.admin')

@section('page-title', 'Alokasi Penilaian')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Alokasi Penilaian</h1>
            <p class="text-gray-600 text-sm lg:text-base">Kelola alokasi penilaian antara penilai dan dinilai</p>
        </div>
        <button onclick="openCreateModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center space-x-2 w-full lg:w-auto">
            <i data-lucide="plus" class="w-4 h-4 lg:w-5 lg:h-5"></i>
            <span class="text-sm lg:text-base">Tambah Alokasi</span>
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
                <!-- Filter Siklus -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter Periode</label>
                    <select id="siklusFilter" onchange="filterAllocations()" class="w-full lg:w-48 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="">Semua Periode</option>
                        @foreach($siklusList as $siklus)
                            <option value="{{ $siklus->id }}">{{ $siklus->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filter Status</label>
                    <select id="statusFilter" onchange="filterAllocations()" class="w-full lg:w-48 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
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
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Siklus</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Penilai</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Dinilai</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Status</th>
                        <th class="px-4 lg:px-6 py-3 lg:py-4 text-left text-xs lg:text-sm font-semibold text-gray-900">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="allocationsTableBody">
                    @forelse($allocations as $allocation)
                    <tr class="hover:bg-gray-50 transition-colors allocation-row" 
                        data-siklus="{{ $allocation->siklus_id }}" 
                        data-status="{{ $allocation->status }}">
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center">
                                    <i data-lucide="calendar" class="text-white w-4 h-4"></i>
                                </div>
                                <div class="font-medium text-gray-900 text-sm lg:text-base">{{ $allocation->siklus->nama }}</div>
                            </div>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="text-gray-900 text-sm font-medium">{{ $allocation->penilai->name }}</div>
                            <div class="text-gray-500 text-xs">{{ $allocation->penilai->position->name ?? '-' }}</div>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="text-gray-900 text-sm font-medium">{{ $allocation->dinilai->name }}</div>
                            <div class="text-gray-500 text-xs">{{ $allocation->dinilai->position->name ?? '-' }}</div>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'in_progress' => 'bg-blue-100 text-blue-800',
                                    'completed' => 'bg-green-100 text-green-800'
                                ];
                                $statusText = [
                                    'pending' => 'Pending',
                                    'in_progress' => 'In Progress',
                                    'completed' => 'Completed'
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$allocation->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusText[$allocation->status] ?? ucfirst($allocation->status) }}
                            </span>
                        </td>
                        <td class="px-4 lg:px-6 py-3 lg:py-4">
                            <div class="flex items-center space-x-2">
                                <button onclick="editAllocation('{{ $allocation->id }}')" class="p-1 text-gray-400 hover:text-green-600 transition-colors">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </button>
                                <button onclick="openDeleteModal('{{ $allocation->id }}')" class="p-1 text-gray-400 hover:text-red-600 transition-colors">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-4 lg:px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center space-y-2">
                                <i data-lucide="clipboard-list" class="w-12 h-12 text-gray-300"></i>
                                <p class="text-sm lg:text-base">Belum ada alokasi penilaian</p>
                                <button onclick="openCreateModal()" class="text-blue-600 hover:text-blue-700 text-sm">
                                    Tambah alokasi pertama
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
    <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeCreateModal()"></div>
    <div class="bg-white rounded-xl w-full max-w-lg border border-gray-200 shadow-lg relative z-10">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Tambah Alokasi</h2>
            <button onclick="closeCreateModal()" class="text-gray-500 hover:text-gray-700 transition-colors p-1 hover:bg-gray-100 rounded">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.allocations.store') }}" method="POST">
            @csrf
            <div class="p-4 space-y-4">
                <!-- Siklus -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periode *</label>
                    <select name="siklus_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="">Pilih Periode</option>
                        @foreach ($siklusList as $s)
                            <option value="{{ $s->id }}">{{ $s->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Penilai -->
                <!-- <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Penilai (Ketua Divisi) *</label>
                    <select name="penilai_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="">Pilih Penilai</option>
                        @foreach ($penilaiList as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->position->name ?? '-' }})</option>
                        @endforeach
                    </select>
                </div> -->

                <!-- Dinilai -->
                <!-- <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dinilai (Staff) *</label>
                    <select name="dinilai_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="">Pilih Staff</option>
                        @foreach ($dinilaiList as $d)
                            <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->position->name ?? '-' }})</option>
                        @endforeach
                    </select>
                </div> -->

                <!-- Actions -->
                <div class="flex space-x-2 pt-4">
                    <button type="button" onclick="closeCreateModal()" class="flex-1 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                        Simpan Alokasi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 flex items-center justify-center z-50 p-4 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeEditModal()"></div>
    <div class="bg-white rounded-xl w-full max-w-lg border border-gray-200 shadow-lg relative z-10">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Edit Alokasi</h2>
            <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700 transition-colors p-1 hover:bg-gray-100 rounded">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Form -->
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="p-4 space-y-4">
                <!-- Siklus -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periode *</label>
                    <select name="siklus_id" id="edit_siklus_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="">Pilih Periode</option>
                        @foreach ($siklusList as $s)
                            <option value="{{ $s->id }}">{{ $s->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Penilai -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Penilai (Ketua Divisi) *</label>
                    <select name="penilai_id" id="edit_penilai_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="">Pilih Penilai</option>
                        @foreach ($penilaiList as $p)
                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->position->name ?? '-' }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Dinilai -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Dinilai (Staff) *</label>
                    <select name="dinilai_id" id="edit_dinilai_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="">Pilih Staff</option>
                        @foreach ($dinilaiList as $d)
                            <option value="{{ $d->id }}">{{ $d->name }} ({{ $d->position->name ?? '-' }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                    <select name="status" id="edit_status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex space-x-2 pt-4">
                    <button type="button" onclick="closeEditModal()" class="flex-1 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                        Update Alokasi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Delete Modal -->
<div id="deleteModal" class="fixed inset-0 flex items-center justify-center z-50 p-4 hidden">
    <div class="fixed inset-0 bg-black bg-opacity-50" onclick="closeDeleteModal()"></div>
    <div class="bg-white rounded-xl w-full max-w-sm border border-gray-200 shadow-lg relative z-10">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Hapus Alokasi</h2>
            <button onclick="closeDeleteModal()" class="text-gray-500 hover:text-gray-700 transition-colors p-1 hover:bg-gray-100 rounded">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Content -->
        <div class="p-4">
            <p class="text-gray-600 mb-4">Apakah Anda yakin ingin menghapus alokasi ini? Tindakan ini tidak dapat dibatalkan.</p>
            
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex space-x-2">
                    <button type="button" onclick="closeDeleteModal()" class="flex-1 px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-medium">
                        Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// -----------------------------
// GENERAL: Re-init lucide if ada
// -----------------------------
function tryInitLucide(){
    if (typeof lucide !== 'undefined') {
        try { lucide.createIcons(); } catch(e){/* ignore */ }
    }
}

// -----------------------------
// CREATE MODAL (sesuai blade)
// -----------------------------
function openCreateModal(){
    const modal = document.getElementById('createModal');
    if (!modal) return;
    modal.classList.remove('hidden');
    tryInitLucide();
}
function closeCreateModal(){
    const modal = document.getElementById('createModal');
    if (!modal) return;
    modal.classList.add('hidden');
}

// -----------------------------
// EDIT: fungsi yang dipanggil tombol di blade
// -----------------------------
function editAllocation(id){
    // fallback safety
    if (!id) {
        console.error('editAllocation: id kosong');
        return;
    }
    // panggil fungsi fetch yang sama (bisa di-inline)
    fetch(`/admin/allocations/${id}/edit`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => {
        // jika server mengembalikan HTML (blade), res.json() akan gagal
        if (!res.ok) throw new Error('Network response was not ok');
        return res.json();
    })
    .then(payload => {
        // dua pola return yang mungkin: langsung allocation, atau objek { success, allocation }
        const data = payload.allocation ?? payload;
        // jika masih ada wrapper success
        // guard: kalau server mengembalikan { success: false }
        if (payload.success === false) {
            alert(payload.message || 'Gagal mengambil data alokasi');
            return;
        }

        // Isi form edit. Pastikan elemen ada di DOM (sesuai blade yang kamu kirim)
        const editForm = document.getElementById('editForm');
        if (editForm) {
            editForm.action = `/admin/allocations/${id}`;
        }

        const siklusEl = document.getElementById('edit_siklus_id');
        const penilaiEl = document.getElementById('edit_penilai_id');
        const dinilaiEl = document.getElementById('edit_dinilai_id');
        const statusEl  = document.getElementById('edit_status');

        if (siklusEl) siklusEl.value = data.siklus_id ?? '';
        if (penilaiEl) penilaiEl.value = data.penilai_id ?? '';
        if (dinilaiEl) dinilaiEl.value = data.dinilai_id ?? '';
        if (statusEl)  statusEl.value  = data.status ?? 'pending';

        // show modal
        const editModal = document.getElementById('editModal');
        if (editModal) {
            editModal.classList.remove('hidden');
            tryInitLucide();
        }
    })
    .catch(err => {
        console.error('editAllocation error:', err);
        // jika server mereturn HTML (blade) karena edit() belum detect AJAX,
        // browser akan gagal parse JSON -> muncul error di console.
        alert('Gagal memuat data edit. Pastikan route edit mengembalikan JSON jika request AJAX.');
    });
}

// -----------------------------
// Tutup modal edit
// -----------------------------
function closeEditModal(){
    const modal = document.getElementById('editModal');
    if (!modal) return;
    modal.classList.add('hidden');
}

// -----------------------------
// DELETE modal handler (sesuai blade)
// -----------------------------
function openDeleteModal(id){
    const deleteForm = document.getElementById('deleteForm');
    if (deleteForm) deleteForm.action = `/admin/allocations/${id}`;
    const modal = document.getElementById('deleteModal');
    if (modal) modal.classList.remove('hidden');
    tryInitLucide();
}
function closeDeleteModal(){
    const modal = document.getElementById('deleteModal');
    if (modal) modal.classList.add('hidden');
}

// -----------------------------
// Close modal when click outside overlay
// -----------------------------
window.addEventListener('click', function(e){
    const createModal = document.getElementById('createModal');
    const editModal = document.getElementById('editModal');
    const deleteModal = document.getElementById('deleteModal');

    // ketika event.target sama dengan container modal (yang memiliki kelas fixed inset-0)
    if (createModal && e.target === createModal) closeCreateModal();
    if (editModal && e.target === editModal) closeEditModal();
    if (deleteModal && e.target === deleteModal) closeDeleteModal();
});

// -----------------------------
// Init on DOM ready: filter & lucide
// -----------------------------
document.addEventListener('DOMContentLoaded', function(){
    // jika kamu punya fungsi filterAllocations() di file, biarkan dipanggil di sini.
    if (typeof filterAllocations === 'function') {
        try { filterAllocations(); } catch(e){ console.warn(e); }
    }
    tryInitLucide();
});

// -----------------------------
// FILTERING
// -----------------------------

function filterAllocations() {
    const siklusValue = document.getElementById('siklusFilter').value;
    const statusValue = document.getElementById('statusFilter').value;

    const rows = document.querySelectorAll('.allocation-row');

    rows.forEach(row => {
        const rowSiklus = row.getAttribute('data-siklus');
        const rowStatus = row.getAttribute('data-status');

        const matchSiklus = siklusValue === '' || siklusValue === rowSiklus;
        const matchStatus = statusValue === '' || statusValue === rowStatus;

        row.style.display = (matchSiklus && matchStatus) ? '' : 'none';
    });

    updateActiveFilters();
}

function resetFilters() {
    document.getElementById('siklusFilter').value = '';
    document.getElementById('statusFilter').value = '';
    filterAllocations();
}

function updateActiveFilters() {
    const siklusValue = document.getElementById('siklusFilter').options[document.getElementById('siklusFilter').selectedIndex].text;
    const statusValue = document.getElementById('statusFilter').options[document.getElementById('statusFilter').selectedIndex].text;

    const activeBox = document.getElementById('activeFilters');

    activeBox.innerHTML = '';

    let hasFilter = false;

    if (document.getElementById('siklusFilter').value !== '') {
        activeBox.innerHTML += `<span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs">Siklus: ${siklusValue}</span>`;
        hasFilter = true;
    }
    if (document.getElementById('statusFilter').value !== '') {
        activeBox.innerHTML += `<span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs">Status: ${statusValue}</span>`;
        hasFilter = true;
    }

    activeBox.classList.toggle('hidden', !hasFilter);
}
</script>

@endsection