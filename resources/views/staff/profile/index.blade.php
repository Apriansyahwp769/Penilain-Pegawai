{{-- resources/views/staff/profile/index.blade.php --}}
@extends('layouts.staff')

@section('page-title', 'Profile Saya')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-gray-900">👤 Profile Saya</h1>
        <p class="text-gray-600 text-sm lg:text-base">Kelola informasi pribadi dan pengaturan akun Anda</p>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
        <div class="flex items-center">
            <svg class="h-5 w-5 text-red-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Profile Summary Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <!-- Avatar -->
                <div class="flex flex-col items-center mb-6">
                    <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mb-3">
                        <span class="text-3xl font-bold text-blue-600">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 text-center">{{ $user->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $user->position->name ?? '-' }}</p>
                    <span class="mt-2 px-3 py-1 bg-gray-100 text-gray-800 text-xs font-medium rounded-full">
                        {{ ucwords(str_replace('_', ' ', $user->role ?? 'staff')) }}
                    </span>
                </div>

                <!-- Quick Info -->
                <div class="border-t pt-4 space-y-3">
                    <div class="flex items-center text-sm">
                        <svg class="h-4 w-4 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span class="text-gray-600">{{ $user->email }}</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <svg class="h-4 w-4 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span class="text-gray-600">{{ $user->phone ?? 'Belum diisi' }}</span>
                    </div>
                    <div class="flex items-center text-sm">
                        <svg class="h-4 w-4 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span class="text-gray-600">{{ $user->division->name ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Edit Forms -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informasi Pribadi (Editable) -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Informasi Pribadi
                </h2>

                <form action="{{ route('staff.profile.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <!-- Nama -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="{{ old('name', $user->name) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                            required
                        >
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email', $user->email) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                            required
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Nomor Telepon
                        </label>
                        <input 
                            type="text" 
                            id="phone" 
                            name="phone" 
                            value="{{ old('phone', $user->phone) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('phone') border-red-500 @enderror"
                            placeholder="Contoh: 08123456789"
                        >
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Divider -->
                    <div class="border-t pt-4">
                        <h3 class="text-md font-medium text-gray-900 mb-3">Ganti Password (Opsional)</h3>
                        <p class="text-sm text-gray-600 mb-4">Kosongkan jika tidak ingin mengganti password</p>
                    </div>

                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password Lama
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="current_password" 
                                name="current_password"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('current_password') border-red-500 @enderror"
                                placeholder="Masukkan password lama"
                            >
                            <button 
                                type="button" 
                                onclick="togglePassword('current_password')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                            >
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password Baru
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password" 
                                name="password"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror"
                                placeholder="Minimal 8 karakter"
                            >
                            <button 
                                type="button" 
                                onclick="togglePassword('password')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                            >
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Konfirmasi Password Baru
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password_confirmation" 
                                name="password_confirmation"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Ketik ulang password baru"
                            >
                            <button 
                                type="button" 
                                onclick="togglePassword('password_confirmation')"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
                            >
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4 flex justify-end space-x-3">
                        <button 
                            type="reset"
                            class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200"
                        >
                            Reset
                        </button>
                        <button 
                            type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center"
                        >
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Update Profile
                        </button>
                    </div>
                </form>
            </div>

            <!-- Informasi Kepegawaian (Readonly) -->
            <div class="bg-white rounded-xl border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    Informasi Kepegawaian
                </h2>
                <p class="text-sm text-gray-500 mb-4">Data berikut hanya bisa diubah oleh administrator</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- NIP -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">NIP</p>
                        <p class="text-sm font-medium text-gray-900">{{ $user->nip }}</p>
                    </div>

                    <!-- Division -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Divisi</p>
                        <p class="text-sm font-medium text-gray-900">{{ $user->division->name ?? '-' }}</p>
                    </div>

                    <!-- Position -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Jabatan</p>
                        <p class="text-sm font-medium text-gray-900">{{ $user->position->name ?? '-' }}</p>
                    </div>

                    <!-- Role -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Role</p>
                        <span class="inline-block px-2 py-1 bg-gray-100 text-gray-800 text-xs font-medium rounded">
                            {{ ucwords(str_replace('_', ' ', $user->role)) }}
                        </span>
                    </div>

                    <!-- Join Date -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Tanggal Bergabung</p>
                        <p class="text-sm font-medium text-gray-900">{{ $user->join_date ? $user->join_date->format('d F Y') : '-' }}</p>
                    </div>

                    <!-- Status -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-xs text-gray-500 mb-1">Status</p>
                        <span class="inline-block px-2 py-1 {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} text-xs font-medium rounded">
                            {{ $user->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
    field.setAttribute('type', type);
}

// Initialize Lucide icons (if used)
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>

@endsection