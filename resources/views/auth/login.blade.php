<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login - Sistem Inventaris SMK Widiatmika</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased bg-gradient-to-tr from-fuchsia-300 to-[#871a77]">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white shadow-2xl rounded-lg flex w-full max-w-5xl">

            <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center">
                <div class="flex justify-center mb-8">
                    <img src="{{ asset('logo.jpg') }}" alt="Ganesha" class="h-24">
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="relative mb-6">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </span>
                        <x-text-input id="email" class="block w-full pl-10 bg-gray-100 border-none" type="email"
                            name="email" :value="old('email')" required autofocus autocomplete="username"
                            placeholder="Username" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />

                    <div class="relative mb-6">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                        </span>
                        <x-text-input id="password" class="block w-full pl-10 bg-gray-100 border-none" type="password"
                            name="password" required autocomplete="current-password" placeholder="Password" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />

                    <div class="flex items-center justify-center mt-8">
                        <x-primary-button class="w-full justify-center text-lg bg-primary hover:bg-purple-900">
                            {{ __('Login Now') }}
                        </x-primary-button>
                    </div>

                    <div class="text-center mt-6 text-sm">
                        @if (Route::has('password.request'))
                            <a class="underline text-gray-600 hover:text-gray-900"
                                href="{{ route('password.request') }}">
                                {{ __('Forgot Password?') }}
                            </a>
                        @endif
                        {{-- <span class="text-gray-400 mx-2">|</span>
                        <a class="underline text-gray-600 hover:text-gray-900" href="{{ route('register') }}">
                            {{ __('SignUp?') }}
                        </a> --}}
                    </div>
                </form>
            </div>

            <div
                class="hidden md:flex w-1/2 bg-primary text-white p-12 flex-col justify-center items-center rounded-r-lg">
                <h2 class="text-2xl text-center font-bold mb-2">Sistem Informasi Manajemen Inventaris</h2>
                <h1 class="text-4xl font-extrabold mb-8">SMK WIDIATMIKA</h1>

                <div class="w-64 h-64 bg-purple-800 rounded-lg flex items-center justify-center">
                    <img  src="{{ asset('inventory-illustration.png') }}" alt="Inventory" class="h-80 w-72">
                </div>
            </div>
        </div>
    </div>
</body>

</html>
