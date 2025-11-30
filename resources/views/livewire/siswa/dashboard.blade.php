<div class="min-h-screen bg-gray-50 pb-20 md:pb-8">
    {{-- 1. Header Section dengan Gradient & Sapaan Personal --}}
    <div class="relative bg-gradient-to-br from-primary to-purple-800 pb-24 pt-8 rounded-b-[2rem] shadow-lg">
        <div class="px-6 md:px-10">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-purple-200 text-sm font-medium mb-1">Selamat Datang,</p>
                    <h1 class="text-2xl md:text-3xl font-bold text-white leading-tight">
                        {{ auth()->guard('siswa')->user()->nama }} 👋
                    </h1>
                </div>

                {{-- Status Akun Badge --}}
                @if (auth()->guard('siswa')->user()->is_ditangguhkan)
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-500 text-white shadow-sm border border-red-400">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Akun Ditangguhkan
                    </span>
                @else
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-500/20 text-green-100 border border-green-400/30 backdrop-blur-sm">
                        <span class="w-2 h-2 rounded-full bg-green-400 mr-2 animate-pulse"></span>
                        Akun Aktif
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Container Utama (Offset ke atas untuk efek menumpuk) --}}
    <div class="px-4 md:px-8 -mt-16 relative z-10">

        {{-- 2. Notifikasi Alert (Jika ada) --}}
        @if (session()->has('message'))
            <div
                class="mb-6 bg-white border-l-4 border-green-500 p-4 rounded-r-xl shadow-md flex items-start animate-fade-in-up">
                <div class="text-green-500 mr-3"><svg class="w-6 h-6" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg></div>
                <p class="text-sm text-gray-700">{{ session('message') }}</p>
            </div>
        @endif
        @if (session()->has('error'))
            <div
                class="mb-6 bg-white border-l-4 border-red-500 p-4 rounded-r-xl shadow-md flex items-start animate-fade-in-up">
                <div class="text-red-500 mr-3"><svg class="w-6 h-6" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg></div>
                <p class="text-sm text-gray-700">{{ session('error') }}</p>
            </div>
        @endif

        {{-- 3. Tombol Aksi Utama (Card Besar) --}}
        <button wire:click="$set('showRequestModal', true)"
            {{ auth()->guard('siswa')->user()->is_ditangguhkan ? 'disabled' : '' }}
            class="w-full group relative overflow-hidden bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 text-left disabled:opacity-70 disabled:cursor-not-allowed mb-8 border border-gray-100">

            <div
                class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-primary/10 rounded-full group-hover:scale-150 transition-transform duration-500">
            </div>

            <div class="relative flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 group-hover:text-primary transition-colors">Pinjam Barang
                        Baru</h3>
                    <p class="text-sm text-gray-500 mt-1">Klik di sini untuk membuat pengajuan peminjaman alat atau
                        barang.</p>
                </div>
                <div
                    class="h-12 w-12 bg-primary text-white rounded-full flex items-center justify-center shadow-md group-hover:bg-purple-700 transition-colors transform group-hover:rotate-90 duration-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
            </div>
        </button>

        {{-- 4. Navigasi Tab Modern --}}
        <div class="flex space-x-1 bg-gray-200/80 p-1 rounded-xl mb-6 backdrop-blur-sm overflow-x-auto">
            <button wire:click="setActiveTab('riwayat')"
                class="flex-1 flex items-center justify-center py-2.5 px-4 rounded-lg text-sm font-semibold transition-all duration-200 whitespace-nowrap
                {{ $activeTab == 'riwayat' ? 'bg-white text-primary shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                <svg class="w-4 h-4 mr-2 {{ $activeTab == 'riwayat' ? 'text-primary' : 'text-gray-400' }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Riwayat Peminjaman
            </button>
            <button wire:click="setActiveTab('ketersediaan')"
                class="flex-1 flex items-center justify-center py-2.5 px-4 rounded-lg text-sm font-semibold transition-all duration-200 whitespace-nowrap
                {{ $activeTab == 'ketersediaan' ? 'bg-white text-primary shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                <svg class="w-4 h-4 mr-2 {{ $activeTab == 'ketersediaan' ? 'text-primary' : 'text-gray-400' }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                    </path>
                </svg>
                Cek Stok Barang
            </button>
        </div>

        {{-- 5. Konten Utama --}}
        <div class="min-h-[300px]">
            {{-- TAB RIWAYAT --}}
            @if ($activeTab == 'riwayat')
                <div class="space-y-4">
                    @forelse($riwayat as $item)
                        @php
                            $statusColors = match ($item->status) {
                                'disetujui' => [
                                    'bg' => 'bg-cyan-50',
                                    'text' => 'text-cyan-700',
                                    'border' => 'border-cyan-200',
                                    'icon' => 'text-cyan-500',
                                ],
                                'dipinjam' => [
                                    'bg' => 'bg-blue-50',
                                    'text' => 'text-blue-700',
                                    'border' => 'border-blue-200',
                                    'icon' => 'text-blue-500',
                                ],
                                'dikembalikan' => [
                                    'bg' => 'bg-green-50',
                                    'text' => 'text-green-700',
                                    'border' => 'border-green-200',
                                    'icon' => 'text-green-500',
                                ],
                                'ditolak' => [
                                    'bg' => 'bg-red-50',
                                    'text' => 'text-red-700',
                                    'border' => 'border-red-200',
                                    'icon' => 'text-red-500',
                                ],
                                'menunggu-konfirmasi' => [
                                    'bg' => 'bg-yellow-50',
                                    'text' => 'text-yellow-700',
                                    'border' => 'border-yellow-200',
                                    'icon' => 'text-yellow-500',
                                ],
                                'perpanjangan-diajukan' => [
                                    'bg' => 'bg-purple-50',
                                    'text' => 'text-purple-700',
                                    'border' => 'border-purple-200',
                                    'icon' => 'text-purple-500',
                                ],
                                default => [
                                    'bg' => 'bg-gray-50',
                                    'text' => 'text-gray-700',
                                    'border' => 'border-gray-200',
                                    'icon' => 'text-gray-500',
                                ],
                            };

                            // Logika waktu
                            $waktuSelesai = $item->waktu_pengembalian_aktual ?? $item->waktu_kembali;
                            $waktuKembaliCarbon = \Carbon\Carbon::parse($item->waktu_kembali);
                            $isMendekatiBatas =
                                $item->status == 'dipinjam' &&
                                $waktuKembaliCarbon->between(now(), now()->addMinutes(30));
                        @endphp

                        {{-- Card Riwayat --}}
                        <div wire:click="showDetail({{ $item->id }})"
                            class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md hover:transition-shadow duration-200 cursor-pointer relative group active:scale-98 active:transition-transform">

                            {{-- Indikator klik (opsional, untuk UX desktop) --}}
                            <div
                                class="absolute inset-0 bg-purple-50 opacity-0 group-hover:opacity-10 transition-opacity">
                            </div>
                            {{-- Header Card --}}
                            <div
                                class="px-5 py-3 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                                <span
                                    class="text-xs text-gray-500 font-medium">{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y, H:i') }}</span>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase {{ $statusColors['bg'] }} {{ $statusColors['text'] }}">
                                    {{ $item->status == 'menunggu-konfirmasi' ? 'Menunggu' : ucfirst($item->status) }}
                                </span>
                            </div>

                            {{-- Body Card --}}
                            <div class="p-5">
                                <div class="mb-3">
                                    <p class="text-xs text-gray-400 uppercase tracking-wider font-bold mb-2">Barang
                                        Dipinjam</p>
                                    <ul class="space-y-1">
                                        @foreach ($item->barangs->take(2) as $barang)
                                            <li class="flex items-center text-gray-800 text-sm">
                                                <span class="w-1.5 h-1.5 rounded-full bg-primary mr-2"></span>
                                                <span class="font-semibold truncate">{{ $barang->nama_barang }}</span>
                                            </li>
                                        @endforeach
                                        @if ($item->barangs->count() > 2)
                                            <li class="text-xs text-gray-500 pl-3.5">+{{ $item->barangs->count() - 2 }}
                                                barang lainnya...</li>
                                        @endif
                                    </ul>
                                </div>

                                <div
                                    class="flex flex-col sm:flex-row sm:items-center text-sm text-gray-600 bg-gray-50 rounded-lg p-3">
                                    <div class="flex items-center mb-2 sm:mb-0 sm:mr-4">
                                        <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <span>{{ \Carbon\Carbon::parse($item->waktu_pinjam)->format('d M, H:i') }}</span>
                                    </div>
                                    <div class="hidden sm:block text-gray-300 mx-2">➝</div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span
                                            class="{{ $isMendekatiBatas ? 'text-orange-600 font-bold animate-pulse' : '' }}">
                                            {{ \Carbon\Carbon::parse($waktuSelesai)->format('d M, H:i') }}
                                        </span>
                                    </div>
                                </div>

                                @if ($item->status == 'ditolak' && $item->alasan_penolakan)
                                    <div
                                        class="mt-3 p-3 bg-red-50 rounded-lg border border-red-100 text-sm text-red-800">
                                        <span class="font-bold block mb-1">⚠️ Alasan Penolakan:</span>
                                        {{ $item->alasan_penolakan }}
                                    </div>
                                @endif
                                <div
                                    class="mt-3 pt-3 border-t border-dashed border-gray-100 flex justify-between items-center">
                                    <span class="text-xs text-gray-400">Tap untuk detail lengkap</span>
                                    <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </div>
                            </div>


                            {{-- Footer Card (Aksi) --}}
                            @if (!in_array($item->status, ['dikembalikan', 'ditolak']) || !in_array($item->status, ['diajukan', 'ditolak']))
                                <div
                                    class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex flex-wrap gap-2 justify-end">
                                    {{-- Tombol Cetak Struk --}}
                                    @if (!in_array($item->status, ['diajukan', 'ditolak']))
                                        <div
                                            class="px-5 py-3 bg-gray-50 border-t border-gray-100 flex flex-wrap gap-2 justify-end">
                                        </div>
                                        {{-- TOMBOL STRUK (DIPERBAIKI) --}}
                                        <a href="{{ route('transaksi.cetak', $item->id) }}" target="_blank"
                                            onclick="event.stopPropagation()" {{-- PENTING: Mencegah modal terbuka --}}
                                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition">
                                            <svg class="w-4 h-4 mr-1 text-gray-500" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m8-4V3a1 1 0 00-1-1H8a1 1 0 00-1 1v10a1 1 0 001 1h8a1 1 0 001-1zM7 10a1 1 0 011-1h8a1 1 0 011 1v10H7V10z">
                                                </path>
                                            </svg>
                                            Struk
                                        </a>
                                    @endif

                                    @if ($item->status == 'diajukan' || $item->status == 'disetujui')
                                        <button wire:click="batalkanPermintaan({{ $item->id }})"
                                            wire:confirm="Anda yakin?"
                                            class="inline-flex items-center px-3 py-1.5 border border-red-300 shadow-sm text-xs font-medium rounded text-red-700 bg-white hover:bg-red-50 focus:outline-none">
                                            Batalkan
                                        </button>
                                    @endif

                                    @if ($item->status == 'dipinjam')
                                        <button wire:click="bukaModalPengembalian({{ $item->id }})"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent shadow-sm text-xs font-medium rounded text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none">
                                            Kembalikan
                                        </button>
                                    @endif

                                    @if ($isMendekatiBatas)
                                        <button wire:click="mintaPerpanjangan({{ $item->id }})"
                                            wire:confirm="Ajukan perpanjangan?"
                                            class="inline-flex items-center px-3 py-1.5 border border-transparent shadow-sm text-xs font-medium rounded text-white bg-orange-500 hover:bg-orange-600 focus:outline-none animate-bounce">
                                            Minta Perpanjangan
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-12 bg-white rounded-xl border border-gray-200 shadow-sm">
                            <div
                                class="mx-auto h-16 w-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">Belum ada riwayat</h3>
                            <p class="mt-1 text-sm text-gray-500">Mulai pinjam barang untuk melihat riwayat di sini.
                            </p>
                        </div>
                    @endforelse
                </div>
            @endif

            {{-- TAB KETERSEDIAAN --}}
            @if ($activeTab == 'ketersediaan')
                <div class="space-y-6">
                    {{-- Filter Header --}}
                    <div
                        class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between">
                        <h2 class="text-gray-800 font-bold text-sm md:text-base">Filter Ruangan:</h2>
                        <div class="relative w-48 md:w-64">
                            {{-- Pastikan wire:model.live ada di sini --}}
                            <select wire:model.live="filterRuangan"
                                class="block w-full pl-3 pr-10 py-2 text-sm border-gray-300 focus:outline-none focus:ring-primary focus:border-primary sm:text-sm rounded-lg bg-gray-50">
                                <option value="">Semua Ruangan</option>
                                {{-- Gunakan $semuaRuangan untuk isi dropdown --}}
                                @foreach ($semuaRuangan as $ruangan)
                                    <option value="{{ $ruangan->id }}">{{ $ruangan->nama_ruangan }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Loop Barang (Gunakan variabel $ruangansDenganBarang) --}}
                    @forelse($ruangansDenganBarang as $ruangan)
                        @if ($ruangan->barangs->isNotEmpty())
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                                <div class="px-6 py-4 border-b border-gray-50 bg-gray-50/50">
                                    <h3 class="font-bold text-gray-800 flex items-center">
                                        <span class="w-1.5 h-6 bg-primary rounded-full mr-3"></span>
                                        {{ $ruangan->nama_ruangan }}
                                    </h3>
                                </div>
                                <div class="divide-y divide-gray-100">
                                    @foreach ($ruangan->barangs as $barang)
                                        <div
                                            class="p-4 flex justify-between items-center hover:bg-gray-50 transition-colors">
                                            <div>
                                                <p class="font-semibold text-gray-800">{{ $barang->nama_barang }}</p>
                                                <p class="text-xs text-gray-500 mt-0.5">
                                                    {{ $barang->kategori->nama_kategori }}</p>
                                            </div>
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold bg-green-100 text-green-800">
                                                {{ $barang->jumlah_saat_ini }} Tersedia
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="text-center py-12">
                            <p class="text-gray-500">Tidak ada barang tersedia di ruangan yang dipilih.</p>
                        </div>
                    @endforelse
                </div>
            @endif
        </div>
    </div>

    {{-- 6. Modal Form Permintaan (Desain Ulang) --}}
    @if ($showRequestModal)
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-end md:items-center justify-center z-50 p-0 md:p-4"
            x-transition.opacity>
            <div class="bg-white w-full md:max-w-lg h-[90vh] md:h-auto md:max-h-[85vh] rounded-t-2xl md:rounded-2xl shadow-2xl flex flex-col"
                @click.away="$set('showRequestModal', false)">

                {{-- Header Modal --}}
                <div
                    class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-2xl">
                    <h3 class="text-lg font-bold text-gray-800">Form Peminjaman</h3>
                    <button wire:click="$set('showRequestModal', false)"
                        class="text-gray-400 hover:text-gray-600 p-1 rounded-full hover:bg-gray-200 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- Body Modal (Scrollable) --}}
                <div class="flex-1 overflow-y-auto p-6 space-y-6">
                    <form wire:submit.prevent="ajukanPeminjaman">

                        {{-- Search Barang --}}
                        <div class="relative z-20">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cari & Tambah Barang</label>
                            <input type="text" wire:model.live.debounce.300ms="searchBarang"
                                class="w-full border-gray-300 rounded-lg focus:ring-primary focus:border-primary text-sm py-2.5 pl-4"
                                placeholder="Ketik nama barang..." autocomplete="off">

                            @if (!empty($barangDitemukan))
                                <div
                                    class="absolute top-full left-0 w-full mt-1 bg-white rounded-lg shadow-xl border border-gray-100 max-h-60 overflow-y-auto z-30">
                                    <ul class="py-1 text-sm">
                                        @forelse($barangDitemukan as $barang)
                                            @if ($barang->jumlah_saat_ini > 0)
                                                <li wire:click="tambahKeKeranjang({{ $barang->id }}, '{{ addslashes($barang->nama_barang) }}', '{{ $barang->ruangan->nama_ruangan }}')"
                                                    class="px-4 py-3 cursor-pointer hover:bg-gray-50 border-b border-gray-50 last:border-0 flex justify-between items-center group transition-colors">
                                                    <div>
                                                        <span
                                                            class="font-semibold text-gray-800 block group-hover:text-primary">{{ $barang->nama_barang }}</span>
                                                        <span class="text-xs text-gray-500">Lokasi:
                                                            {{ $barang->ruangan->nama_ruangan }}</span>
                                                    </div>
                                                    <span
                                                        class="bg-green-100 text-green-800 text-xs px-2 py-0.5 rounded font-bold">{{ $barang->jumlah_saat_ini }}
                                                        Stok</span>
                                                </li>
                                            @else
                                                <li
                                                    class="px-4 py-3 text-gray-400 bg-gray-50 flex justify-between cursor-not-allowed">
                                                    <span>{{ $barang->nama_barang }}</span>
                                                    <span class="text-xs font-bold text-red-400">Habis</span>
                                                </li>
                                            @endif
                                        @empty
                                            <li class="px-4 py-3 text-gray-500 text-center">Barang tidak ditemukan.
                                            </li>
                                        @endforelse
                                    </ul>
                                </div>
                            @endif
                        </div>

                        {{-- Keranjang --}}
                        @if (!empty($keranjang))
                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Barang Dipilih
                                    ({{ count($keranjang) }})</p>
                                <div class="space-y-3 max-h-40 overflow-y-auto pr-1">
                                    @foreach ($keranjang as $index => $item)
                                        <div
                                            class="flex items-center justify-between bg-white p-3 rounded-lg shadow-sm border border-gray-100">
                                            <div class="flex-1 min-w-0 mr-2">
                                                <p class="text-sm font-bold text-gray-800 truncate">
                                                    {{ $item['nama'] }}</p>
                                                <p class="text-xs text-gray-500 truncate">{{ $item['asal'] }}</p>
                                            </div>
                                            <div class="flex items-center bg-gray-100 rounded-lg p-1">
                                                <button type="button"
                                                    wire:click="decrementKuantitas({{ $index }})"
                                                    class="w-6 h-6 flex items-center justify-center rounded bg-white text-gray-600 shadow-sm hover:text-primary font-bold">-</button>
                                                <span
                                                    class="w-8 text-center text-xs font-bold text-gray-800">{{ $item['kuantitas'] }}</span>
                                                <button type="button"
                                                    wire:click="incrementKuantitas({{ $index }})"
                                                    class="w-6 h-6 flex items-center justify-center rounded bg-white text-gray-600 shadow-sm hover:text-primary font-bold">+</button>
                                            </div>
                                            <button type="button"
                                                wire:click="hapusDariKeranjang({{ $index }})"
                                                class="ml-3 text-red-400 hover:text-red-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        @error('keranjang')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror

                        {{-- Ruang & Waktu --}}
                        <div class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi Penggunaan</label>
                                <select wire:model="ruang_pemakaian"
                                    class="w-full border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary">
                                    <option value="">Pilih Ruangan...</option>
                                    @foreach ($ruangans as $ruangan)
                                        @php $isAsal = isset($this->asalRuangan[$ruangan->nama_ruangan]); @endphp
                                        <option value="{{ $ruangan->nama_ruangan }}" {{ $isAsal ? 'disabled' : '' }}
                                            class="{{ $isAsal ? 'text-gray-300' : '' }}">
                                            {{ $ruangan->nama_ruangan }} {{ $isAsal ? '(Asal Barang)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('ruang_pemakaian')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Pinjam</label>
                                    <input type="datetime-local" wire:model="waktu_pinjam"
                                        class="w-full border-gray-300 rounded-lg text-xs md:text-sm focus:ring-primary focus:border-primary">
                                    @error('waktu_pinjam')
                                        <span class="text-red-500 text-xs block">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Kembali</label>
                                    <input type="datetime-local" wire:model="waktu_kembali"
                                        class="w-full border-gray-300 rounded-lg text-xs md:text-sm focus:ring-primary focus:border-primary">
                                    @error('waktu_kembali')
                                        <span class="text-red-500 text-xs block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Footer Modal (Tombol) --}}
                        <div class="mt-8 pt-4 border-t border-gray-100 flex gap-3">
                            <button type="button" wire:click="$set('showRequestModal', false)"
                                class="flex-1 py-2.5 bg-gray-100 text-gray-700 rounded-lg font-semibold hover:bg-gray-200 transition">
                                Batal
                            </button>
                            <button type="submit"
                                class="flex-1 py-2.5 bg-primary text-white rounded-lg font-semibold shadow-md hover:bg-purple-800 transition transform active:scale-95">
                                Ajukan Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Pengembalian & Modal Lain (Gunakan struktur serupa jika ada) --}}
    @if ($showReturnModal && $returnTransaksi)
        {{-- ... (Kode modal pengembalian dengan style serupa modal di atas) ... --}}
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-2">Pengembalian Barang</h3>
                <p class="text-sm text-gray-500 mb-4">Cek kondisi barang sebelum dikembalikan.</p>

                <div class="space-y-3 mb-6 max-h-60 overflow-y-auto">
                    @foreach ($returnTransaksi->barangs as $barang)
                        <div
                            class="flex items-center justify-between bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <span class="text-sm font-medium text-gray-800">{{ $barang->nama_barang }}</span>
                            <div class="flex items-center">
                                <label class="text-xs text-gray-500 mr-2">Rusak:</label>
                                <input type="number" wire:model="kerusakanDilaporkan.{{ $barang->id }}"
                                    class="w-16 border-gray-300 rounded text-center text-sm p-1" min="0"
                                    max="{{ $barang->pivot->kuantitas }}">
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex gap-3">
                    <button wire:click="$set('showReturnModal', false)"
                        class="flex-1 py-2 bg-gray-100 text-gray-700 rounded-lg font-semibold hover:bg-gray-200">Batal</button>
                    <button wire:click="ajukanPengembalian"
                        class="flex-1 py-2 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 shadow">Kirim
                        Laporan</button>
                </div>
            </div>
        </div>
    @endif

    {{-- MODAL DETAIL TRANSAKSI (BOTTOM SHEET UI) --}}
    @if ($showDetailModal && $detailTransaksi)
        <div class="fixed inset-0 mb-3 z-[60] flex items-end md:items-center justify-center" role="dialog">

            {{-- Backdrop Gelap (Klik tutup) --}}
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" wire:click="closeDetail"
                x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            </div>

            {{-- Kontainer Modal --}}
            <div class="bg-white w-full md:max-w-lg rounded-t-[2rem] md:rounded-2xl shadow-2xl overflow-hidden transform transition-all max-h-[90vh] flex flex-col relative z-10"
                x-transition:enter="transform transition ease-out duration-300"
                x-transition:enter-start="translate-y-full md:translate-y-10 md:opacity-0"
                x-transition:enter-end="translate-y-0 md:translate-y-0 md:opacity-100"
                x-transition:leave="transform transition ease-in duration-200"
                x-transition:leave-start="translate-y-0 md:translate-y-0 md:opacity-100"
                x-transition:leave-end="translate-y-full md:translate-y-10 md:opacity-0">

                {{-- Handle Bar (Hanya Mobile) --}}
                <div class="md:hidden pt-3 pb-1 flex justify-center w-full bg-white" wire:click="closeDetail">
                    <div class="w-12 h-1.5 bg-gray-300 rounded-full"></div>
                </div>

                {{-- Header Modal --}}
                <div
                    class="px-6 py-4 border-b border-gray-100 flex justify-between items-start bg-white sticky top-0 z-20">
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Detail Transaksi</p>
                        <h3 class="text-xl font-bold text-gray-800">#TRX-{{ $detailTransaksi->id }}</h3>
                    </div>
                    <button wire:click="closeDetail"
                        class="p-2 bg-gray-100 rounded-full text-gray-500 hover:bg-gray-200 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                {{-- Body Modal (Scrollable) --}}
                <div class="p-6 overflow-y-auto flex-1 space-y-6 bg-gray-50/50">

                    {{-- 1. Status Banner --}}
                    @php
                        $statusDetail = match ($detailTransaksi->status) {
                            'disetujui' => [
                                'color' => 'bg-cyan-100 text-cyan-800 border-cyan-200',
                                'icon' => 'check-circle',
                            ],
                            'dipinjam' => ['color' => 'bg-blue-100 text-blue-800 border-blue-200', 'icon' => 'clock'],
                            'dikembalikan' => [
                                'color' => 'bg-green-100 text-green-800 border-green-200',
                                'icon' => 'badge-check',
                            ],
                            'ditolak' => ['color' => 'bg-red-100 text-red-800 border-red-200', 'icon' => 'x-circle'],
                            default => [
                                'color' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                'icon' => 'exclamation-circle',
                            ],
                        };
                    @endphp
                    <div class="flex items-center p-4 rounded-xl border {{ $statusDetail['color'] }}">
                        {{-- Ikon dinamis berdasarkan status bisa ditambahkan di sini --}}
                        <div class="flex-1">
                            <p class="text-xs opacity-70 font-bold uppercase">Status Saat Ini</p>
                            <p class="text-lg font-bold">
                                {{ ucfirst(str_replace('-', ' ', $detailTransaksi->status)) }}</p>
                        </div>
                    </div>

                    {{-- 2. Informasi Utama (Grid) --}}
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Lokasi Pemakaian --}}
                        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                            <div class="flex items-center gap-2 mb-2 text-purple-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="text-xs font-bold uppercase">Dipakai Di</span>
                            </div>
                            <p class="font-semibold text-gray-800">{{ $detailTransaksi->ruang_pemakaian }}</p>
                        </div>

                        {{-- Admin Penanggung Jawab --}}
                        <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm">
                            <div class="flex items-center gap-2 mb-2 text-purple-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span class="text-xs font-bold uppercase">Admin</span>
                            </div>
                            <p class="font-semibold text-gray-800">{{ $detailTransaksi->user->name ?? 'Menunggu...' }}
                            </p>
                        </div>
                    </div>

                    {{-- 3. Timeline Waktu --}}
                    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm relative overflow-hidden">
                        <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-100 my-4"></div>

                        <div class="relative flex items-start mb-4">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-1.5 mr-3 relative z-10 ring-4 ring-white">
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold">Waktu Pinjam</p>
                                <p class="text-sm font-semibold text-gray-800">
                                    {{ \Carbon\Carbon::parse($detailTransaksi->waktu_pinjam)->format('d F Y, H:i') }}
                                </p>
                            </div>
                        </div>

                        <div class="relative flex items-start">
                            @php
                                $isLate =
                                    $detailTransaksi->status == 'dipinjam' &&
                                    \Carbon\Carbon::parse($detailTransaksi->waktu_kembali)->isPast();
                                $dotColor = $isLate
                                    ? 'bg-red-500'
                                    : ($detailTransaksi->waktu_pengembalian_aktual
                                        ? 'bg-green-500'
                                        : 'bg-gray-300');
                            @endphp
                            <div
                                class="w-2 h-2 {{ $dotColor }} rounded-full mt-1.5 mr-3 relative z-10 ring-4 ring-white">
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 uppercase font-bold">Waktu Kembali</p>
                                @if ($detailTransaksi->waktu_pengembalian_aktual)
                                    <p class="text-sm font-semibold text-green-700">
                                        {{ \Carbon\Carbon::parse($detailTransaksi->waktu_pengembalian_aktual)->format('d F Y, H:i') }}
                                        (Aktual)
                                    </p>
                                @else
                                    <p class="text-sm font-semibold text-gray-800">
                                        {{ \Carbon\Carbon::parse($detailTransaksi->waktu_kembali)->format('d F Y, H:i') }}
                                        (Rencana)
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 4. Daftar Barang (Detail Asal) --}}
                    <div>
                        <h4 class="text-sm font-bold text-gray-700 mb-3 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-primary" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            Detail Barang
                        </h4>
                        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                            <div class="divide-y divide-gray-50">
                                @foreach ($detailTransaksi->barangs as $barang)
                                    <div class="p-4 hover:bg-gray-50 transition-colors">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="font-bold text-gray-800 text-sm">{{ $barang->nama_barang }}
                                                </p>
                                                <p class="text-xs text-gray-500 mt-0.5 flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                                        </path>
                                                    </svg>
                                                    Asal: {{ $barang->ruangan->nama_ruangan ?? 'Gudang' }}
                                                    </span>
                                            </div>
                                            <span
                                                class="bg-primary/10 text-primary text-xs font-bold px-2 py-1 rounded-lg">
                                                {{ $barang->pivot->kuantitas }} Unit
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- 5. Alasan Penolakan (Jika Ada) --}}
                    @if ($detailTransaksi->status == 'ditolak' && $detailTransaksi->alasan_penolakan)
                        <div class="bg-red-50 p-4 rounded-xl border border-red-100">
                            <p class="text-xs font-bold text-red-500 uppercase mb-1">Catatan Penolakan</p>
                            <p class="text-sm text-red-700">{{ $detailTransaksi->alasan_penolakan }}</p>
                        </div>
                    @endif

                </div>


                {{-- Footer Modal (Tombol Aksi) --}}
                <div class="p-4 bg-white border-t border-gray-100 sticky bottom-0 z-20 flex flex-col gap-3">

                    {{-- 1. Tombol Cetak Struk (Selalu muncul jika status valid) --}}
                    @if (!in_array($detailTransaksi->status, ['diajukan', 'ditolak', 'menunggu-konfirmasi']))
                        <a href="{{ route('siswa.transaksi.cetak', $detailTransaksi->id) }}" target="_blank"
                            class="w-full flex items-center justify-center py-3 bg-gray-100 text-gray-700 rounded-xl font-bold text-sm hover:bg-gray-200 transition border border-gray-200">
                            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m8-4V3a1 1 0 00-1-1H8a1 1 0 00-1 1v10a1 1 0 001 1h8a1 1 0 001-1zM7 10a1 1 0 011-1h8a1 1 0 011 1v10H7V10z">
                                </path>
                            </svg>
                            Cetak / Download Struk PDF
                        </a>
                    @endif

                    {{-- 2. Tombol Aksi Lainnya (Horizontal) --}}
                    <div class="flex gap-3">
                        {{-- Tombol Tutup Modal --}}
                        <button wire:click="closeDetail"
                            class="flex-1 py-3 bg-white border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition">
                            Tutup
                        </button>

                        {{-- Aksi Khusus --}}
                        @if ($detailTransaksi->status == 'dipinjam')
                            <button wire:click="bukaModalPengembalian({{ $detailTransaksi->id }})"
                                class="flex-1 py-3 bg-primary text-white rounded-xl font-bold hover:bg-purple-800 transition shadow-lg shadow-purple-200">
                                Ajukan Kembali
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
