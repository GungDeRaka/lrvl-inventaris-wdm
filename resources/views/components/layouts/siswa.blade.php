<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script> --}}
    @livewireStyles
</head>

<body class="bg-gray-100">
    <nav class="bg-primary text-white shadow-md">
        <div class="max-w-4xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold">Portal Siswa</h1>
            <div class="flex items-center space-x-4">
                {{-- LINK BARU --}}
                <a href="{{ route('siswa.profil') }}" class="hover:underline">Profil Saya</a>
                <form method="POST" action="{{ route('siswa.logout') }}">
                    @csrf
                    <a href="{{ route('siswa.logout') }}"
                        onclick="event.preventDefault(); this.closest('form').submit();"
                        class="hover:underline">Logout</a>
                </form>
            </div>
        </div>
    </nav>
    <main class="max-w-4xl mx-auto p-4">
        {{ $slot }}
    </main>
    @livewireScripts
</body>

</html>
