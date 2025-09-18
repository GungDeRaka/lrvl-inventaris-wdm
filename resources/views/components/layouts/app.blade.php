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
    <div x-data="{ open: false }" class="min-h-screen">
        {{-- HEADER UTAMA (Logo & Profil) --}}
        <header class="p-2 shadow-sm bg-gradient-to-tr from-purple-500 to-[#620F55] relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-center h-20 relative">
                    {{-- LOGO & JUDUL DI TENGAH --}}
                    <div class="flex items-center space-x-4">
                        {{-- Div untuk background gradasi logo --}}
                       
                            <img class="h-16 w-16" src="{{ asset('logo.jpg') }}" alt="Logo SMK Widiatmika">
                        
                        <span class="text-white text-2xl font-bold">SMK WIDIATMIKA</span>
                    </div>

                    {{-- DROPDOWN PROFIL DI KANAN --}}
                    <div class="absolute right-4 top-1/2 -translate-y-1/2">
                        <div x-data="{ dropdownOpen: false }" class="relative">
                            <button @click="dropdownOpen = !dropdownOpen"
                                class="flex items-center justify-center h-12 w-12 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-600 focus:outline-none">
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
                </div>
            </div>
        </header>

        {{-- NAVIGASI BAR --}}
        <nav class="bg-[#620F55] shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center h-14 space-x-8">
                    <a href="{{ route('dashboard') }}"
                        class="font-semibold text-white hover:text-gray-300 {{ request()->routeIs('dashboard') ? 'border-b-2 border-amber-400 text-amber-400' : '' }}">
                        DASHBOARD TRANSAKSI
                    </a>
                    <a href="{{ route('barang.index') }}"
                        class="font-semibold text-white hover:text-gray-300 {{ request()->routeIs('barang.index') ? 'border-b-2 border-amber-400 text-amber-400' : '' }}">
                        MANAJEMEN BARANG
                    </a>
                </div>
            </div>
        </nav>

        {{-- KONTEN UTAMA --}}
        <main class="max-w-7xl mx-auto py-8 sm:px-6 lg:px-8">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
    {{-- <script src="//cdn.jsdelivr.net/npm/alpinejs@3.13.10/dist/cdn.min.js" defer></script> --}}
</body>

</html>
