<div>
    {{-- Care about people's approval and you will be their prisoner. --}}

    {{-- Notifikasi --}}
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            {{ session('error') }}</div>
    @endif

    {{-- Header Halaman --}}
    <div class="flex items-center justify-between space-x-2 w-full md:w-auto mb-6">
        <div class="flex justify-around items-center space-x-2">

            <h1 class="text-2xl font-semibold text-gray-800">Manajemen Siswa</h1>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari kode atau nama..."
                class="w-full md:w-auto px-3 py-2 border border-gray-300 rounded-md shadow-sm">
        </div>
        <button wire:click="openModal()" class="bg-primary text-white font-bold py-2 px-4 rounded">Tambah Siswa
            Baru</button>
    </div>

    {{-- Tabel Siswa --}}
    <div class="bg-white shadow-md rounded-lg overflow-x-auto">
        <table class="min-w-full leading-normal">
            <thead class="bg-primary text-white">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase">NIS</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase">Nama</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase">Email</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase">No.Hp</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase">Kelas</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase">Status Akun</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-white uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siswas as $siswa)
                    <tr class="hover:bg-gray-50 border-b">
                        <td class="px-5 py-4 text-sm">{{ $siswa->nis }}</td>
                        <td class="px-5 py-4 text-sm">{{ $siswa->nama }}</td>
                        <td class="px-5 py-4 text-sm">{{ $siswa->email }}</td>
                        <td class="px-5 py-4 text-sm">{{ $siswa->no_hp }}</td>
                        <td class="px-5 py-4 text-sm">{{ $siswa->kelas }}</td>
                        {{-- status akun --}}
                        <td class="px-5 py-4 text-sm">
                            @if ($siswa->is_ditangguhkan)
                                <span
                                    class="px-2 py-1 font-semibold leading-tight rounded-full bg-red-200 text-red-900">Ditangguhkan</span>
                            @else
                                <span
                                    class="px-2 py-1 font-semibold leading-tight rounded-full bg-green-200 text-green-900">Aktif</span>
                            @endif
                        </td>
                        {{-- tombol aksi --}}
                        <td class="px-5 py-4 text-sm whitespace-nowrap">
                            @if ($siswa->is_ditangguhkan)
                                <button wire:click="batalTangguhan({{ $siswa->id }})"
                                    class="font-semibold text-green-600 hover:text-green-900">Batal Tangguhan</button>
                            @endif

                            <button wire:click="edit({{ $siswa->id }})"
                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                                Edit
                            </button>
                            <button wire:click="openPasswordModal({{ $siswa->id }})"
                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                                </svg>
                                Ganti Password
                            </button>
                            <button wire:click="konfirmasiHapus({{ $siswa->id }})"
                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-4 h-4 mr-1.5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.134-2.033-2.134H8.033c-1.12 0-2.033.954-2.033 2.134v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                                Hapus
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4">Belum ada data siswa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $siswas->links() }}</div>
    </div>

    {{-- Modal Tambah/Edit Siswa --}}
    @if ($showModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <form wire:submit.prevent="simpanSiswa">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        {{ $siswa_id ? 'Edit Data Siswa' : 'Tambah Siswa Baru' }}</h3>
                    <div class="space-y-4">
                        <div>
                            <label for="nis" class="block text-sm font-medium">NIS</label>
                            <input type="text" wire:model="nis" id="nis"
                                class="mt-1 block w-full border-gray-300 rounded-md">
                            @error('nis')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="nama" class="block text-sm font-medium">Nama Lengkap</label>
                            <input type="text" wire:model="nama" id="nama"
                                class="mt-1 block w-full border-gray-300 rounded-md">
                            @error('nama')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium">Email</label>
                            <input type="email" wire:model="email" id="email"
                                class="mt-1 block w-full border-gray-300 rounded-md">
                            @error('email')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="kelas" class="block text-sm font-medium">Kelas</label>
                            <input type="text" wire:model="kelas" id="kelas"
                                class="mt-1 block w-full border-gray-300 rounded-md">
                            @error('kelas')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="no_hp" class="block text-sm font-medium">No. HP</label>
                            <input type="text" wire:model="no_hp" id="no_hp"
                                class="mt-1 block w-full border-gray-300 rounded-md">
                            @error('no_hp')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>
                        @if (!$siswa_id)
                            <div>
                                <label for="password" class="block text-sm font-medium">Password</label>
                                <input type="password" wire:model="password" id="password"
                                    class="mt-1 block w-full border-gray-300 rounded-md"
                                    placeholder="Min. 6 karakter">
                                @error('password')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                    </div>
                    <div class="mt-6 flex justify-end space-x-2">
                        <button type="button" wire:click="closeModal()"
                            class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                        <button type="submit"
                            class="px-4 py-2 bg-primary text-white rounded">{{ $siswa_id ? 'Update' : 'Simpan' }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Ganti Password Siswa --}}
    @if ($showPasswordModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <form wire:submit.prevent="updatePassword">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Ganti Password untuk: {{ $passwordSiswaNama }}
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

    {{-- Modal Konfirmasi Hapus --}}
    @if ($siswaIdToDelete)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-30">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
                <p>Anda yakin ingin menghapus siswa ini?</p>
                <div class="mt-4 flex justify-end space-x-2">
                    <button wire:click="$set('siswaIdToDelete', null)"
                        class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                    <button wire:click="hapusSiswa" class="px-4 py-2 bg-red-600 text-white rounded">Ya, Hapus</button>
                </div>
            </div>
        </div>
    @endif
</div>
