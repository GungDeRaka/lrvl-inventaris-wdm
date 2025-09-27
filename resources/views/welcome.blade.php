<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Inventaris SMK Widiatmika</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased shadow-sm bg-gradient-to-tr from-fuchsia-300 to-[#871a77] ">
    <div class="relative flex items-center justify-center min-h-screen">
        <div class="max-w-xxl mx-auto p-6 text-center">

            {{-- Logo --}}
            <div class="flex justify-center mb-6">
                <div class="p-3 ">
                    {{-- Pastikan file logo-widiatmika.png ada di folder /public --}}
                    <img class="h-40 w-40" src="{{ asset('logo.jpg') }}" alt="Logo SMK Widiatmika">
                </div>
            </div>

            {{-- Judul Sistem --}}
            <h1 class="text-4xl font-bold text-white mb-8">
                Sistem Informasi Manajemen Inventaris<br>SMK Widiatmika
            </h1>

            {{-- Tombol Aksi --}}
            <div class="flex items-center justify-center space-x-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}"
                            class="px-6 py-3 bg-fuchsia-700 text-white font-semibold rounded-lg shadow-md hover:bg-purple-800 transition">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}"
                            class="px-6 py-3 bg-fuchsia-700 text-white font-semibold rounded-lg shadow-md hover:bg-purple-800 transition">Masuk</a>

                        {{-- @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="px-6 py-3 bg-white text-fuchsia-700 font-semibold rounded-lg shadow-md hover:bg-gray-50 transition">Daftar</a>
                        @endif --}}
                    @endauth
                @endif
            </div>
        </div>
    </div>
</body>

</html>
