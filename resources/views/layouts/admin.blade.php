<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
   

    <title>Performance App - Admin Panel</title>
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
            @include('components.admin-sidebar')
        </div>

        <!-- Overlay -->
        <div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden hidden"></div>

        <div class="flex-1 flex flex-col overflow-hidden lg:ml-0 w-full">
            <!-- Header -->
            @include('components.admin-header')

            <!-- Main Content -->
            <main class="flex-1 overflow-auto p-4 lg:p-6 mt-16 lg:mt-0">
                @yield('content')
            </main>
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
    </script>
</body>

</html>