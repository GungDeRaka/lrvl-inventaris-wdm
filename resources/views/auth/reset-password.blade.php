<x-guest-layout>
    <div class="flex min-h-screen bg-white">
        
        {{-- ======================= --}}
        {{-- BAGIAN KIRI: Visual Banner (Hanya di Desktop) --}}
        {{-- ======================= --}}
        <div class="hidden lg:flex lg:w-1/2 relative bg-indigo-900 justify-center items-center overflow-hidden">
            {{-- Background Image: Gedung Sekolah --}}
            <div class="absolute inset-0 bg-cover bg-center" 
                 style="background-image: url('{{ asset('gambargedungwdm.webp') }}');">
            </div>
            
            {{-- Gradient Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-br from-primary to-purple-400 mix-blend-multiply"></div>

            {{-- Konten Visual --}}
            <div class="relative z-10 text-center px-10">
                <div class="mb-8 flex justify-center animate-fade-in-down">
                    <div class="p-3 bg-white/10 backdrop-blur-sm border border-white/20 shadow-xl">
                        <img src="{{ asset('logo.jpg') }}" alt="Logo SMK Widiatmika" class="h-20 w-auto ">
                    </div>
                </div>
                <h2 class="text-4xl font-bold text-white mb-4 tracking-tight">Atur Ulang Kata Sandi</h2>
                <p class="text-indigo-100 text-lg max-w-md mx-auto leading-relaxed">
                    Silakan buat kata sandi baru yang kuat dan mudah diingat untuk mengamankan akun Anda kembali.
                </p>
            </div>
             <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-indigo-900/50 to-transparent"></div>
        </div>


        {{-- ======================= --}}
        {{-- BAGIAN KANAN: Form Reset Password --}}
        {{-- ======================= --}}
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12 lg:p-16 relative bg-gray-50/50">
            <div class="w-full max-w-md space-y-8 bg-white p-8 ] shadow-sm border border-gray-100">
                
                <div class="text-center">
                    <img src="{{ asset('logo.jpg') }}" alt="Logo" class="h-12 w-auto mx-auto lg:hidden mb-4 ">
                    <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">
                        Buat Kata Sandi Baru
                    </h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Masukkan kata sandi baru Anda di bawah ini.
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded-r">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700 font-bold">
                                    Ups! Ada kesalahan input:
                                </p>
                                <ul class="mt-1 list-disc list-inside text-sm text-red-600">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.store') }}" class="mt-8 space-y-6">
                    @csrf

                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

                    {{-- Input: Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Akun</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                </svg>
                            </div>
                            <input type="email" name="email" id="email" 
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl bg-gray-100 text-gray-500 sm:text-sm cursor-not-allowed" 
                                value="{{ old('email', $request->email) }}" required readonly>
                        </div>
                    </div>

                    {{-- Input: Password Baru --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Kata Sandi Baru</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="password" name="password" id="password" 
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition ease-in-out duration-150" 
                                placeholder="Minimal 8 karakter" required autofocus autocomplete="new-password">
                        </div>
                    </div>

                    {{-- Input: Konfirmasi Password --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Kata Sandi</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="password" name="password_confirmation" id="password_confirmation" 
                                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition ease-in-out duration-150" 
                                placeholder="Ulangi kata sandi baru" required autocomplete="new-password">
                        </div>
                    </div>

                    {{-- Tombol Submit --}}
                    <div>
                        <button type="submit" 
                            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:scale-[1.02]">
                            Simpan Kata Sandi Baru
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>