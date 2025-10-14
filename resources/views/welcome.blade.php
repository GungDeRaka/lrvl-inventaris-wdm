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
            <div class="flex flex-col items-center justify-center gap-4">
                {{-- Tombol Login Siswa --}}
                <a href="{{ route('siswa.login') }}" class="w-full sm:w-auto px-6 py-3 bg-primary text-white font-semibold rounded-lg shadow-md hover:bg-purple-800 transition">
                    Login Sebagai Siswa
                </a>

                {{-- Tombol Login Staff --}}
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('login') }}" class="w-full border border-amber-400 hover:bg-amber-400 sm:w-auto px-6 py-3 bg-white text-primary font-semibold rounded-lg shadow-md transition">
                        Login Sebagai Kepala Gudang
                    </a>
                    <a href="{{ route('login') }}" class="w-full sm:w-auto px-6 py-3 bg-white text-primary font-semibold rounded-lg shadow-md border border-amber-400 hover:bg-amber-400 transition">
                        Login Sebagai Penjaga Gudang
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
