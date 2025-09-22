<div>
    <h1 class="text-2xl font-semibold text-gray-800">Selamat Datang, {{ Auth::user()->name }}!</h1>
    <p class="text-gray-600">Ini adalah halaman dashboard Anda.</p>

    {{-- Nanti kita akan isi dengan komponen Livewire untuk peminjaman --}}
    {{-- Ganti bagian "MELAKUKAN PEMINJAMAN" dengan kode ini --}}
    <div>
        <h2 class="text-xl font-semibold text-gray-700 mb-4">MELAKUKAN PEMINJAMAN</h2>
        <div class="bg-white p-6 rounded-lg shadow">

            {{-- Notifikasi Sukses --}}
            @if (session()->has('message'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                    role="alert">
                    <span class="block sm:inline">{{ session('message') }}</span>
                </div>
            @endif
            
            @if (session()->has('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Terjadi Kesalahan!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Menampilkan Error Validasi --}}
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                    role="alert">
                    <strong class="font-bold">Harap perbaiki error berikut:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            <form wire:submit.prevent="simpanPeminjaman">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Kolom Kiri: Data Siswa --}}
                    <div class="space-y-4">
                        <div>
                            <label for="nis" class="block text-sm font-medium text-gray-700">Cari NIS Siswa</label>
                            <input type="text" id="nis" wire:model.live.debounce.300ms="nis"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                placeholder="Masukkan NIS...">
                        </div>

                        @if ($siswaDitemukan)
                            <div class="bg-gray-50 p-4 rounded-md border">
                                <p class="font-bold">{{ $siswaDitemukan->nama }}</p>
                                <p class="text-sm text-gray-600">Kelas: {{ $siswaDitemukan->kelas }}</p>
                                <p class="text-sm text-gray-600">No. HP: {{ $siswaDitemukan->no_hp }}</p>
                            </div>
                        @elseif(!empty($nis))
                            <div class="bg-red-50 p-4 rounded-md border border-red-200">
                                <p class="font-bold text-red-700">Siswa tidak ditemukan.</p>
                            </div>
                        @endif
                    </div>

                    {{-- Kolom Kanan: Data Barang & Peminjaman --}}
                    <div class="space-y-4">
                        <div class="relative">
                            <label for="searchBarang" class="block text-sm font-medium text-gray-700">Cari Barang
                                (Kode/Nama)</label>
                            <input type="text" id="searchBarang" wire:model.live.debounce.300ms="searchBarang"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                                placeholder="Ketik min. 2 huruf...">

                            @if (!empty($barangDitemukan) && !$selectedBarangId)
                                <div class="absolute z-10 w-full mt-1 bg-white rounded-md shadow-lg border">
                                    <ul class="max-h-60 overflow-auto">
                                        @foreach ($barangDitemukan as $barang)
                                            <li wire:click="selectBarang({{ $barang->id }}, '{{ addslashes($barang->nama_barang) }}')"
                                                class="px-4 py-2 cursor-pointer hover:bg-gray-100">
                                                {{ $barang->nama_barang }} (Stok: {{ $barang->jumlah_saat_ini }})
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        @if ($selectedBarangId)
                            <div class="bg-gray-50 p-4 rounded-md border">
                                <p class="font-bold">Barang Dipilih: {{ $selectedBarangNama }}</p>
                            </div>
                        @endif

                        <div>
                            <label for="ruang_pemakaian" class="block text-sm font-medium text-gray-700">Ruang
                                Pemakaian</label>
                            <input type="text" id="ruang_pemakaian" wire:model="ruang_pemakaian"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>

                        <div>
                            <label for="waktu_kembali" class="block text-sm font-medium text-gray-700">Waktu
                                Pengembalian</label>
                            <input type="datetime-local" id="waktu_kembali" wire:model="waktu_kembali"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit"
                        class="bg-purple-700 hover:bg-purple-800 text-white font-bold py-2 px-4 rounded">
                        Simpan Peminjaman
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
