<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PETRO Muba Performance App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>

<body class="bg-gradient-to-br from-yellow-400 via-orange-500 to-yellow-600 flex items-center justify-center min-h-screen relative overflow-hidden">
    
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 w-64 h-64 bg-white rounded-full blur-3xl"></div>
        <div class="absolute bottom-10 right-10 w-96 h-96 bg-white rounded-full blur-3xl"></div>
    </div>

    <!-- Logo Background (Watermark) -->
    <div class="absolute inset-0 flex items-center justify-center opacity-5">
        <img src="{{ asset('img/petromuba.png') }}" alt="PETRO Muba" class="w-2/3 max-w-2xl">
    </div>

    <div class="w-full max-w-md mx-4 relative z-10">
        <!-- Card Container -->
        <div class="bg-white/80 backdrop-blur-sm p-8 rounded-2xl shadow-2xl">
            
            <!-- Logo Section -->
            <div class="flex flex-col items-center mb-8">
                <img src="{{ asset('img/petromuba.png') }}" alt="PETRO Muba Logo" class="h-16 w-auto mb-4">
                <h2 class="text-2xl font-bold text-gray-800">Performance App</h2>
                <p class="text-gray-500 text-sm mt-1">Masuk ke akun Anda</p>
            </div>

            @if (session('error'))
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center">
                    <i data-lucide="alert-circle" class="w-5 h-5 mr-2"></i>
                    <span class="text-sm">{{ session('error') }}</span>
                </div>
            @endif

            <form action="{{ route('login.process') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Email Input -->
                <div>
                    <label class="block mb-2 font-medium text-gray-700">Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="mail" class="w-5 h-5 text-gray-400"></i>
                        </div>
                        <input type="email" name="email" required
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 focus:outline-none transition"
                               placeholder="nama@pegawai.com">
                    </div>
                </div>

                <!-- Password Input -->
                <div>
                    <label class="block mb-2 font-medium text-gray-700">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="w-5 h-5 text-gray-400"></i>
                        </div>
                        <input type="password" name="password" required id="password"
                               class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 focus:outline-none transition"
                               placeholder="Masukkan password">
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <i data-lucide="eye" id="eyeIcon" class="w-5 h-5 text-gray-400 hover:text-gray-600"></i>
                        </button>
                    </div>
                </div>

               <!-- Login Button -->
                <button type="submit"
                    class="w-full bg-gradient-to-r from-orange-500 to-yellow-500 hover:from-orange-600 hover:to-yellow-600 text-white py-3 rounded-lg font-semibold transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    Masuk
                </button>
            </form>

            <!-- Footer -->
            <div class="mt-6 text-center">
                <p class="text-xs text-gray-500">
                    2026 PT Petro Muba ( Perseroda ) | All Rights Reserved.
                </p>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordInput.type = 'password';
                eyeIcon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        }
    </script>

</body>
</html>