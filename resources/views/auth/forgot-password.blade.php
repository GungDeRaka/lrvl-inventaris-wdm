<x-guest-layout>
    <div class="flex min-h-screen bg-white">
        
        {{-- BAGIAN KIRI: Visual/Banner (Hanya muncul di Layar Besar) --}}
        <div class="hidden lg:flex lg:w-1/2 relative bg-primary/65 justify-center items-center overflow-hidden">
            {{-- Background Image dengan Overlay --}}
            <div class="absolute inset-0 bg-cover bg-center mix-blend-multiply" 
                 style="background-image: url('{{ asset('gambargedungwdm.webp') }}');">
            </div>
            
            {{-- Dekorasi Gradient Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-br opacity-40 from-purple-500 to-primary"></div>

            {{-- Teks/Konten Bagian Kiri --}}
            <div class="relative z-10 text-center px-10">
                <div class="mb-6 flex justify-center">
                    {{-- Placeholder Logo (Ganti dengan asset logo Anda) --}}
                    <div class="bg-white/10 p-4 rounded-full backdrop-blur-sm border border-white/20">
                        <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </div>
                </div>
                <h2 class="text-3xl font-bold text-white mb-2">Keamanan Akun</h2>
                <p class="text-indigo-200 text-lg">Reset kata sandi Anda untuk mendapatkan kembali akses ke Sistem Inventaris SMK Widiatmika.</p>
            </div>

            {{-- Hiasan Lingkaran Abstrak --}}
            <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-purple-600 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob"></div>
            <div class="absolute -top-24 -right-24 w-80 h-80 bg-indigo-600 rounded-full mix-blend-multiply filter blur-3xl opacity-30 animate-blob animation-delay-2000"></div>
        </div>

        {{-- BAGIAN KANAN: Form Forgot Password --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 lg:p-16">
            <div class="w-full max-w-md space-y-8">
                
                {{-- Header Form --}}
                <div class="text-center lg:text-left">
                    <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                        Lupa Kata Sandi?
                    </h2>
                    <p class="mt-2 text-sm text-gray-600 leading-relaxed">
                        Jangan khawatir. Masukkan alamat email yang terdaftar, dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.
                    </p>
                </div>

                {{-- Status Session (Pesan Sukses) --}}
                <x-auth-session-status class="mb-4" :status="session('status')" />

                {{-- Form --}}
                <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-6">
                    @csrf

                    {{-- Input Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Sekolah / Admin</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                </svg>
                            </div>
                            <input type="email" name="email" id="email" 
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-150 ease-in-out" 
                                placeholder="nama@widiatmika.sch.id" 
                                value="{{ old('email') }}" required autofocus>
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    {{-- Tombol Submit --}}
                    <div>
                        <button type="submit" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:scale-[1.02]">
                            Kirim Tautan Reset
                        </button>
                    </div>

                    {{-- Tombol Kembali ke Login --}}
                    <div class="text-center mt-4">
                        <a href="{{ route('login') }}" class="font-medium text-sm text-indigo-600 hover:text-indigo-500 flex items-center justify-center gap-2 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            Kembali ke Halaman Login
                        </a>
                    </div>
                </form>

                {{-- Footer Kecil --}}
                <p class="mt-8 text-center text-xs text-gray-400">
                    &copy; {{ date('Y') }} Sistem Inventaris SMK Widiatmika. <br>All rights reserved.
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>