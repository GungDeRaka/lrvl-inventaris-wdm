<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Siswa - Sistem Inventaris SMK Widiatmika</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script> --}}
</head>

<body class="font-sans text-gray-900 antialiased relative min-h-screen overflow-hidden">

    {{-- 🔹 Gambar Background --}}
    <div class="absolute inset-0">
        <img src="{{ asset('gambargedungwdm.webp') }}" 
             alt="Gedung SMK Widiatmika" 
             class="w-full h-full object-cover">
        {{-- 🔹 Overlay Gradasi --}}
        <div class="absolute inset-0 bg-gradient-to-tr from-fuchsia-800 to-[#871a77] opacity-80"></div>
    </div>

    {{-- 🔹 Konten Utama --}}
    <div class="relative z-10 min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 text-white">
        {{-- Logo --}}
        <div>
            <a href="/">
                <img class="h-20 w-20 shadow-lg" src="{{ asset('logo.jpg') }}" alt="Logo SMK Widiatmika">
            </a>
        </div>

        {{-- Judul --}}
        <h1 class="text-2xl font-bold mt-4 text-white drop-shadow-lg">Login Portal Siswa</h1>
        <p class="text-gray-200 mb-6">Sistem Inventaris SMK Widiatmika</p>

        {{-- Kotak Form --}}
        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white/90 backdrop-blur-sm shadow-2xl overflow-hidden sm:rounded-lg text-gray-900">

            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('siswa.login') }}">
                @csrf

                <div>
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" 
                                  name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div class="mt-4">
                    <x-input-label for="password" value="Password" />
                    <x-text-input id="password" class="block mt-1 w-full" type="password" 
                                  name="password" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" 
                               class="rounded border-gray-300 text-primary shadow-sm focus:ring-primary" 
                               name="remember">
                        <span class="ms-2 text-sm text-gray-700">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-end mt-4">
                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" 
                           href="{{ route('siswa.password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif

                    <x-primary-button class="ms-3 bg-primary hover:bg-purple-900">
                        {{ __('Log in') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
