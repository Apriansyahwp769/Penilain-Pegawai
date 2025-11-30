<aside class="w-64 bg-white border-r border-gray-200 flex flex-col h-full">
    <!-- Logo Section -->
    <div class="p-4 lg:p-6 border-b border-gray-200">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-sm">PK</span>
            </div>
            <div class="flex-1">
                <h1 class="text-lg font-semibold text-gray-900">Performance App</h1>
                <p class="text-sm text-gray-500 hidden lg:block">Ketua Divisi</p>
            </div>
            <!-- Close Button Mobile -->
            <button class="lg:hidden p-1 text-gray-500 hover:text-gray-700">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 p-4">
        <ul class="space-y-2">
            <!-- Dashboard -->
            <li>
                <a href="{{ route('ketua-divisi.dashboard') }}" class="flex items-center space-x-3 px-3 py-3 rounded-lg transition-colors text-sm lg:text-base {{ request()->routeIs('ketua-divisi.dashboard') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 flex-shrink-0"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
            </li>

            <!-- Penilaian Tim -->
            <li>
                <a href="{{ route('ketua-divisi.penilaian.index') }}" class="flex items-center space-x-3 px-3 py-3 rounded-lg transition-colors text-sm lg:text-base {{ request()->routeIs('ketua-divisi.penilaian.*') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i data-lucide="clipboard-list" class="w-5 h-5 flex-shrink-0"></i>
                    <span class="font-medium">Penilaian Tim</span>
                </a>
            </li>

            <!-- Riwayat Tim -->
            <li>
                <a href="{{ route('ketua-divisi.riwayat.index') }}" class="flex items-center space-x-3 px-3 py-3 rounded-lg transition-colors text-sm lg:text-base {{ request()->routeIs('ketua-divisi.riwayat.*') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i data-lucide="history" class="w-5 h-5 flex-shrink-0"></i>
                    <span class="font-medium">Riwayat Tim</span>
                </a>
            </li>

            <!-- Profile Saya -->
            <li>
                <a href="{{ route('ketua-divisi.profile.index') }}" class="flex items-center space-x-3 px-3 py-3 rounded-lg transition-colors text-sm lg:text-base {{ request()->routeIs('ketua-divisi.profile.*') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i data-lucide="user" class="w-5 h-5 flex-shrink-0"></i>
                    <span class="font-medium">Profile Saya</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Bottom Section -->
    <div class="p-4 border-t border-gray-200">
        <!-- Logout Button with Form -->
        <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="mt-2">
            @csrf
            <button type="button" onclick="confirmLogout()" class="flex items-center space-x-3 px-3 py-3 text-red-600 hover:bg-red-50 rounded-lg w-full transition-colors text-sm lg:text-base">
                <i data-lucide="log-out" class="w-5 h-5 flex-shrink-0"></i>
                <span class="font-medium">Logout</span>
            </button>
        </form>
    </div>
</aside>



<script>
// ✅ Logout Functions
function confirmLogout() {
    document.getElementById('logoutModal').classList.remove('hidden');
}

function closeLogoutModal() {
    document.getElementById('logoutModal').classList.add('hidden');
}

function submitLogout() {
    document.getElementById('logoutForm').submit();
}

// Close modal when clicking outside
document.getElementById('logoutModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeLogoutModal();
    }
});

// ESC key to close modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeLogoutModal();
    }
});

// Initialize Lucide icons if available
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>