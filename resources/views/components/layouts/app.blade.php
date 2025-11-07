<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Sistem Inventaris SMK Widiatmika' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-gray-100 font-sans">

    {{-- 
      Inisialisasi Alpine.js 
      'isSidebarOpen' akan melacak status sidebar.
      Kita gunakan 'localStorage' agar pilihan sidebar (buka/tutup) diingat browser.
    --}}
    <div x-data="{ isSidebarOpen: JSON.parse(localStorage.getItem('isSidebarOpen') || 'true') }" x-init="$watch('isSidebarOpen', val => localStorage.setItem('isSidebarOpen', val))" class="flex min-h-screen">

        {{-- ============================================= --}}
        {{-- SIDEBAR NAVIGASI (KIRI) --}}
        {{-- ============================================= --}}
        <aside class="bg-primary text-white flex flex-col transition-all duration-300 ease-in-out overflow-hidden"
            :class="isSidebarOpen ? 'w-64' : 'w-0 lg:w-20'">

            {{-- Logo dan Nama Admin --}}
            <div class="flex items-center justify-center h-20 shadow-md">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4">
                    <img class="h-12 w-12 rounded-sm flex-shrink-0" src="{{ asset('logo.jpg') }}"
                        alt="Logo SMK Widiatmika">
                    <div :class="!isSidebarOpen && 'hidden'" class="overflow-hidden transition-all duration-200">
                        <p class="font-bold text-sm whitespace-nowrap">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-purple-200 whitespace-nowrap">
                            {{ Auth::user()->peran == 'kepala_gudang' ? 'Kepala Gudang' : 'Penjaga Gudang' }}</p>
                    </div>
                </a>
            </div>

            {{-- Daftar Menu Navigasi --}}
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                {{-- Link Dashboard --}}
                <a href="{{ route('dashboard') }}"
                    class="flex items-center space-x-3 p-3 rounded-md hover:bg-white/20 {{ request()->routeIs('dashboard') ? 'bg-white/20' : '' }}">
                    <svg class="h-6 w-6 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6-4h.01M12 17h.01">
                        </path>
                    </svg>
                    <span :class="!isSidebarOpen && 'hidden'" class="overflow-hidden whitespace-nowrap">Dashboard</span>
                </a>

                {{-- Link Manajemen Barang --}}
                <a href="{{ route('barang.index') }}"
                    class="flex items-center space-x-3 p-3 rounded-md hover:bg-white/20 {{ request()->routeIs('barang.index') ? 'bg-white/20' : '' }}">
                    <svg class="h-6 w-6 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                    <span :class="!isSidebarOpen && 'hidden'" class="overflow-hidden whitespace-nowrap">Manajemen
                        Barang</span>
                </a>

                {{-- Link Manajemen RAB --}}
                <a href="{{ route('rab.index') }}"
                    class="flex items-center space-x-3 p-3 rounded-md hover:bg-white/20 {{ request()->routeIs('rab.index') ? 'bg-white/20' : '' }}">
                    <svg class="h-6 w-6 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <span :class="!isSidebarOpen && 'hidden'" class="overflow-hidden whitespace-nowrap">Manajemen
                        RAB</span>
                </a>

                {{-- Menu Khusus Kepala Gudang --}}
                @can('kelola-pengguna')
                    <hr class="border-purple-400">
                    <p :class="!isSidebarOpen && 'hidden'"
                        class="px-3 pt-2 text-xs font-semibold text-purple-200 uppercase tracking-wider">Admin Area</p>

                    <a href="{{ route('kategori.index') }}"
                        class="flex items-center space-x-3 p-3 rounded-md hover:bg-white/20 {{ request()->routeIs('kategori.index') ? 'bg-white/20' : '' }}">
                        <svg class="h-6 w-6 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                        <span :class="!isSidebarOpen && 'hidden'" class="overflow-hidden whitespace-nowrap">Manajemen
                            Kategori</span>
                    </a>

                    <a href="{{ route('ruangan.index') }}"
                        class="flex items-center space-x-3 p-3 rounded-md hover:bg-white/20 {{ request()->routeIs('ruangan.index') ? 'bg-white/20' : '' }}">
                        <svg class="h-6 w-6 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                        </svg>
                        <span :class="!isSidebarOpen && 'hidden'" class="overflow-hidden whitespace-nowrap">Manajemen
                            Ruangan</span>
                    </a>

                    <a href="{{ route('user.index') }}"
                        class="flex items-center space-x-3 p-3 rounded-md hover:bg-white/20 {{ request()->routeIs('user.index') ? 'bg-white/20' : '' }}">
                        <svg class="h-6 w-6 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8.25V6a1.5 1.5 0 011.5-1.5h15A1.5 1.5 0 0121 6v2.25M3 8.25h18M5.25 8.25v9A1.5 1.5 0 006.75 18h10.5a1.5 1.5 0 001.5-1.5v-9M9 12h6" />
                        </svg>
                        <span :class="!isSidebarOpen && 'hidden'" class="overflow-hidden whitespace-nowrap">Manajemen
                            Admin</span>
                    </a>

                    <a href="{{ route('siswa.index') }}"
                        class="flex items-center space-x-3 p-3 rounded-md hover:bg-white/20 {{ request()->routeIs('siswa.index') ? 'bg-white/20' : '' }}">
                        <svg class="h-6 w-6 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                            </path>
                        </svg>
                        <span :class="!isSidebarOpen && 'hidden'" class="overflow-hidden whitespace-nowrap">Manajemen
                            Siswa</span>
                    </a>
                @endcan
            </nav>

            {{-- Tombol Logout di Bawah --}}
            <div class="px-4 pb-4 flex-shrink-0">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"
                        class="flex items-center space-x-3 p-3 rounded-md hover:bg-white/20">
                        <svg class="h-6 w-6 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                        <span :class="!isSidebarOpen && 'hidden'" class="overflow-hidden whitespace-nowrap">Log
                            Out</span>
                    </a>
                </form>
            </div>
        </aside>

        {{-- ============================================= --}}
        {{-- AREA KONTEN UTAMA (KANAN) --}}
        {{-- ============================================= --}}
        <div class="flex-1 flex flex-col">

            {{-- Header Atas (Tombol Hamburger & Notifikasi) --}}
            <header
                class=" shadow-sm h-20 flex items-center justify-between px-6 bg-gradient-to-l from-fuchsia-300 to-primary relative">
                <div class="flex flex-row justify-evenly">
                    {{-- Tombol Buka/Tutup Sidebar --}}
                    <button @click="isSidebarOpen = !isSidebarOpen"
                        class="p-2 rounded-md text-zinc-50 hover:bg-amber-400">
                        <!-- Icon Hamburger (tampil jika sidebar tertutup) -->
                        <svg x-show="!isSidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>

                        <!-- Icon Close (tampil jika sidebar terbuka) -->
                        <svg x-show="isSidebarOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <h1 class="text-xl text-white font-bold items center p-2">{{ $title ?? ' ' }}</h1>
                </div>
                {{-- Ikon Notifikasi & Profil (Desktop) --}}
                <div class="flex items-center space-x-2">
                    <livewire:notifications.bell />
                    <div x-data="{ dropdownOpen: false }" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen"
                            class="flex items-center justify-center h-12 w-12 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-600 focus:outline-none">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
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
            </header>

            {{-- Konten Utama (Scrollable) --}}
            <main class="flex-1 overflow-y-auto p-6 md:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>

</html>
