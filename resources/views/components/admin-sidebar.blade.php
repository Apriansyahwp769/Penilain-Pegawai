<aside class="w-64 bg-white border-r border-gray-200 flex flex-col h-full">
    <!-- Logo Section -->
    <div class="p-4 lg:p-6 border-b border-gray-200">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-sm">PK</span>
            </div>
            <div class="flex-1">
                <h1 class="text-lg font-semibold text-gray-900">Performance App</h1>
                <p class="text-sm text-gray-500 hidden lg:block">Admin Panel</p>
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
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-3 py-3 rounded-lg transition-colors text-sm lg:text-base {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 flex-shrink-0"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
            </li>

            <!-- Siklus -->
            <li>
                <a href="{{ route('admin.siklus.index') }}" class="flex items-center space-x-3 px-3 py-3 rounded-lg transition-colors text-sm lg:text-base {{ request()->routeIs('admin.siklus.*') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i data-lucide="calendar-range" class="w-5 h-5 flex-shrink-0"></i>
                    <span class="font-medium">Siklus</span>
                </a>
            </li>

            <!-- Data Master - Dengan Submenu -->
            <li>
                <div class="mb-2">
                    <button onclick="toggleDataMaster()" class="flex items-center justify-between w-full px-3 py-3 rounded-lg transition-colors text-sm lg:text-base {{ request()->routeIs('admin.criteria.*') || request()->routeIs('admin.users.*') || request()->routeIs('admin.allocations.*') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-700 hover:bg-gray-50' }}">
                        <div class="flex items-center space-x-3">
                            <i data-lucide="database" class="w-5 h-5 flex-shrink-0"></i>
                            <span class="font-medium">Data Master</span>
                        </div>
                        <i data-lucide="chevron-down" id="dataMasterIcon" class="w-4 h-4 transition-transform"></i>
                    </button>
                </div>
                
                <!-- Submenu Data Master -->
                <ul id="dataMasterSubmenu" class="ml-8 space-y-1 {{ request()->routeIs('admin.criteria.*') || request()->routeIs('admin.users.*') || request()->routeIs('admin.allocations.*') ? '' : 'hidden' }}">
                    <!-- Kriteria Penilaian -->
                    <li>
                        <a href="{{ route('admin.criteria.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg transition-colors text-sm {{ request()->routeIs('admin.criteria.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                            <i data-lucide="list-checks" class="w-4 h-4 flex-shrink-0"></i>
                            <span>Kriteria Penilaian</span>
                        </a>
                    </li>
                    
                    <!-- Manajemen User -->
                    <li>
                        <a href="{{ route('admin.users.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg transition-colors text-sm {{ request()->routeIs('admin.users.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                            <i data-lucide="users" class="w-4 h-4 flex-shrink-0"></i>
                            <span>Manajemen User</span>
                        </a>
                    </li>
                    
                    <!-- Alokasi Penilaian -->
                    <li>
                        <a href="{{ route('admin.allocations.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg transition-colors text-sm {{ request()->routeIs('admin.allocations.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50' }}">
                            <i data-lucide="share-2" class="w-4 h-4 flex-shrink-0"></i>
                            <span>Alokasi Penilaian</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Monitoring -->
            <li>
                <a href="{{ route('admin.monitoring.index') }}" class="flex items-center space-x-3 px-3 py-3 rounded-lg transition-colors text-sm lg:text-base {{ request()->routeIs('admin.monitoring.*') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i data-lucide="monitor" class="w-5 h-5 flex-shrink-0"></i>
                    <span class="font-medium">Monitoring</span>
                </a>
            </li>

            <!-- Laporan -->
            <li>
                <a href="{{ route('admin.laporan.index') }}" class="flex items-center space-x-3 px-3 py-3 rounded-lg transition-colors text-sm lg:text-base {{ request()->routeIs('admin.laporan.*') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-700 hover:bg-gray-50' }}">
                    <i data-lucide="bar-chart-3" class="w-5 h-5 flex-shrink-0"></i>
                    <span class="font-medium">Laporan</span>
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
// Toggle Data Master submenu
function toggleDataMaster() {
    const submenu = document.getElementById('dataMasterSubmenu');
    const icon = document.getElementById('dataMasterIcon');
    
    submenu.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
}

// Auto expand Data Master submenu jika active
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.pathname.includes('/admin/criteria') || 
        window.location.pathname.includes('/admin/users') || 
        window.location.pathname.includes('/admin/allocations')) {
        const submenu = document.getElementById('dataMasterSubmenu');
        const icon = document.getElementById('dataMasterIcon');
        
        submenu.classList.remove('hidden');
        icon.classList.add('rotate-180');
    }
});

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
</script>