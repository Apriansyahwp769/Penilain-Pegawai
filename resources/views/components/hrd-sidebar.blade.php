<aside class="w-64 bg-white border-r border-gray-200 flex flex-col h-full">

 <!-- Logo Section -->
    <div class="p-4 lg:p-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <img src="{{ asset('img/petromuba.png') }}" alt="PETRO Muba Logo" class="h-8 w-auto">
            <!-- Close Button Mobile -->
            <button class="lg:hidden p-1 text-gray-500 hover:text-gray-700">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <p class="text-sm text-gray-600 mt-2 font-medium">HRD Panel</p>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 p-4">
        <ul class="space-y-2">

            <!-- Dashboard -->
            <li>
                <a href="{{ route('hrd.dashboard') }}"
                   class="flex items-center space-x-3 px-3 py-3 rounded-lg transition-colors text-sm lg:text-base
                   {{ request()->routeIs('hrd.dashboard') 
                        ? 'bg-blue-50 text-blue-700 border border-blue-200' 
                        : 'text-gray-700 hover:bg-gray-50' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 flex-shrink-0"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
            </li>

            <!-- Verifikasi Penilaian -->
            <li>
                <a href="{{ route('hrd.verifikasi.index') }}"
                   class="flex items-center space-x-3 px-3 py-3 rounded-lg transition-colors text-sm lg:text-base
                   {{ request()->routeIs('hrd.verifikasi.*') 
                        ? 'bg-blue-50 text-blue-700 border border-blue-200' 
                        : 'text-gray-700 hover:bg-gray-50' }}">
                    <i data-lucide="check-circle" class="w-5 h-5 flex-shrink-0"></i>
                    <span class="font-medium">Verifikasi Penilaian</span>
                </a>
            </li>

            <!-- Monitoring -->
            <li>
                <a href="{{ route('hrd.monitoring.index') }}"
                   class="flex items-center space-x-3 px-3 py-3 rounded-lg transition-colors text-sm lg:text-base
                   {{ request()->routeIs('hrd.monitoring.*') 
                        ? 'bg-blue-50 text-blue-700 border border-blue-200' 
                        : 'text-gray-700 hover:bg-gray-50' }}">
                    <i data-lucide="activity" class="w-5 h-5 flex-shrink-0"></i>
                    <span class="font-medium">Monitoring</span>
                </a>
            </li>

            <!-- Laporan -->
            <li>
                <a href="#"
                   class="flex items-center space-x-3 px-3 py-3 rounded-lg transition-colors text-sm lg:text-base text-gray-400 cursor-not-allowed"
                   title="Coming Soon">
                    <i data-lucide="bar-chart-3" class="w-5 h-5 flex-shrink-0"></i>
                    <span class="font-medium">Laporan</span>
                    <span class="ml-auto text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">Soon</span>
                </a>
            </li>

            <!-- Profile -->
            <li>
                <a href="{{ route('hrd.profile.index') }}"
                   class="flex items-center space-x-3 px-3 py-3 rounded-lg transition-colors text-sm lg:text-base
                   {{ request()->routeIs('hrd.profile.*') 
                        ? 'bg-blue-50 text-blue-700 border border-blue-200' 
                        : 'text-gray-700 hover:bg-gray-50' }}">
                    <i data-lucide="user" class="w-5 h-5 flex-shrink-0"></i>
                    <span class="font-medium">Profil</span>
                </a>
            </li>

        </ul>
    </nav>

    <!-- Bottom Section -->
    <div class="p-4 border-t border-gray-200">
        <form id="logoutForm" action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="button" onclick="confirmLogout()"
                class="flex items-center space-x-3 px-3 py-3 text-red-600 hover:bg-red-50 rounded-lg w-full transition-colors text-sm lg:text-base">
                <i data-lucide="log-out" class="w-5 h-5 flex-shrink-0"></i>
                <span class="font-medium">Logout</span>
            </button>
        </form>
    </div>

</aside>