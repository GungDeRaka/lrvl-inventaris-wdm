<div>
    {{-- Notifikasi --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 -z-10 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('message') }}
        </div>
    @endif

    {{-- Header Halaman --}}
    <div class="flex justify-between items-center space-x-2 w-full md:w-auto mb-6">
        <div class="flex justify-around items-center space-x-2">
            <h1 class="text-2xl font-semibold text-gray-800">Manajemen Pengguna</h1>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama admin..."
                class="w-full md:w-auto px-3 py-2 border border-gray-300 rounded-md shadow-sm mr-4">
        </div>
        <button wire:click="openModal()" class="bg-primary hover:bg-purple-800 text-white font-bold py-2 px-4 rounded">
            Tambah Pengguna Baru
        </button>
    </div>

    {{-- Tabel Pengguna --}}
    <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table class="min-w-full leading-normal">
            <thead>
                <tr class="bg-gray-200">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Email</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Peran</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="hover:bg-gray-50 border-b">
                        <td class="px-5 py-4 text-sm">{{ $user->name }}</td>
                        <td class="px-5 py-4 text-sm">{{ $user->email }}</td>
                        <td class="px-5 py-4 text-sm capitalize">
                            {{ str_replace('_', ' ', $user->peran) }}
                        </td>
                        <td class="px-5 py-4 text-sm whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                {{-- Tombol Edit --}}
                                <button wire:click="edit({{ $user->id }})"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                    <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                    </svg>
                                    Edit
                                </button>

                                {{-- Tombol Ganti Password --}}
                                <button wire:click="openPasswordModal({{ $user->id }})"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                                    </svg>
                                    Ganti Password
                                </button>

                                {{-- Tombol Hapus --}}
                                <button wire:click="konfirmasiHapus({{ $user->id }})"
                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                    {{ $user->id == auth()->id() ? 'disabled' : '' }}>
                                    <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.134-2.033-2.134H8.033c-1.12 0-2.033.954-2.033 2.134v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                    </svg>
                                    Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Modal Tambah/Edit Pengguna --}}
    @if ($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <form wire:submit.prevent="simpanPengguna">
                    <input type="hidden" wire:model="user_id">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ $user_id ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}</h3>
                    <div class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium">Nama</label>
                            <input type="text" wire:model="name" id="name"
                                class="mt-1 block w-full border-gray-300 rounded-md">
                            @error('name')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium">Email</label>
                            <input type="email" wire:model="email" id="email"
                                class="mt-1 block w-full border-gray-300 rounded-md">
                            {{-- {{ $user_id ? 'readonly' : '' }}> --}}
                            @error('email')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        @if (!$user_id)
                            <div>
                                <label for="password" class="block text-sm font-medium">Password</label>
                                <input type="password" wire:model="password" id="password"
                                    class="mt-1 block w-full border-gray-300 rounded-md">
                                @error('password')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                        <div>
                            <label for="peran" class="block text-sm font-medium">Peran</label>
                            <select wire:model="peran" id="peran"
                                class="mt-1 block w-full border-gray-300 rounded-md">
                                <option value="">Pilih Peran</option>
                                <option value="kepala_gudang">Kepala Gudang</option>
                                <option value="penjaga_gudang">Penjaga Gudang</option>
                                <option value="bendahara">Bendahara</option>
                            </select>
                            @error('peran')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-2">
                        <button type="button" wire:click="closeModal()"
                            class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-primary text-white rounded">{{ $user_id ? 'Update' : 'Simpan' }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Ganti Password --}}
    @if ($showPasswordModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <form wire:submit.prevent="updatePassword">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Ganti Password untuk: {{ $passwordUserName }}
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label for="new_password" class="block text-sm font-medium">Password Baru</label>
                            <input type="password" wire:model="new_password" id="new_password"
                                class="mt-1 block w-full border-gray-300 rounded-md" autofocus>
                            @error('new_password')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-2">
                        <button type="button" wire:click="closeModal()"
                            class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded">Update
                            Password</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal konfirmasi hapus admin --}}
    @if ($userIdToDelete)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Konfirmasi Hapus Pengguna</h3>
                <p class="text-sm text-gray-600 mb-4">
                    Anda yakin ingin menghapus pengguna <strong
                        class="text-primary">"{{ $userNameToDelete }}"</strong>? Tindakan ini tidak bisa
                    dibatalkan.
                </p>
                <div class="mt-4 flex justify-end space-x-2">
                    <button wire:click="$set('userIdToDelete', null)"
                        class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                    <button wire:click="hapusPengguna" class="px-4 py-2 bg-red-600 text-white rounded">Ya,
                        Hapus</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Notifikasi Error (Tambahkan ini jika belum ada) --}}
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative my-4" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif
</div>
