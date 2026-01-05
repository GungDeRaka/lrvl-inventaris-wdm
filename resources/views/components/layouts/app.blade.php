<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Sistem Inventaris SMK Widiatmika' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    {{-- STYLE PENTING: x-cloak menyembunyikan elemen sampai Alpine.js siap --}}
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-gray-100 font-sans antialiased">

    {{-- 
        DATA ALPINE.JS:
        - sidebarOpen: Status sidebar.
        - init(): Mengecek ukuran layar saat pertama kali dibuka. 
                  Jika layar besar (Desktop), cek localStorage. 
                  Jika layar kecil (Mobile), default tutup (false).
    --}}
    {{-- Toast Notification --}}
    <div x-data="{ show: false, message: '', type: 'success' }"
    x-cloak
        x-on:notify.window="show = true; message = $event.detail.message; type = $event.detail.type || 'success'; setTimeout(() => show = false, 3000)"
        class="fixed top-2 right-20 z-50 flex w-72 flex-col gap-2">

        <div x-show="show" x-transition:enter="transform ease-out duration-300 transition"
            x-transition:enter-start="translate-y-10 opacity-0" {{-- Muncul dari bawah --}}
            x-transition:enter-end="translate-y-0 opacity-100" x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0 scale-90"
            class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        {{-- Ikon Ceklis Hijau --}}
                        <svg x-show="type === 'success'" class="h-6 w-6 text-green-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{-- Ikon Silang Merah --}}
                        <svg x-show="type === 'error'" class="h-6 w-6 text-red-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p class="text-sm font-medium text-gray-900"
                            x-text="type === 'success' ? 'Berhasil!' : 'Terjadi Kesalahan'"></p>
                        <p class="mt-1 text-sm text-gray-500" x-text="message"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div x-data="{
        sidebarOpen: false,
        init() {
            if (window.innerWidth >= 768) {
                this.sidebarOpen = JSON.parse(localStorage.getItem('sidebarOpen') || 'true');
            } else {
                this.sidebarOpen = false;
            }
            this.$watch('sidebarOpen', val => {
                if (window.innerWidth >= 768) localStorage.setItem('sidebarOpen', val);
            });
        }
    }" class="flex h-screen overflow-hidden" x-cloak>

        {{-- ============================================= --}}
        {{-- BACKDROP MOBILE (Layar Gelap saat sidebar buka di HP) --}}
        {{-- ============================================= --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false"
            x-transition:enter="transition-opacity ease-linear duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-20 bg-black bg-opacity-50 md:hidden">
        </div>

        {{-- ============================================= --}}
        {{-- SIDEBAR NAVIGASI --}}
        {{-- ============================================= --}}
        {{-- 
            Penjelasan Class:
            - fixed inset-y-0 left-0 z-30: Agar sidebar menempel di kiri dan di atas konten lain pada Mobile.
            - md:relative: Pada Desktop, sidebar menjadi bagian dari layout (tidak menumpuk).
            - transform transition-all: Animasi halus.
            - md:translate-x-0: Pada desktop sidebar selalu terlihat (tapi lebarnya berubah).
            - -translate-x-full: Pada mobile, defaultnya tersembunyi di kiri layar.
        --}}
        <aside
            class="fixed inset-y-0 left-0 z-30 bg-primary text-white transition-all duration-300 ease-in-out transform md:translate-x-0 md:relative flex flex-col"
            :class="{
                'translate-x-0 w-64': sidebarOpen,
                '-translate-x-full md:w-20 md:translate-x-0': !sidebarOpen
            }">

            {{-- Header Sidebar (Logo) --}}
            <div class="flex items-center justify-center h-20 shadow-md bg-primary/90">
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-4 overflow-hidden">
                    <img class="h-10 w-10 flex-shrink-0 border-2 border-white/20" src="{{ asset('logo.jpg') }}"
                        alt="Logo">

                    {{-- Teks hanya muncul jika sidebar terbuka --}}
                    <div x-show="sidebarOpen" class="transition-opacity duration-200">
                        <p class="font-bold text-sm whitespace-nowrap">{{ Auth::user()->name }}</p>
                        <p class="text-[10px] text-purple-200 whitespace-nowrap uppercase tracking-wider">
                            {{ Auth::user()->peran == 'kepala_gudang' ? 'Kepala Gudang' : 'Penjaga Gudang' }}
                        </p>
                    </div>
                </a>
            </div>

            {{-- Menu Navigasi --}}
            <nav class="flex-1 px-3 py-6 space-y-2 overflow-y-auto scrollbar-hide">

                {{-- Helper Function untuk Link --}}
                @php
                    $navLinkClass =
                        'flex items-center space-x-3 p-3 rounded-lg transition duration-200 hover:bg-white/20 hover:text-white';
                    $activeClass = 'bg-white/20 text-white shadow-inner';
                @endphp

                <a href="{{ route('dashboard') }}" title="Dashboard"
                    class="{{ $navLinkClass }} {{ request()->routeIs('dashboard') ? $activeClass : '' }}">
                    <svg class="h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span x-show="sidebarOpen" class="whitespace-nowrap font-medium">Dashboard</span>
                </a>

                <a href="{{ route('barang.index') }}" title="Barang"
                    class="{{ $navLinkClass }} {{ request()->routeIs('barang.index') ? $activeClass : '' }}">
                    <svg class="h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span x-show="sidebarOpen" class="whitespace-nowrap font-medium">Data Barang</span>
                </a>

                <a href="{{ route('rab.index') }}" title="RAB"
                    class="{{ $navLinkClass }} {{ request()->routeIs('rab.index') ? $activeClass : '' }}">
                    <svg class="h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span x-show="sidebarOpen" class="whitespace-nowrap font-medium">Manajemen RAB</span>
                </a>

                @can('kelola-pengguna')
                    <div x-show="sidebarOpen" class="pt-4 pb-1">
                        <p class="px-3 text-xs font-semibold text-purple-200 uppercase tracking-wider">Fitur Khusus Kepala
                            Gudang</p>
                    </div>
                    <hr x-show="!sidebarOpen" class="border-white/20 my-2">

                    <a href="{{ route('kategori.index') }}" title="Kategori"
                        class="{{ $navLinkClass }} {{ request()->routeIs('kategori.index') ? $activeClass : '' }}">
                        <svg class="h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <span x-show="sidebarOpen" class="whitespace-nowrap font-medium">Kategori</span>
                    </a>

                    <a href="{{ route('ruangan.index') }}" title="Ruangan"
                        class="{{ $navLinkClass }} {{ request()->routeIs('ruangan.index') ? $activeClass : '' }}">
                        <svg class="h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span x-show="sidebarOpen" class="whitespace-nowrap font-medium">Ruangan</span>
                    </a>

                    <a href="{{ route('user.index') }}" title="Users"
                        class="{{ $navLinkClass }} {{ request()->routeIs('user.index') ? $activeClass : '' }}">
                        <svg class="h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span x-show="sidebarOpen" class="whitespace-nowrap font-medium">Admin & Staff</span>
                    </a>

                    <a href="{{ route('siswa.index') }}" title="Siswa"
                        class="{{ $navLinkClass }} {{ request()->routeIs('siswa.index') ? $activeClass : '' }}">
                        {{-- Icon Mortarboard (Topi Wisuda) --}}
                        <svg class="h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4L2 9l10 5 10-5-10-5zm0 13v3m0-3c-4.418 0-8-1.79-8-4V9m16 0v4c0 2.21-3.582 4-8 4" />
                        </svg>
                        <span x-show="sidebarOpen" class="whitespace-nowrap font-medium">Data Siswa</span>
                    </a>
                @endcan

            </nav>

            {{-- Footer Sidebar (Logout) --}}
            <div class="p-4 border-t border-white/10">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();"
                        class="flex items-center space-x-3 p-2 rounded-lg hover:bg-red-500/20 hover:text-red-100 transition-colors text-white/80">
                        <svg class="h-6 w-6 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span x-show="sidebarOpen" class="whitespace-nowrap font-medium">Keluar</span>
                    </a>
                </form>
            </div>
        </aside>

        {{-- ============================================= --}}
        {{-- KONTEN UTAMA --}}
        {{-- ============================================= --}}
        <div class="flex-1 flex flex-col h-screen overflow-hidden transition-all duration-300"
            :class="sidebarOpen ? 'md:ml-0' : 'md:ml-0'">
            {{-- Note: karena sidebar position:relative di desktop, margin-left otomatis diurus flexbox --}}

            {{-- Header Atas --}}
            <header class="h-20 bg-primary shadow-sm flex items-center justify-between px-4 lg:px-6 z-10">
                <div class="flex items-center">
                    {{-- Tombol Hamburger --}}
                    <button @click="sidebarOpen = !sidebarOpen" class="text-white focus:outline-none lg:hidden">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="hidden lg:block text-white focus:outline-none mr-4">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path x-show="!sidebarOpen" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path x-show="sidebarOpen" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                        </svg>
                    </button>

                    <h1 class="text-lg font-semibold text-white ml-2 md:ml-0">{{ $title ?? 'Dashboard' }}</h1>
                </div>


                <div class="flex items-center space-x-4">
                    <livewire:notifications.bell />

                    {{-- Dropdown Profil --}}
                    <div x-data="{ dropdownOpen: false }" class="relative">
                        <button @click="dropdownOpen = !dropdownOpen"
                            class="flex items-center space-x-2 focus:outline-none">
                            <div class="w-8 h-8 rounded-full bg-white/20 text-white flex items-center justify-center">
                                <span class="font-bold text-sm">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                            <span
                                class="hidden md:block text-sm font-medium text-white">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="dropdownOpen" @click.away="dropdownOpen = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5 z-50"
                            style="display: none;">
                            <div class="px-4 py-2 border-b">
                                <p class="text-sm text-gray-700 font-medium">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                            </div>
                            {{-- <a href="{{ route('profile.edit') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Edit Profil</a> --}}
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    Log Out
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Area Konten (Scrollable) --}}
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>

</html>
