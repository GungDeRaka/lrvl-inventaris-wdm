<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-100">
    <nav class="bg-primary text-white shadow-md">
        <div class="max-w-4xl mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-xl font-bold">Portal Siswa</h1>
            <form method="POST" action="{{ route('siswa.logout') }}">
                @csrf
                <a href="{{ route('siswa.logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="hover:underline">Logout</a>
            </form>
        </div>
    </nav>
    <main class="max-w-4xl mx-auto p-4">
        {{ $slot }}
    </main>
    @livewireScripts
</body>
</html>