<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Performance App</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-white p-8 rounded-xl shadow-lg">
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">Selamat Datang</h2>

        @if (session('error'))
            <div class="mb-4 text-red-600 text-center">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('login.process') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block mb-1 font-medium text-gray-700">Email</label>
                <input type="email" name="email" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none">
            </div>

            <div>
                <label class="block mb-1 font-medium text-gray-700">Password</label>
                <input type="password" name="password" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none">
            </div>

            <button type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg font-semibold transition">
                Login
            </button>

            <p class="text-center text-sm text-gray-600 mt-3">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-indigo-600 hover:underline">Daftar</a>
            </p>
        </form>
    </div>

    <script>
        lucide.createIcons();
    </script>

</body>
</html>
