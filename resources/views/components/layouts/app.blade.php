<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Sistem Inventaris SMK Widiatmika' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-gray-300 font-sans">
    <div x-data="{ isMobileMenuOpen: false }" class="min-h-screen">
        {{-- HEADER UTAMA (Logo & Profil) --}}
        <header class="p-2 shadow-sm bg-gradient-to-tr from-fuchsia-300 to-primary relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between md:justify-center h-20 relative">
                    {{-- LOGO & JUDUL DI TENGAH --}}
                    <div class="flex items-center space-x-4">

                        {{-- Div untuk background gradasi logo --}}
                        <img class="h-8 w-8 md:h-16 md:w-16" src="{{ asset('logo.jpg') }}" alt="Logo SMK Widiatmika">
                        <span class="text-white text-xl md:text-2xl font-bold">SMK WIDIATMIKA</span>
                    </div>

                    {{-- DROPDOWN PROFIL DI KANAN --}}
                    <div class="hidden md:flex absolute right-4 top-1/2 -translate-y-1/2 items-center space-x-2">
                        {{-- KOMPONEN NOTIFIKASI  --}}
                        <livewire:notifications.bell />
                        <div x-data="{ dropdownOpen: false }" class="relative">
                            <button @click="dropdownOpen = !dropdownOpen"
                                class="flex items-center justify-center h-8 w-8 md:h-12 md:w-12 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-600 focus:outline-none">
                                {{-- Icon User --}}
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </button>
                            <div x-show="dropdownOpen" @click.away="dropdownOpen = false"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-20" x-transition>
                                <div class="px-4 py-2 text-sm text-gray-700">{{ Auth::user()->name }}</div>
                                <a href="{{ route('profile.edit') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil Saya</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a href="{{ route('logout') }}"
                                        onclick="event.preventDefault(); this.closest('form').submit();"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Log Out
                                    </a>
                                </form>
                            </div>
                        </div>
                    </div>
                    {{-- tombol Hamburger  --}}
                    <div class="md:hidden">
                        <button @click="isMobileMenuOpen = !isMobileMenuOpen"
                            class="p-2 rounded-md text-white hover:bg-white/20">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{ 'hidden': isMobileMenuOpen, 'inline-flex': !isMobileMenuOpen }"
                                    class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{ 'hidden': !isMobileMenuOpen, 'inline-flex': isMobileMenuOpen }"
                                    class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

            </div>
        </header>

        {{-- NAVIGASI BAR --}}
        <nav class="hidden md:block bg-primary shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-center md:justify-start h-14 space-x-8">
                    <a href="{{ route('dashboard') }}"
                        class="font-semibold hover:text-gray-300 {{ request()->routeIs('dashboard') ? 'border-b-2 border-amber-400 text-amber-400' : 'text-white' }}">
                        DASHBOARD TRANSAKSI
                    </a>
                    <a href="{{ route('barang.index') }}"
                        class="font-semibold hover:text-gray-300 {{ request()->routeIs('barang.index') ? 'border-b-2 border-amber-400 text-amber-400' : 'text-white' }}">
                        MANAJEMEN BARANG
                    </a>
                    @can('kelola-pengguna')
                        <a href="{{ route('kategori.index') }}"
                            class="font-semibold hover:text-gray-300 {{ request()->routeIs('kategori.index') ? 'border-b-2 border-amber-400 text-amber-400' : 'text-white' }}">
                            MANAJEMEN KATEGORI
                        </a>
                        <a href="{{ route('ruangan.index') }}"
                            class="font-semibold hover:text-gray-300 {{ request()->routeIs('ruangan.index') ? 'border-b-2 border-amber-400 text-amber-400' : 'text-white ' }}">
                            MANAJEMEN RUANGAN
                        </a>
                        <a href="{{ route('user.index') }}"
                            class="font-semibold hover:text-gray-300 {{ request()->routeIs('user.index') ? 'border-b-2 border-amber-400 text-amber-400' : ' text-white' }}">
                            MANAJEMEN ADMIN
                        </a>
                        <a href="{{ route('siswa.index') }}"
                            class="font-semibold text-white hover:text-gray-300 {{ request()->routeIs('siswa.index') ? 'border-b-2 border-amber-400 text-amber-400' : 'text-white' }}">
                            MANAJEMEN SISWA
                        </a>
                    @endcan
                </div>
            </div>
        </nav>

        {{-- MENU NAVIGASI MOBILE (Muncul saat hamburger diklik) --}}
        <div x-show="isMobileMenuOpen" class="md:hidden absolute w-full z-10 bg-primary shadow-lg" x-transition>
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="{{ route('dashboard') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-white">DASHBOARD TRANSAKSI</a>
                <a href="{{ route('barang.index') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-white">MANAJEMEN BARANG</a>
                @can('kelola-pengguna')
                    <a href="{{ route('kategori.index') }}"
                        class="block px-3 py-2 rounded-md text-base font-medium text-white">MANAJEMEN KATEGORI</a>
                    <a href="{{ route('ruangan.index') }}"
                        class="block px-3 py-2 rounded-md text-base font-medium text-white">MANAJEMEN RUANGAN</a>
                    <a href="{{ route('user.index') }}"
                        class="block px-3 py-2 rounded-md text-base font-medium text-white">MANAJEMEN PENGGUNA</a>
                @endcan
                {{-- Di menu mobile, kita juga bisa tambahkan link profil dan logout --}}
                <div class="border-t border-purple-400 mt-4 pt-4">
                    <a href="{{ route('profile.edit') }}"
                        class="block px-3 py-2 rounded-md text-base font-medium text-white">Profil Saya</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}"
                            onclick="event.preventDefault(); this.closest('form').submit();"
                            class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-white">
                            Log Out
                        </a>
                    </form>
                </div>
            </div>
        </div>

        {{-- KONTEN UTAMA --}}
        <main class="max-w-7xl mx-auto py-8 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts

</body>

</html>
