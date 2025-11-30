<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <title>Performance App - Ketua Divisi Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Load Lucide JS -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>

<body class="bg-gray-50">
    <!-- Mobile Menu Button -->
    <div class="lg:hidden fixed top-4 left-4 z-50">
        <button id="mobileMenuButton" class="p-2 bg-white rounded-lg shadow-md">
            <i data-lucide="menu" class="w-5 h-5"></i>
        </button>
    </div>

    <div class="flex h-screen">
        <!-- Sidebar -->
        <div id="sidebar" class="w-64 bg-white border-r border-gray-200 flex flex-col fixed lg:static inset-y-0 left-0 z-40 transform -translate-x-full lg:translate-x-0 transition-transform duration-300">
            @include('components.staff-sidebar')
        </div>

        <!-- Overlay -->
        <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden hidden"></div>

        <div class="flex-1 flex flex-col overflow-hidden lg:ml-0 w-full">
            <!-- Header -->
            @include('components.staff-header')

            <!-- Main Content -->
            <main class="flex-1 overflow-auto p-4 lg:p-6 mt-16 lg:mt-0">
                @yield('content')
            </main>
        </div>

        <!-- Logout Confirmation Modal -->
        <div id="logoutModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-xl p-6 max-w-sm w-full mx-4 shadow-xl">
                <div class="flex items-center justify-center w-12 h-12 bg-red-100 rounded-full mx-auto mb-4">
                    <i data-lucide="log-out" class="w-6 h-6 text-red-600"></i>
                </div>

                <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Konfirmasi Logout</h3>
                <p class="text-gray-600 text-center mb-6">Apakah Anda yakin ingin keluar dari sistem?</p>

                <div class="flex space-x-3">
                    <button onclick="closeLogoutModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                        Batal
                    </button>
                    <button onclick="submitLogout()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                        Ya, Logout
                    </button>
                </div>
            </div>
        </div>
    </div>
    @stack('scripts')

    <script>
        // Initialize Lucide icons
        document.addEventListener('DOMContentLoaded', function() {
            // Init Lucide
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
                console.log('Lucide icons initialized');
            } else {
                console.error('Lucide not loaded');
            }

            // Mobile menu functionality
            const mobileMenuButton = document.getElementById('mobileMenuButton');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');

            if (mobileMenuButton && sidebar && overlay) {
                mobileMenuButton.addEventListener('click', function() {
                    sidebar.classList.toggle('-translate-x-full');
                    overlay.classList.toggle('hidden');
                });

                overlay.addEventListener('click', function() {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('hidden');
                });

                // Close sidebar ketika klik link di mobile
                const sidebarLinks = document.querySelectorAll('#sidebar a');
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth < 1024) {
                            sidebar.classList.add('-translate-x-full');
                            overlay.classList.add('hidden');
                        }
                    });
                });
            }
        });

        // Re-init Lucide ketika modal dibuka
        function initLucideIcons() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }

        // Panggil init ketika modal dibuka
        function openCreateModal() {
            document.getElementById('createModal').classList.remove('hidden');
            initLucideIcons();
        }

        function openEditModal(siklusId) {
            // ... kode edit modal ...
            document.getElementById('editModal').classList.remove('hidden');
            initLucideIcons();
        }

        // Fungsi logout 
        function confirmLogout() {
            document.getElementById('logoutModal').classList.remove('hidden');
        }

        function closeLogoutModal() {
            document.getElementById('logoutModal').classList.add('hidden');
        }

        function submitLogout() {
            document.getElementById('logoutForm').submit();
        }

        // Close modal on outside click
        document.getElementById('logoutModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeLogoutModal();
        });

        // ESC to close
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeLogoutModal();
        });

        // Dan pastikan init Lucide tetap ada
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
            // ... kode mobile sidebar ...
        });
    </script>
</body>

</html>