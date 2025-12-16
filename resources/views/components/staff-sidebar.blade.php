<aside class="w-64 bg-white border-r border-gray-200 flex flex-col h-full">
    <!-- Logo Section -->
    <div class="p-4 lg:p-6 border-b border-gray-200">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <span class="text-white font-bold text-sm">PM</span>
            </div>
            <div class="flex-1">
                <h1 class="text-lg font-semibold text-gray-900">PETRO Muba</h1>
                <p class="text-sm text-gray-500 hidden lg:block">Staff Panel</p>
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

            <!-- Hasil Penilaian -->
            <li>
                <a href="{{ route('staff.hasil_penilaian.index') }}"
                    class="flex items-center space-x-3 px-3 py-3 rounded-lg transition-colors 
                    text-sm lg:text-base 
                    {{ request()->routeIs('staff.hasil_penilaian.*') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-700 hover:bg-gray-50' }}">

                    <i data-lucide="file-bar-chart" class="w-5 h-5"></i>
                    <span class="font-medium">Hasil Penilaian</span>
                </a>
            </li>

            <!-- Riwayat -->
            <li>
                <a href="{{ route('staff.riwayat.index') }}"
                    class="flex items-center space-x-3 px-3 py-3 rounded-lg transition-colors text-sm lg:text-base 
                    {{ request()->routeIs('staff.riwayat.*') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-700 hover:bg-gray-50' }}">

                    <i data-lucide="history" class="w-5 h-5"></i>
                    <span class="font-medium">Riwayat</span>
                </a>
            </li>

            <!-- Profile -->
            <li>
                <a href="{{ route('staff.profile.index') }}"
                    class="flex items-center space-x-3 px-3 py-3 rounded-lg transition-colors text-sm lg:text-base 
                    {{ request()->routeIs('staff.profile.*') ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'text-gray-700 hover:bg-gray-50' }}">

                    <i data-lucide="user-circle" class="w-5 h-5"></i>
                    <span class="font-medium">Profil</span>
                </a>
            </li>

        </ul>
    </nav>

    <!-- Bottom Section -->
    <div class="p-4 border-t border-gray-200">
        <!-- Logout Button with Form -->
        <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="mt-2">
            @csrf
            <button type="button" onclick="confirmLogout()"
                class="flex items-center space-x-3 px-3 py-3 text-red-600 hover:bg-red-50 rounded-lg w-full transition-colors text-sm lg:text-base">

                <i data-lucide="log-out" class="w-5 h-5"></i>
                <span class="font-medium">Logout</span>
            </button>
        </form>
    </div>
</aside>
