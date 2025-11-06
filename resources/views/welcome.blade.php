<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Manajemen Inventaris - SMK Widiatmika</title>
    {{-- @vite('resources/css/app.css') --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script> --}}
</head>

<body class="antialiased bg-white">
    <div class="relative flex flex-col lg:flex-row min-h-screen">

        {{-- Gambar Latar Belakang (hanya untuk layar kecil & sedang) --}}
        <div class="absolute inset-0 lg:hidden">
            <img src="{{ asset('gambargedungwdm.webp') }}" alt="Gedung SMK Widiatmika" class="w-full h-full object-cover">
            {{-- Overlay lembut agar teks tetap jelas --}}
            <div class="absolute inset-0 bg-black/40"></div>
        </div>

        {{-- Panel Ungu --}}
        <div
            class="relative z-10 flex flex-col justify-center items-center text-center lg:items-start lg:text-left 
                   bg-primary/95 lg:bg-primary text-white backdrop-blur-sm lg:backdrop-blur-none
                   w-full lg:w-6/12 min-h-[50vh] lg:min-h-screen
                   px-4 sm:px-8 lg:px-14 py-10 sm:py-16 lg:py-0
                   mt-10 sm:my-12 md:my-12 lg:my-0">

            {{-- Kontainer isi agar mudah diatur --}}
            <div class="lg:ml-32 max-w-lg">
                {{-- Logo & Nama Sekolah --}}
                <div class="flex flex-col lg:flex-row items-center gap-4 mb-8 justify-center lg:justify-start">
                    <img src="{{ asset('logo.jpg') }}" alt="Logo SMK Widiatmika" class="h-20 w-20 object-contain">
                    <div>
                        <h2 class="text-2xl font-semibold leading-tight">SMK<br>Widiatmika</h2>
                    </div>
                </div>

                {{-- Judul Sistem --}}
                <h1 class="text-3xl sm:text-4xl font-medium mb-6 leading-snug">
                    Selamat datang di <br>
                    <span class="text-amber-400 font-bold">Sistem Manajemen Inventaris</span>
                </h1>

                <p class="text-lg mb-6">Pilih peran login sebagai:</p>

                {{-- Tombol Login --}}
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="{{ route('siswa.login') }}"
                        class="px-10 py-3 bg-amber-400 text-[#620F55] font-semibold rounded-md shadow-md hover:bg-amber-500 transition text-center">
                        Siswa
                    </a>
                    <a href="{{ route('login') }}"
                        class="px-10 py-3 bg-amber-400 text-[#620F55] font-semibold rounded-md shadow-md hover:bg-amber-500 transition text-center">
                        Admin
                    </a>
                </div>
            </div>
        </div>

        {{-- Gambar Gedung (hanya untuk layar besar) --}}
        <div class="hidden lg:block w-1/2">
            <img src="{{ asset('gambargedungwdm.webp') }}" alt="Gedung SMK Widiatmika"
                class="w-full h-full object-cover">
        </div>
    </div>
</body>

</html>
