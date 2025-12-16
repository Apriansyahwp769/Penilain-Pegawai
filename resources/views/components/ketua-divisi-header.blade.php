<!-- resources/views/components/admin-header.blade.php -->
<header class="bg-white border-b border-gray-200 px-4 lg:px-6 py-4 fixed w-full lg:static top-0 z-20">
    <div class="flex items-center justify-between">
        <!-- Mobile Title - Tampil hanya di mobile -->
        <div class="lg:hidden">
            <h1 class="text-lg font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h1>
        </div>

        <!-- Desktop - Empty space or breadcrumb -->
        <div class="hidden lg:block flex-1">
            <h1 class="text-xl font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h1>
        </div>

        <!-- Right Section -->
        <div class="flex items-center space-x-3 lg:space-x-4">
          
            <!-- User Profile -->
            <div class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                <div class="w-8 h-8 lg:w-9 lg:h-9 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white font-semibold text-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </span>
                </div>
                <div class="hidden lg:block">
                    <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500">
                        @php
                            $roleNames = [
                                'admin' => 'Administrator',
                                'ketua_divisi' => 'Ketua Divisi',
                                'staff' => 'Staff'
                            ];
                        @endphp
                        {{ $roleNames[auth()->user()->role] ?? auth()->user()->role }}
                        @if(auth()->user()->division)
                            - {{ auth()->user()->division->name }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</header>