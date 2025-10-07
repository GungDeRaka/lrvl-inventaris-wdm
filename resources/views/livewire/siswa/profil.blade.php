<div>
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Ganti Password</h2>

        @if (session()->has('message'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                {{ session('message') }}
            </div>
        @endif

        <form wire:submit.prevent="updatePassword">
            <div class="space-y-4">
                {{-- Password Saat Ini --}}
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700">Password Saat Ini</label>
                    <input type="password" id="current_password" wire:model="current_password"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('current_password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                {{-- Password Baru --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password Baru</label>
                    <input type="password" id="password" wire:model="password"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                {{-- Konfirmasi Password Baru --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                    <input type="password" id="password_confirmation" wire:model="password_confirmation"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="bg-primary text-white font-bold py-2 px-4 rounded-lg shadow-md">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>